<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/controller.php
 *
 * Всі контроллери успадковують цей клас
 */

class Controller extends Loader {
	
	public $load;
	
	/**
	 * Визиваємо батьківський конструктор та копіюємо ідентифікатор на обєкт
    * це потрібно для надання логіки. Відтак для завантаження скажімо бібліотеки
    * ми не пишемо $this->library(library_name), а пишемо $this->load->library(library_name)
	*/
	function __construct($alias, $init_page = false)
    {
        $this->load = $this;
        $this->alias = $alias;

        parent::__construct();
        
        if($init_page)
        {
            $actions = $this->cache->get('__page_before_init', 'wl_aliases');
            if($actions === NULL)
            {
                $actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1_id' => 0, 'type' => '__page_before_init'));
                $this->cache->add('__page_before_init', $actions, 'wl_aliases');
            }
            if($actions)
                foreach ($actions as $action) {
                    $this->function_in_alias($action->alias2_id, '__page_before_init');
                }
        }
	}

    public function __get($key)
    {
        if($key == '__')
            return $this->alias->options;

        if(substr($key, 0, 2) == '__' && strlen($key) > 2)
        {
            $key = substr($key, 2);
            return $this->alias->options[$key] ?? null;
        }

        $key = explode('__', $key);
        if($key[0] == 'o' && !empty($key[1]))
        {
            $key = array_shift($key);
            $key = implode('__', $key);
            return $this->alias->options[$key] ?? null;
        }
        if($key[0] == 'ao' && !empty($key[1]))
        {
            $key = array_shift($key);
            $key = implode('__', $key);
            return $this->alias->admin_options[$key] ?? null;
        }
        return null;
    }
	
	/**
	 * Викликаємо батьківський метод з ідентифікатором на обєкт
	 *
	 * @params $class ім'я класу
	 * @params $var завжди не задана(null)
	 */
	// public function library($classname, $var = null)
    // {
	// 	parent::library($classname, $this);
	// }

    public function get__wl_cooperation($function_name = null)
    {
        if(empty($function_name))
            return $this->alias->wl_aliases_cooperation;

        if(isset($this->alias->wl_aliases_cooperation[$function_name]))
            return $this->alias->wl_aliases_cooperation[$function_name];

        return false;
    }

    public function init__wl_cooperation($function_name = false, $data = NULL)
    {
        if(empty($function_name))
            return NULL;

        if(isset($this->alias->wl_aliases_cooperation[$function_name]))
        {
            foreach ($this->alias->wl_aliases_cooperation[$function_name] as $alias_id) {
                $data = $this->function_in_alias($alias_id, $function_name, $data);
            }
            return $data;
        }

        return false;
    }
	
}

?>