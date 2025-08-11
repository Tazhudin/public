<?php

namespace Dev05\Classes\Changelog\Release;
use Dev05\Classes\Changelog\Interfaces\IStorage;

final class Storage implements IStorage
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
     * @behavior add record in release table
     * @param int $data
     * @return 
     * @throws Exception
     */
    public function add(array $data) {
        if (empty($data)) {
            throw new Exception('Invalid data');
        }
        $res = $this->hlBlock::add($data);
        return $res;
    }
    
    /**
     * @behavior delete record in release by id
     * @param int $id
     * @return 
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
     * @behavior update record in release by id
     * @param int $id, array $data
     * @return 
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
