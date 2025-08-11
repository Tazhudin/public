<?php

namespace Dev05\Classes\Changelog\Feature;

final class Storage //implements Storage
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
     * @behavior add record in feature
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function add($data) {
        if (empty($data)) {
            throw new Exception('Invalid data');
        }
        $res = $this->hlBlock::add($data);
        return $res;
    }
    
    /**
     * @behavior delete record in feature by id
     * @param int $id
     * @return array
     * @throws Exception
     */
    public function delete(int $id)
    {
        if ($id < 0) {
            throw new Exception('Invalid id');
        }
        
        $res = $this->hlBlock::delete($id);
        return $res;
    }

    /**
     * @behavior update feature record 
     * @param int $id array $data
     * @return array
     * @throws Exception
     */
    public function update(int $id, array $data)
    {
        if ($id < 0 || empty($data)) {
            throw new Exception('Invalid id');
        }
        $res = $this->hlBlock::update($id, $data);
        return $res;
    }

}
