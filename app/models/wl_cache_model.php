<?php 

class wl_cache_model extends Loader
{

	public $wl_sitemap = false;
	public $showTimeSiteGenerate = false;
	
	public function init($link)
	{
		if($this->wl_sitemap = $this->db->getAllDataById('wl_sitemap', sha1($link), 'link_sha1'))
		{
			$this->page->id = $this->wl_sitemap->content_id;
			$this->page->code = $this->wl_sitemap->code;
			return true;
		}
		return false;
	}

	public function get($alias_uri)
	{
		if(empty($alias_uri))
			return false;

		if(!WL_PAGE_CACHE && $this->wl_sitemap->code < 300)
			return true;

		$cache_key = $this->cache->get_html_key($this->wl_sitemap->content, $alias_uri);

		switch ($this->wl_sitemap->code) {
			case 200:
				if($page = $this->cache->get($cache_key, 'html', false))
				{
					echo $page;
                    if($this->showTimeSiteGenerate)
                    {
                    	$this->db->showTime();
						echo '<br><center>load from cache</center>';
                    }
                    exit();
                }
				break;

			case 201:
				if(!$this->user->auth())
				{
					if($page = $this->cache->get($cache_key, 'html', false))
					{
						echo $page;
	                    if($this->showTimeSiteGenerate)
	                    {
	                    	$this->db->showTime();
							echo '<br><center>load from cache</center>';
	                    }
	                    exit();
	                }
	            }
				break;
			
			case 301:
				$referer = array();
				$referer['sitemap_id'] = $this->wl_sitemap->id;
				$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
				$referer['date'] = time();
				$this->db->insertRow('wl_sitemap_from', $referer);

				if(empty($this->wl_sitemap->data))
					$this->wl_sitemap->data = '/';

				parent::redirect($this->wl_sitemap->data);

			case 404:
				$referer = array();
				$referer['sitemap'] = $this->wl_sitemap->id;
				$referer['from'] = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'direct link';
				$referer['date'] = time();
				$this->db->insertRow('wl_sitemap_from', $referer);
				
				parent::page_404(false);
				break;
		}

		if(WL_PAGE_CACHE && ($this->wl_sitemap->code == 200 || ($this->wl_sitemap->code == 201 && !$this->user->auth())))
			ob_start();
	}

	public function set($alias)
	{
		$cache = array();

		if($this->page->id !== NULL && $this->wl_sitemap->alias_id != $alias->id)
		{
			$cache['alias_id'] = $alias->id;
			$cache['content_id'] = $this->page->id;
		}

		if($this->page->code != $this->wl_sitemap->code)
			$cache['code'] = $this->page->code;

		if(WL_PAGE_CACHE && ($this->page->code == 200 || ($this->page->code == 201 && !$this->user->auth())) && count($_GET) == 1)
		{
			$content = (string) ob_get_contents();

			$key = $this->cache->get_html_key($this->page->id, $alias->alias);
			$this->cache->add($key, $content, 'html', false);

			ob_end_flush();
		}

		if(!empty($cache))
		{
			$cache['time'] = time();
			$this->db->updateRow('wl_sitemap', $cache, $this->wl_sitemap->id);
		}

		if($this->showTimeSiteGenerate)
			$this->db->showTime();
		exit;
	}

}

?>