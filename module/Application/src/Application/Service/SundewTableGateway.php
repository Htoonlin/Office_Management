<?php
/**
 * Created by PhpStorm.
 * User: NyanTun
 * Date: 7/16/2015
 * Time: 3:39 PM
 */

namespace Application\Service;

use Zend\Db\Metadata\Metadata;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SundewTableGateway extends AbstractTableGateway
{
    protected $executeUser;
    /**
     * @param string $filter
     * @param string $orderBy
     * @param string $order
     * @param string $source
     * @return Paginator
     * @throws \Exception
     */
    public function paginate($filter, $orderBy, $order, $source = '')
    {
        try{
            $metadata = new Metadata($this->adapter);

            if(empty($source)){
                $source = $this->table;
            }
            $columns = $metadata->getColumnNames($source);
            $select = new Select($source);
            $select->order($orderBy . ' ' . $order);

            if(!empty($filter)){
                $query = "CONCAT_WS(' '," . implode(',', $columns) . ') LIKE ?';
                $where = new Where();
                $where->literal($query, '%' . $filter . '%');
                $select->where($where);
            }

            $paginatorAdapter = new DbSelect($select, $this->adapter);
            return new Paginator($paginatorAdapter);

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    /**
     * @param Select $select
     * @return Paginator
     */
    public function paginateWith(Select $select){
        $paginatorAdapter = new DbSelect($select, $this->adapter);
        return new Paginator($paginatorAdapter);
    }
}