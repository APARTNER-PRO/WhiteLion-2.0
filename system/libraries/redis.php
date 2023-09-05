<?php

/**
 * v1	03.03.2021	created (moved) from db 2.9
 */
class redis
{
	
	private $connect;
	public $html_cache_in_redis = false;

	/*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg)
    {
        if(!empty($cfg['host']))
        {
            $this->connect = new \Redis();
            $this->connect->connect($cfg['host'], $cfg['port']);
            if(!empty($cfg['auth']))
                $this->connect->auth($cfg['auth']);
            if(!empty($cfg['html_cache_in_redis']))
                $this->html_cache_in_redis = true;
        }
    }

    public function set($key, $data)
    {
        if(is_object($this->connect))
        {
            $key = str_replace('/', DIRSEP, $key);
            $this->connect->set($key, $data);
            return true;
        }
        return false;
    }

    public function get($key)
    {
        if(is_object($this->connect))
        {
            $key = str_replace('/', DIRSEP, $key);
            if($this->connect->exists($key) > 0)
                return $this->connect->get($key);
        }
        return NULL;
    }

    public function del($key)
    {
        if(is_object($this->connect))
        {
            $key = str_replace('/', DIRSEP, $key);
            $this->connect->del($key);
            return true;
        }
        return false;
    }

    public function delByKey($key = '')
    {
        if(is_object($this->connect))
        {
            $key = str_replace('/', DIRSEP, $key);
            if($allKeys = $this->connect->keys($key.'*'))
            {
                $this->connect->del($allKeys);
                return true;
            }
        }
        return false;
    }

    public function ping()
    {
        if(is_object($this->connect))
            return $this->connect->ping();
        return false;
    }

    public function do($command = false, $value = NULL)
    {
        if(is_object($this->connect) && $command)
            return $this->connect->$command($value);
        return false;
    }

}