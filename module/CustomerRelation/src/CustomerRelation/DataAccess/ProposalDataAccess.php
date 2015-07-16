<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 4/28/2015
 * Time: 1:36 PM
 */

namespace CustomerRelation\DataAccess;

use Application\Service\SundewTableGateway;
use CustomerRelation\Entity\Proposal;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class ProposalDataAccess extends SundewTableGateway
{
    protected $staffId;
    public function __construct(Adapter $dbAdapter,$staffId)
    {
        $this->staffId=$staffId;
        $this->table="tbl_cr_proposal";
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(),new Proposal());
        $this->initialize();
    }

    public function fetchAll($paginated = false, $filter ='', $orderBy= 'proposalDate', $order='ASC')
    {
        $view = 'vw_cr_proposal';
        if($paginated){
           return $this->paginate($filter, $orderBy, $order, $view);
        }
        $proposalView=new TableGateway($view, $this->adapter);
        return $proposalView->select(array('proposalBy'=>$this->staffId));
    }
    public function getProposal($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('proposalId'=>$id,'proposalBy'=>$this->staffId));
        return $rowset->current();
    }
    public function saveProposal(Proposal $proposal)
    {
        $id=$proposal->getProposalId();
        $data=$proposal->getArrayCopy();
        $data['proposalBy']=$this->staffId;
        if(is_array($proposal->getProposalFile())){
            $data['proposalFile']=$proposal->getProposalFile()['tmp_name'];
        }
        if($id > 0){
            $this->update($data, array('proposalId'=>$id));
        }else{
            unset($data['proposalId']);
            $this->insert($data);
        }
        if(!$proposal->getProposalId())
        {
            $proposal->setProposalId($this->getLastInsertValue());
        }
        return $proposal;
    }
    public function deleteProposal($id)
    {
        $this->delete(array('proposalId'=>(int)$id));
    }
}