<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 1/19/2015
 * Time: 4:08 PM
 */

namespace Application\DataAccess;

use Application\Entity\Role;
use Application\Service\SundewTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;

class RoleDataAccess extends SundewTableGateway
{
    public function __construct(Adapter $dbAdapter)
    {
        $this->table = 'tbl_role';
        $this->adapter = $dbAdapter;
        $this->resultSetPrototype = new HydratingResultSet(new ClassMethods(), new Role());
        $this->initialize();
    }

    public function fetchAll()
    {
        $results = $this->select();
        return $results->toArray();
    }

    public function getComboData($key, $value)
    {
        $results = $this->select();
        $selectData = array();
        foreach($results as $role){
            $data = $role->getArrayCopy();
            $selectData[$data[$key]] = $data[$value];
        }

        return $selectData;
    }

    public function getRole($id)
    {
        $id = (int)$id;
        $rowset = $this->select(array('roleId' => $id));
        $row = $rowset->current();
        if(!$row){
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getChildren($parentId = null, $parentName = "")
    {
        $results = $this->select(function (Select $select) use ($parentId){
            $select->where(array('parentId' => $parentId))->order(array('priority ASC'));
        });

        $resultList = array();
        foreach($results as $role)
        {
            $children = $this->getChildren($role->getRoleId(), $parentName);
            if(!empty($children)){
                $role->setChildren($children);
            }
            $resultList[] = $role;
        }
        return $resultList;
    }

    public function saveRole(Role $role)
    {
        $id = $role->getRoleId();
        $data = $role->getArrayCopy();

        if($id > 0){
            $this->update($data, array('roleId' => $id));
        }else{
            unset($data['roleId']);
            $this->insert($data);
        }
        if(!$role->getRoleId()){
            $role->setRoleId($this->getLastInsertValue());
        }
        return $role;
    }

    public function deleteRole($id)
    {
        $results = $this->select(array("parentId" => $id));
        foreach($results as $role){
            $this->deleteRole($role->getRoleId());
        }
        $this->delete(array('roleId' => (int)$id));
    }
}