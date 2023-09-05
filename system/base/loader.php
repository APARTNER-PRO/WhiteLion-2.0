<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/loader.php
 *
 * Завантажуємо сторонні класи, бібліотеки тощо...
 */
 
class Loader {

    public $alias;
    public $request;
    private $autoload; // бібліотеки, що доступні як у контролері так і у моделі
    
    /**
     * Якшо в конфігу задана секція "autoload" то завантажуємо ці бібліотеки.
     * Якщо виклик із сервісу, то передається назва сервісу
     */
    function __construct()
    {
        if($this->autoload = $this->config('autoload'))
            foreach ($this->autoload as $class) {
                $this->library($class);
            }
    }
    
    /**
     * Завантажуємо конфіг
     *
     * @params $key назва індексу масива
     *
     * @return значення
     */
    function config($key)
    {
        if($key == 'data')
            return $this->request;
        require APP_PATH.'config.php';
        if(array_key_exists($key, $config))
            return $config[$key];
        else
            return null;
    }

    function authorize()
    {
        $this->model('wl_user_model');
        $this->wl_user_model->authorize();
    }
    
    /**
     * Завантажуємо подання
     *
     * @params $view назва подання
     * @params $data параметри
     */	
    function view($view_file, $data = null, $setContentRobot = true)
    {
        if($data)
            foreach($data as $key => $value) {
                $$key = $value;
            }

        $this->init__wl_cooperation('__page_before_view');
        if(!empty($this->page->id) && $setContentRobot || $this->alias->seo_robot)
        {
            // unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
            $this->page->setContentRobot($data);
        }

        $view_path = APP_PATH.'views'.DIRSEP;
        if($this->page->amp_view)
            $view_path .= 'amp'.DIRSEP;

        if($this->alias->service_id)
        {
            if(isset($_SESSION['option']->uniqueDesign) && $_SESSION['option']->uniqueDesign)
                $view_path .= $this->alias->alias.DIRSEP.$view_file.'.php';
            else
            {
                if($this->page->amp_view)
                    $view_path = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.'amp'.DIRSEP.$view_file.'.php';
                else
                    $view_path = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.$view_file.'.php';
            }
        }
        else
            $view_path .= $view_file.'.php';
        if(file_exists($view_path))
            require $view_path;
    }
    
    /**
     * Завантажуємо подання головної розмітки (layout)
        *
     * @params $view_file назва подання
     * @params $data параметри
     */	
    function page_view($view_file = false, $data = null)
    {
        // unset($_SESSION['alias-cache'][$_SESSION['alias']->id]);
        if($data)
            foreach($data as $key => $value) {
                $$key = $value;
            }

        $this->init__wl_cooperation('__page_before_view');
        // if(!isset($this->alias->seo_robot) || $this->alias->seo_robot == true)
            $this->page->setContentRobot($data);
        
        $view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
        if($this->page->amp_view)
            $view_path = APP_PATH.'views'.DIRSEP.'amp'.DIRSEP.'page_view.php';
        if($this->alias->service_id && $view_file)
        {
            if(isset($_SESSION['option']->uniqueDesign) && $_SESSION['option']->uniqueDesign > 0 && $view_file)
            {
                if($this->page->amp_view)
                    $view_file = APP_PATH.'views'.DIRSEP.'amp'.DIRSEP.$_SESSION['alias']->alias.DIRSEP.$view_file;
                else
                    $view_file = APP_PATH.'views'.DIRSEP.$_SESSION['alias']->alias.DIRSEP.$view_file;
            }
            else
            {
                if($this->page->amp_view)
                    $view_file = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.'amp'.DIRSEP.$view_file;
                else
                    $view_file = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.$view_file;
            }
        }
        if(file_exists($view_path))
            require $view_path;
    }
    
    /**
     * Завантажуємо подання повідомлення з головною розміткою (layout)
     *
     * @params $data параметри
     */	
    function notify_view($data = null)
    {
        if($data)
            foreach($data as $key => $value) {
                $$key = $value;
            }
        $this->init__wl_cooperation('__page_before_view');
        $view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
        $view_file = 'notify_view';
        if(file_exists($view_path))
        {
            require $view_path;
            exit();
        }
    }

    function profile_view($sub_page = false, $data = null, $user_id = 0)
    {
        if(is_array($data))
            foreach($data as $key => $value) {
                $$key = $value;
            }
        if($sub_page)
            $sub_page .= '.php';
        if($this->alias->service_name)
            $sub_page = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.$sub_page;
        $view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
        $view_file = 'profile/index_view';
        if(empty($user))
        {
            $this->model('wl_user_model');
            $user = $this->wl_user_model->getInfo($user_id, false);
        }
        if(file_exists($view_path))
        {
            require $view_path;
            exit();
        }
    }

    function page_404($update_SiteMap = false)
    {
        // $this->library('page');
        $this->page->code = 404;
        // if($update_SiteMap)
        // {
        // 	$this->library('db');
        // 	if($_SESSION['alias']->content === NULL)
        // 	{
        // 		$page = $this->db->sitemap_add($_SESSION['alias']->content, $_SESSION['alias']->link, 404);
        // 		$referer = array();
        // 		$referer['sitemap'] = $page->id;
        // 		$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
        // 		$referer['date'] = time();
        // 		$this->db->insertRow('wl_sitemap_from', $referer);
        // 	}
        // 	else
        // 		$this->db->sitemap_update($_SESSION['alias']->content, 'code', 404);
        // }
        header('HTTP/1.0 404 Not Found');
        if($page = $this->cache->get('page_404', 'html', false))
        {
            echo $page;
            exit;
        }

        $view_path = APP_PATH.'views'.DIRSEP.'404_page.php';
        // $view_path = APP_PATH.'views'.DIRSEP.'page_view.php';
        // $view_file = '404_view';
        if(file_exists($view_path))
        {
            ob_start();
            require $view_path;
            $content = (string) ob_get_contents();
            $this->cache->add('page_404', $content, 'html', false);
            ob_end_flush();
            exit();
        }
    }

    /**
     * Завантажуємо подання розмітки панелі керування сайтом
        *
     * @params $view_file назва подання
     * @params $data параметри
     */	
    function admin_view($view_file = false, $data = null)
    {
        if(empty($this->page->title))
            $this->page->title = strip_tags($this->page->name);

        $view_path = APP_PATH.'views'.DIRSEP.'admin/admin_view.php';
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            if(!$view_file) return true;

            if($data)
                foreach($data as $key => $value) {
                    $$key = $value;
                }

            $view_path = APP_PATH.'views'.DIRSEP.'admin/__ajax_view.php';
        }

        if($this->alias->service_name && !in_array($view_file, ['403_view', '404_view']))
            $view_file = APP_PATH.'services'.DIRSEP.$this->alias->service_name.DIRSEP.'views'.DIRSEP.'admin'.DIRSEP.$view_file;

        if(file_exists($view_path))
        {
            require $view_path;
            if($view_file)
                $_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
            exit();
        }
    }
    
    /**
     * Завантажуємо моделі
     *
     * @params $model назва моделі
     */	
    function model($model, $model_path = '')
    {
        if(isset($this->$model) && is_object($this->$model))
            return true;
        $model_path = APP_PATH . $model_path . 'models' . DIRSEP . $model . '.php';
        if(file_exists($model_path))
        {
            require_once $model_path;
            $this->$model = new $model();
            if(!empty($this->$model->model_table))
                $this->$model->setModelTable($this->$model->model_table);
            if($this->autoload)
                foreach ($this->autoload as $obj) {
                    if(!empty($this->$obj) && is_object($this->$obj))
                        $this->$model->$obj = $this->$obj;
                }
        }
    }
    
    /**
     * Завантажуємо моделі
     *
     * @params $model назва моделі
     */	
    function smodel($model)
    {
        if($this->alias->service_name)
        {
            if(isset($this->$model) && is_object($this->$model))
                return true;
            $this->model($model, 'services' . DIRSEP . $this->alias->service_name . DIRSEP);
        }
        return false;
    }

    private $wl_aliases = null; // Array [$alias_id] => $alias_url
    public function function_in_alias($alias_url, $method = 'index', $data = array(), $mode = '', $init_page = false)
    {
        if(empty($alias_url))
            return null;

        if(is_numeric($alias_url))
        {
            if(is_null($this->wl_aliases))
            {
                $this->wl_aliases = $this->cache->get('__wl_aliases', 'wl_aliases');
                if (is_null($this->wl_aliases))
                {
                    $this->wl_aliases = [];
                    if($list = $this->db->select('wl_aliases', 'id, alias')->get('array'))
                        foreach($list as $a) {
                            $this->wl_aliases[$a->id] = $alias->alias;
                        }
                    $this->cache->add('__wl_aliases', $this->wl_aliases, 'wl_aliases');
                }
            }
            
            if(isset($this->wl_aliases[$alias_url]))
                $alias_url = $this->wl_aliases[$alias_url];
            else
                return null;
        }

        $alias = null;
        $registry = Registry::singleton();
        $class_name = 'alias__'.strtolower($alias_url);
        $alias_obj = $registry->get($class_name);

        if(is_null($alias_obj))
        {
            $this->model('wl_alias_model');
            $alias = $this->wl_alias_model->init($alias_url);

            $aliasController = $alias_url;
            if($alias->service_id)
            {
                $path = APP_PATH . 'services' . DIRSEP . $alias->service_name . DIRSEP . $alias->service_name;
                $aliasController = 'WL_SERVICE\\' . $alias->service_name;
                if(!empty($mode))
                {
                    $path .= '__' . $mode;
                    $aliasController .= '__' . $mode;
                }
                require_once $path . '.php';
            }

            $path = APP_PATH . 'controllers' . DIRSEP;
            if(!empty($mode))
                $path .= $mode . DIRSEP;
            if (is_file($path . $alias_url . '.php'))
            {
                require_once $path . $alias_url  . '.php';

                $aliasController = $alias_url;
                if(!empty($mode))
                    $aliasController .= '__' . $mode;
            }

            if (class_exists($aliasController))
            {
                $alias_obj = new $aliasController($alias, $init_page);
                $registry->set($class_name, $alias_obj);
            }
        }

        if(is_object($alias_obj))
        {
            if($init_page && !is_null($alias))
            {
                $this->alias = $alias;
                if(is_callable(array($alias_obj, '_remap')))
                    $alias_obj->_remap($method, $data);
                else if(is_callable(array($alias_obj, $method)))
                    $alias_obj->$method($data);
                return $alias;
            }
            else
            {
                if(is_callable(array($alias_obj, '_remap')))
                    return $alias_obj->_remap($method, $data);
                else if(is_callable(array($alias_obj, $method)))
                    return $alias_obj->$method($data);
            }
        }

        if($init_page)
            $this->page_404();

        return null;
    }

    /**
     * Завантажуємо бібліотеки
     * Створюємо об'єкти і зберігаємо в реєстрі
     *
     * @params $class назва класу/файла
     */
    // function library($class)
    function library($class, $data = null)
    {
        if(empty($class))
            return false;

        $class = strtolower($class);

        $registry = Registry::singleton();
        if ($registry->get($class) !== null)
        {
            $this->$class =	$registry->get($class);
            if (property_exists($this->$class, 'alias'))
                $this->$class->alias = $this->alias;
            return true;
        }

        $class_path = SYS_PATH . 'libraries' . DIRSEP . $class . '.php';
        if (file_exists($class_path))
        {
            require $class_path;
            if($data === null)
                $data = $this->config($class);
            $obj = new $class($data);
            if (is_object($obj))
            {
                $registry->set($class, $obj);
                $this->$class = $obj;

                foreach($this->autoload as $key) {
                    if (property_exists($class, $key))
                    {
                        if ($registry->get($key) !== null)
                        {
                            $this->$class->$key = $registry->get($key);
                        }
                        else if(isset($this->$key))
                            $this->$class->$key = $this->$key;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Здійснюємо перенаправлення на вказану адресу
     *
     * @params $link адреса перенаправлення. Якщо відсутня, то на сторінку звідки прийшов користувач
     * @params $use_SITE_URL чи використовувати префікс адреси сайту до адреси перенаправлення
     */
    function redirect($link = '', $use_SITE_URL = true)
    {
        $this->notify->setToSession();

        if($link == '' || $link[0] == '#')
        {
            if($_SERVER['HTTP_REFERER'])
            {
                $use_SITE_URL = false;
                $link = $_SERVER['HTTP_REFERER'] . $link;
                unset($_GET['request']);
                if(!empty($_GET))
                {
                    $get = [];
                    foreach ($_GET as $key => $value) {
                        $get[] = $key.'='.$value;
                    }
                    $link .= '?'.implode('&', $get);
                }
            }
            else
            {
                $link = SITE_URL;
                $use_SITE_URL = false;
            }
        }
        else if($link == '/' || $link == 'main')
        {
            $link = SITE_URL;
            $use_SITE_URL = false;
        }

        if($use_SITE_URL)
            $link = SITE_URL . $link;

        header ('HTTP/1.1 303 See Other');
        header("Location: {$link}");
        exit();
    }

    function json($value = '')
    {
        header('Content-type: application/json');
        echo json_encode($value);
        $_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
        exit();
    }

    function text($word = '', $alias = -1)
    {
        // $word = trim($word);
        // if($word != '' && (LANGUAGE || $alias >= 0))
        // {
        //     $this->model('wl_language_model');
        //     return $this->wl_language_model->get($word, $alias);
        // }
        return $word;
    }

    public function js($link='')
    {
        if(is_array($link))
            foreach ($link as $js) {
                if(!empty($js))
                    $this->page->js_load = $js;
            }
        else if(!empty($link))
            $this->page->js_load = $js;
    }

    public function js_init($link='')
    {
        if(is_array($link))
            foreach ($link as $js) {
                if(!empty($js))
                    $this->page->js_init = $js;
            }
        else if(!empty($link))
            $this->page->js_init = $js;
    }
    
}

?>