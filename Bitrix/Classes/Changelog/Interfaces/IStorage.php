<?php
namespace Dev05\Classes\Changelog\Interfaces;

interface IStorage
{
    public function getlist(array $params);
    public function add(array $data);
    public function delete(int $id);
    public function update(int $id, array $data);
}