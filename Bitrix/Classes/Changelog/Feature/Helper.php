<?php

namespace Dev05\Classes\Changelog\Feature;
use Dev05\Classes\HighLoadBlock;
use Bitrix\Main\Entity\ReferenceField;

use Bitrix\Highloadblock\HighloadBlockTable;

class Helper 
{
    use Entity;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        $this->setHlEntity($this->hlTableName);
    }

    
    
    /**
     * @behavior return array feature records
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getlist(array $params) {
        if (empty($params)) {
            throw new Exception('Invalid params');
        }
        $res = $this->hlBlock::getList($params);
        return $res;
    }
    
    /**
     * @behavior get all feature records
     * @param int $id
     * @return 
     * @throws 
     */
    public function getAll() {
        $dbRes = $this->hlBlock::getList(array(
            'select' => array('*')
        ));
        return $dbRes;
    }

    /**
     * @behavior get feature record 
     * @param int $id
     * @return 
     * @throws Exception
     */
    public  function getById( $id) {
        if ($id < 0) {
            throw new Exception('Invalid id');
        }
        $dbRes = $this->hlBlock::getList(array(
            'select' => array('*'),
            'filter' => array('ID' => $id)
        ));
        return $dbRes;
    } 

}

