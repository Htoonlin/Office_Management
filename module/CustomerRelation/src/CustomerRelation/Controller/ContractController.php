<?php
/**
 * Created by PhpStorm.
 * User: july
 * Date: 5/4/2015
 * Time: 2:35 PM
 */

namespace CustomerRelation\Controller;

use Account\DataAccess\CurrencyDataAccess;
use Application\Service\SundewExporting;
use CustomerRelation\DataAccess\ContractDataAccess;
use CustomerRelation\Entity\Contract;
use CustomerRelation\Helper\ContractHelper;
use CustomerRelation\DataAccess\CompanyDataAccess;
use CustomerRelation\DataAccess\ContactDataAccess;
use HumanResource\DataAccess\StaffDataAccess;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
class ContractController extends AbstractActionController
{
    private $staffId;
    private $staffName;
    private function contractTable()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!$this->staffId){
            $userId=$this->layout()->current_user->userId;
            $staffDataAccess=new StaffDataAccess($adapter);
            $staff=$staffDataAccess->getStaffByUser($userId);
            $this->staffId=boolval($staff)?$staff->getStaffId():0;
            $this->staffName=boolval($staff)?$staff->getStaffName():'';
        }
        return new ContractDataAccess($adapter,$this->staffId);
    }
    private function currencyCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CurrencyDataAccess($adapter);
        return $dataAccess->getComboData('currencyId','code');
    }
    private function companyCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new CompanyDataAccess($adapter);
        return $dataAccess->getComboData('companyId','name');
    }
    private function contactCombos()
    {
        $adapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $dataAccess=new ContactDataAccess($adapter);
        return $dataAccess->getComboData('contactId','name');
    }
    public function indexAction()
    {
        $page = (int)$this->params()->fromQuery('page',1);
        $sort = $this->params()->fromQuery('sort','contractDate');
        $sortBy = $this->params()->fromQuery('by','dsc');
        $filter = $this->params()->fromQuery('filter','');
        $pageSize = (int)$this->params()->fromQuery('size', 10);

        $paginator=$this->contractTable()->fetchAll(true,$filter,$sort,$sortBy);
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage($pageSize);
        return new ViewModel(array(
            'paginator'=>$paginator,
            'sort'=>$sort,
            'sortBy'=>$sortBy,
            'filter'=>$filter,
        ));
    }
    public function detailAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $helper = new ContractHelper();
        $form = $helper->getForm($this->currencyCombos(),$this->companyCombos(),$this->contactCombos());
        $contract = $this->contractTable()->getContractView($id);
        $isEdit = true;
        $hasFile = 'false';
        $currentFile = "";

        if(!$contract){
            $isEdit = false;
            $contract = new Contract();
        }else{
            $currentFile = $contract->getContractFile();
        }
        $form->bind($contract);
        $request = $this->getRequest();

        if($request->isPost()){
            $post_data = array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post_data);
            $form->setInputFilter($helper->getInputFilter(($isEdit ? $post_data['contractId'] : 0), $post_data['code']));
            if($form->isValid()){
                $file = $contract->getContractFile();

                if($post_data['contractFile'] ==  'false' && empty($file['name'])){
                    $contract->setContractFile(null);
                }else if($post_data['contractFile'] == 'true' && empty($file['name']) && $isEdit){
                    $contract->setContractFile($currentFile);
                }
                $this->contractTable()->saveContract($contract);
                $this->flashMessenger()->addMessage('Save successful');
                return $this->redirect()->toRoute('cr_contract');
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
            'contract'=>$contract,
            'isEdit' => $isEdit,
            'hasFile' => $hasFile,
            'staffName'=>$this->staffName,));
    }
    public function deleteAction()
    {

        $id = (int)$this->params()->fromRoute('id', 0);

        $contract = $this->contractTable()->getContract($id);
        if($contract){
            $this->contractTable()->deleteContract($id);
            $this->flashMessenger()->addMessage('Delete successful!');
        }

        return $this->redirect()->toRoute("cr_contract");
    }

    public function exportAction()
    {
        $export = new SundewExporting($this->contractTable()->fetchAll(false));
        $response = $this->getResponse();
        $filename = 'attachment; filename="Contract-' . date('Ymdhis') . '.xlsx"';

        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Type', 'application/ms-excel; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', $filename);
        $response->setContent($export->getExcel());

        return $response;
    }

    public function jsonDeleteAction()
    {
        $data=$this->params()->fromPost('chkId',array());
        $message="success";

        $db=$this->contractTable()->getAdapter();
        $conn=$db->getDriver()->getConnection();
        try{
            $conn->beginTransaction();
            foreach($data as $id){
                $this->contractTable()->deleteContract($id);
            }
            $conn->commit();
            $this->flashMessenger()->addMessage('Delete successful!');
        }catch (\Exception $ex){
            $conn->rollback();
            $message=$ex->getMessage();
        }
        return new JsonModel(array("message"=>$message));
    }
}