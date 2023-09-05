<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/base/router.php
 *
 * Шукає шлях до контроллеру і створює об'єкт
 */
 
class Router extends Loader {
        
    function __construct($req = null)
    {
        if($req === null)
            exit('Router has NULL request');

        $this->request = $req;

        parent::__construct();
    }
    
    /**
     * Шукаємо шлях
     */
    function init($skip_ip_statistic = [])
    {
        $parts = $this->data->url();
        $this->page->amp_view = $this->data->use_amp();

        $alias_uri = $parts[0];
        $method = $parts[1] ?? 'index';
        $data = $parts[2] ?? null;
        $mode = '';
        
        if($alias_uri == 'admin')
        {
            if (!$this->user->auth())
                parent::redirect('login?redirect=' . $this->request);

            $alias_uri = $parts[1] ?? 'admin';
            $mode = 'admin';
            if($this->user->can($alias_uri))
            {
                if(LANGUAGE && LANGUAGE != ALL_LANGUAGES[0])
                    parent::redirect(SERVER_URL.$this->request, false);

                $method = $parts[2] ?? 'index';
                $data = $parts[3] ?? null;
            }
            else 
            {
                $alias_uri = 'admin';
                $method = 'page_403';
                $data = null;
            }        
        }
        else if ($alias_uri == 'api')
        {
            $alias_uri = $parts[1] ?? 'api';
            $method = $parts[2] ?? 'index';
            $data = $parts[3] ?? null;
            $mode = 'api';
        }
        /*
        else if(empty($_POST))
        {
        	if(!$this->user->can('admin'))
        		if ($ip = $this->user->ip_address())
        			if (empty($skip_ip_statistic) || !in_array($ip, $skip_ip_statistic))
        			{
        				parent::model('wl_statistic_model');
        				$this->wl_statistic_model->set_views();
        			}

        	parent::model('wl_cache_model');
        	if($this->wl_cache_model->init($this->data->url(true)))
        	{
        		if(isset($_SESSION['statistic']) && $_SESSION['statistic']->get_to_page)
        			$this->wl_statistic_model->set_page($this->wl_cache_model->wl_sitemap);

        		if(count($_GET) == 1) // only _GET[request] from .htaccess
        			$this->wl_cache_model->get($alias_uri);
        	}
        }
        */

        $alias_obj = parent::function_in_alias($alias_uri, $method, $data, $mode, true);

        $_SESSION['_POST'] = $_SESSION['_GET'] = NULL;
        /*
        if(empty($_POST) && $mode == '' && isset($this->wl_cache_model) && is_object($this->wl_cache_model))
        {
            if($this->wl_cache_model->wl_sitemap == false)
            {
                parent::library('sitemap');
                $this->wl_cache_model->wl_sitemap = $this->sitemap->add(['alias_id' => $alias_obj->id]);
                
                if(isset($_SESSION['statistic']) && $_SESSION['statistic']->get_to_page)
                    $this->wl_statistic_model->set_page($this->wl_cache_model->wl_sitemap);

                $this->wl_cache_model->set($alias_obj);
            }
            else
            {
                $this->wl_cache_model->wl_sitemap->alias_id = $alias_obj->id;
                $this->wl_cache_model->wl_sitemap->content_id = $this->page->id;
                $this->wl_cache_model->wl_sitemap->code = $this->page->code;

                if(isset($_SESSION['statistic']) && $_SESSION['statistic']->get_to_page)
                    $this->wl_statistic_model->set_page($this->wl_cache_model->wl_sitemap);

                $this->wl_cache_model->set($alias_obj);
            }
        }
        */
    }
    
}