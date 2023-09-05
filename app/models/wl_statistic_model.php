<?php

class wl_statistic_model {

	public $page_id = 0;

	public function set_views()
	{	
		if($this->searchBot())
			return true;

		if(!isset($_SESSION['statistic']))
		{
			$_SESSION['statistic'] = new stdClass();
			$_SESSION['statistic']->lastRow = $this->db->getQuery("SELECT id, day FROM `wl_statistic_views` WHERE ID = (SELECT MAX(ID) FROM `wl_statistic_views`)");
			$_SESSION['statistic']->unique = !isset($_COOKIE['statisticViews']);
			$_SESSION['statistic']->pages = array();

			if($this->user->can('admin'))
				$_SESSION['statistic']->get_to_page = false;
			else
			{
				$wl_options_0 = $this->cache->get('__wl_options_0', 'wl_aliases');
	            if($wl_options_0 === NULL)
	            {
	            	$wl_options_0 = [];
	                if($list = $this->db->getAllDataByFieldInArray('wl_options', array('service_id' => 0, 'alias_id' => 0)))
	                	foreach($list as $option) {
	                		if($option->serialized)
	                			$wl_options_0[$option->name] = unserialize($option->value);
	                		else
	                			$wl_options_0[$option->name] = $option->value;
	                	}
	                $this->cache->add('__wl_options_0', $wl_options_0, 'wl_aliases');
	            }
	            $_SESSION['statistic']->get_to_page = $wl_options_0['statictic_set_page'] ?? false;
	        }
		}
		$today = strtotime('today');

		if($_SESSION['statistic']->lastRow && $today == $_SESSION['statistic']->lastRow->day)
		{
			if($_SESSION['statistic']->unique)
				$update = ' `unique` = `unique` + 1, `views` = `views` + 1 ';
			else
				$update = ' `views` = `views` + 1 ';
			
			$this->db->executeQuery("UPDATE `wl_statistic_views` SET {$update} WHERE `id` = {$_SESSION['statistic']->lastRow->id}");
		}
		else
		{
			$lastRow = new stdClass();
			$data['day'] = $lastRow->day = $today;
			$data['unique'] = 1;
			$data['views'] = 1;

			$lastRow->id = $this->db->insertRow('wl_statistic_views', $data);
			$_SESSION['statistic']->lastRow = $lastRow;
			$_SESSION['statistic']->unique = false;
		}

		if(!isset($_COOKIE['statisticViews']))
			setcookie('statisticViews', 'views', time() + 3600*24*31, '/');
	}

	public function set_page($page = NULL)
	{
		if(!in_array($page->link_sha1, $_SESSION['statistic']->pages))
		{
			$_SESSION['statistic']->pages[] = $page->link_sha1;
			$this->updatePageViews($page, true);
		}
		else
			$this->updatePageViews($page);
	}

	private function updatePageViews($page, $unique = false)
	{
		$where = $this->data->array_language(['alias_id' => $page->alias_id]);
		if($page->alias_id == 0)
			$where['content_id'] = $page->id;
		else
			$where['content_id'] = $page->content_id;
		$where['day'] = strtotime('today');

		$result = $this->db->getAllDataById('wl_statistic_pages', $where);
		if(!is_object($result))
		{
			$where['unique'] = 1;
			$where['views'] = 1;
			$this->page_id = $this->db->insertRow('wl_statistic_pages', $where);
		}
		else
		{
			$update = $unique ? ' `unique` = `unique` + 1, `views` = `views` + 1 ' : ' `views` = `views` + 1 ';
			$this->db->executeQuery("UPDATE `wl_statistic_pages` SET {$update} WHERE `id` = {$result->id}");
			$this->page_id = $result->id;
		}
	}

	public function searchBot()
	{
		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			$bots = array('Googlebot', 'Yahoo', 'Slurp', 'MSNBot', 'Teoma', 'Scooter', 'ia_archiver', 'Lycos', 'Yandex', 'StackRambler', 'Mail.Ru', 'Aport', 'WebAlta', 'bot', 'Google', 'YandexBot', 'Wget', 'AdsBot-Google-Mobile', 'adsbot');
			foreach ($bots as $bot) {
				if ( stristr($_SERVER['HTTP_USER_AGENT'], $bot) ) return true;
			}
		}
		return false;
	}
	
}

?>