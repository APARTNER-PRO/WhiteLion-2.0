<?php

/**
 * v1	03.03.2021	created (moved) from db 2.9
 */
class cache
{
	public $data;
	public $alias;
	public $db;

	public $redis = false;
    public $html_cache_in_redis = false;

    /*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg)
    {
        if(!empty($cfg['redis_host']))
        {
            $this->redis = new Redis();
            $this->redis->connect($cfg['redis_host'], $cfg['redis_port']);
            if(!empty($cfg['redis_auth']))
                $this->redis->auth($cfg['redis_auth']);
            if(!empty($cfg['html_cache_in_redis']))
                $this->html_cache_in_redis = true;
        }
    }

    public function redis_set($key, $data)
    {
        if(is_object($this->redis))
        {
            $key = str_replace('/', DIRSEP, $key);
            $this->redis->set($key, $data);
            return true;
        }
        return false;
    }

    public function redis_get($key)
    {
        if(is_object($this->redis))
        {
            $key = str_replace('/', DIRSEP, $key);
            if($this->redis->exists($key) > 0)
                return $this->redis->get($key);
        }
        return NULL;
    }

    public function redis_del($key)
    {
        if(is_object($this->redis))
        {
            $key = str_replace('/', DIRSEP, $key);
            $this->redis->del($key);
            return true;
        }
        return false;
    }

    public function redis_delByKey($key = '')
    {
        if(is_object($this->redis))
        {
            $key = str_replace('/', DIRSEP, $key);
            if($allKeys = $this->redis->keys($key.'*'))
            {
                $this->redis->del($allKeys);
                return true;
            }
        }
        return false;
    }

    public function redis_ping()
    {
        if(is_object($this->redis))
            return $this->redis->ping();
        return false;
    }

    public function redis_do($command = false, $value = NULL)
    {
        if(is_object($this->redis) && $command)
            return $this->redis->$command($value);
        return false;
    }

    public function add($key, $data, $alias = false, $json = true)
    {
        if(!$alias)
            $alias = $this->alias->alias;

        if(LANGUAGE)
            $alias .= '_'.LANGUAGE;

        if(($json || $this->html_cache_in_redis) && is_object($this->redis))
        {
            $redis_key = $alias . DIRSEP . $key . '.json';
            if($json)
                $data = serialize($data);
            else
                $redis_key = $alias . DIRSEP . $key . '.html';
            $this->redis_set($redis_key, $data);
            return true;
        }

        $key = str_replace('/', DIRSEP, $key);
        $path = $alias . DIRSEP . $key . '.json';
        $dirs = explode(DIRSEP, $path);
        array_pop($dirs);
        $dirPath = CACHE_PATH;
        $dirPath = explode(DIRSEP, $dirPath);
        array_pop($dirPath);
        $dirPath = implode(DIRSEP, $dirPath);
        if(!is_dir($dirPath))
            mkdir($dirPath, 0755);
        foreach ($dirs as $dir) {
            $dirPath .= DIRSEP . $dir;
            if(!is_dir($dirPath))
                mkdir($dirPath, 0755);
        }
        if($json)
        {
            $path = CACHE_PATH . $alias . DIRSEP . $key . '.json';
            file_put_contents($path, serialize($data));
        }
        else
        {
            $path = CACHE_PATH . $alias . DIRSEP . $key . '.html';
            file_put_contents($path, $data);
        }
    }

    public function get($key, $alias = false, $json = true)
    {
        if(!$alias)
            $alias = $this->alias->alias;

        if(LANGUAGE)
            $alias .= '_'.LANGUAGE;

        if($json)
        {
            if(is_object($this->redis))
            {
                $data = $this->redis_get($alias . DIRSEP . $key . '.json');
                if($data === NULL)
                    return NULL;
                return unserialize($data);
            }

            $path = CACHE_PATH . $alias . DIRSEP . $key . '.json';
            if(file_exists($path))
                return unserialize(file_get_contents($path));
        }
        else
        {
            if($this->html_cache_in_redis && is_object($this->redis))
                return $this->redis_get($alias . DIRSEP . $key . '.html');

            $path = CACHE_PATH . $alias . DIRSEP . $key . '.html';
            if(file_exists($path))
                return file_get_contents($path);
        }
        return NULL;
    }

    public function delete($key, $alias = false, $json = true)
    {
        if($alias === false)
            $alias = $this->alias->alias;
        if(LANGUAGE)
        {
            foreach (ALL_LANGUAGES as $language) {
                $alias_lang = $alias .'_'.$language;

                if(($json || $this->html_cache_in_redis) && is_object($this->redis))
                {
                    if($json)
                        $this->redis_del($alias_lang . DIRSEP . $key . '.json');
                    else
                        $this->redis_del($alias_lang . DIRSEP . $key . '.html');
                }
                else
                {
                    $path = CACHE_PATH . $alias_lang . DIRSEP . $key . '.json';
                    if(!$json)
                        $path = CACHE_PATH . $alias_lang . DIRSEP . $key . '.html';
                    if(file_exists($path))
                        unlink($path);
                }
            }
            return true;
        }
        else
        {
            if(($json || $this->html_cache_in_redis) && is_object($this->redis))
            {
                if($json)
                    $this->redis_del($alias . DIRSEP . $key . '.json');
                else
                    $this->redis_del($alias . DIRSEP . $key . '.html');
            }
            else
            {
                $path = CACHE_PATH . $alias . DIRSEP . $key . '.json';
                if(!$json)
                    $path = CACHE_PATH . $alias_lang . DIRSEP . $key . '.html';
                if(file_exists($path))
                    return unlink($path);
            }
        }
        
        return false;
    }

    public function html_cache_clear($content = NULL, $alias = 0)
    {
        if($content === NULL)
            return false;
        
        if($GLOBALS['usePageCache'])
        {
            $alias_link = $this->alias->alias;
            if($alias != $this->alias->id)
            {
                if($a = $this->db->getAllDataById('wl_aliases', $alias))
                    $alias_link = $a->alias;
                else
                    return false;
            }

            $this->delete($this->get_html_key($content, $alias_link), 'html', false);
        }

        $this->alias->options['sitemap_lastedit'] = time();
        $this->db->updateRow('wl_options', array('value' => $_SESSION['option']->sitemap_lastedit), array('service' => 0, 'alias' => 0, 'name' => 'sitemap_lastedit'));
        return true;
    }

    public function get_html_key($content = 0, $alias_link = false)
    {
        if(!$alias_link)
            $alias_link = $this->alias->alias;
        $depth = 2;
        if($content < 0)
            $depth = 1;
        return $alias_link.DIRSEP.$this->getContentKey($alias_link.'_', $content, $depth);
    }

    public function getContentKey($pre = '', $content = 0, $depth = 1)
    {
        if($depth == 0 || $content == 0 || !is_numeric($content))
            return $pre.$content;
        if($content < 0)
        {
            $content *= -1;
            $pre .= '-';
        }
        $p_100 = ceil($content / 100) * 100;
        if($depth == 1)
            return $p_100.DIRSEP.$pre.$content;
        if($depth == 2)
        {
            $p_1000 = ceil($content / 1000) * 1000;
            return $p_1000.DIRSEP.$p_100.DIRSEP.$pre.$content;
        }
    }

}

?>