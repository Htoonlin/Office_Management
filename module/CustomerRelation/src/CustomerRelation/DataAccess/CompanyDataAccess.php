<?php
/**
 * Created by PhpStorm.
 * User: Lwin
 * Date: 4/28/2015
 * Time: 1:39 PM
 */

namespace CustomerRelation\DataAccess;

use Application\Service\SundewTableGateway;
use CustomerRelation\Entity\Company;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Db\Sql\Where;

/**
 * Class CompanyDataAccess
 * @package CustomerRelation\DataAccess
 */
class CompanyDataAccess extends SundewTableGateway
{
    /**
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->table='tbl_cr_company';
        $this->adapter=$dbAdapter;
        $this->resultSetPrototype=new HydratingResultSet(new ClassMethods(), new Company());
        $this->initialize();
    }

    /**
     * @param bool $paginated
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @return \Zend\Db\ResultSet\ResultSet|Paginator
     * @throws \Exception
     */
    public function fetchAll($paginated=false, $filter='', $orderBy='name', $order='ASC')
    {
        if($paginated){
            return $this->paginate($filter, $orderBy, $order);
        }
        return $this->select();

    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function getComboData($key, $value)
    {
        $results=$this->select();
        $selectData=array();
        foreach($results as $company){
            $data=$company->getArrayCopy();
            $selectData[$data[$key]]=$data[$value];
        }
        return $selectData;
    }

    /**
     * @param $id
     * @return array|\ArrayObject|null
     */
    public function getCompany($id)
    {
        $id=(int)$id;
        $rowset=$this->select(array('companyId'=>$id));
        $row=$rowset->current();

        return $row;
    }

    /**
     * @param Company $company
     * @return Company
     */
    public function saveCompany(Company $company)
    {
        $id=$company->getCompanyId();
        $data=$company->getArrayCopy();


        if($id>0){
            $this->update($data, array('companyId'=>$id));
        }else{
            unset($data['companyId']);
            $this->insert($data);
        }
        if(!$company->getCompanyId()){
            $company->setCompanyId($this->getLastInsertValue());
        }
        return $company;
    }

    /**
     * @param $id
     */
    public function deleteCompany($id)
    {
        $this->delete(array('companyId'=>(int)$id));
    }
}