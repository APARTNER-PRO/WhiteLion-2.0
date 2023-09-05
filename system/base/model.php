<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * created by Ostap Matskiv for White Lion CMS 2.0
 * 1.0       17.02.2021     setModelTable(), add(), update(), getOne(), getAll(), prepareData()
             20.07.2021     getModelTable(), delete()
 */

class Model
{
    protected $table = null;

    public function setModelTable($table)
    {
        $this->table = (string) $table;
    }

    public function getModelTable()
    {
        return $this->table;
    }
    
    public function insert($data = null, $method = 'post')
    {
        if(empty($this->table) || empty($data))
            return false;

        return $this->db->insertRow($this->table, $this->prepareData($data, $method));
    }

    public function update($where, $data, $method = 'post')
    {
        if (empty($this->table) || empty($where) || empty($data))
            return false;

        return $this->db->updateRow($this->table, $this->prepareData($data, $method), $where);
    }

    public function delete($data = null, $method = 'post')
    {
        if(empty($this->table) || empty($data))
            return false;

        return $this->db->deleteRow($this->table, $this->prepareData($data, $method));
    }

    public function getOne($where = [], $method = 'post')
    {
        if (empty($this->table))
            return false;

        if(!empty($where) && !is_bool($where))
            return $this->db->getAllDataById($this->table, $this->prepareData($where, $method));
        
        return false;
    }

    public function getAll($where = [], $order_limit = '')
    {
        if (empty($this->table))
            return false;
            
        if(!empty($where))
            return $this->db->getAllDataByFieldInArray($this->table, $where, $order_limit);
        
        return $this->db->getAllData($this->table, $order_limit);
    }

    public function prepareData($input, $method = 'post')
    {
        if(is_numeric($input))
            return $input;

        if($method != 'get')
            $method = 'post';

        if(!is_array($input))
            $input = [$input];

        $output = [];
        foreach ($input as $key => $value) {
            if(is_numeric($key))
                $output[$value] = $this->data->$method($value);
            else
                $output[$key] = $value;
        }
        return $output;
    }

}


?>