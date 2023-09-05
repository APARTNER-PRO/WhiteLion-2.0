<?php

class wl_alias_model
{
	private $skip_alias_init = ['app', 'logout', 'save', 'assets', 'media', 'js', 'style'];

    public function init($alias_url)
    {
		$alias_url = $this->db->sanitizeString($alias_url);

		if(in_array($alias_url, $this->skip_alias_init))
		{
			$alias = new stdClass();
			$alias->id = $alias->is_main = $alias->service_id = $alias->seo_robot = $alias->admin_sidebar = $alias->admin_order = 0;
			$alias->alias = $alias->name = $alias_url;
			$alias->admin_ico = $alias->service_name = $alias->service_table = '';
			$alias->url = SITE_URL . $alias_url;
			$alias->api_url = SERVER_URL . 'api/' . $alias_url;
			$alias->admin_url = SERVER_URL . 'admin/' . $alias_url;
			$alias->options = $alias->admin_options = $alias->wl_aliases_cooperation = $alias->imageReSizes = [];
			return $alias;
		}

		if($alias = $this->cache->get($alias_url, 'wl_aliases'))
			return $alias;

		$options_where['service_id'] = $options_where['alias_id'] = array(0);

		$this->db->select('wl_aliases as a', '*', $alias_url, 'alias')
					->join('wl_services', 'name as service_name, table as service_table', '#a.service_id')
					->join('wl_ntkd', 'name', $this->data->array_language(['alias_id' => '#a.id', 'content_id' => 0]));
		if ($alias = $this->db->get('single'))
		{
			$alias->name = html_entity_decode($alias->name, ENT_QUOTES);
			$options_where['alias_id'][] = $alias->id;
			$options_where['alias_id'][] = -$alias->id;
			if ($alias->service_id > 0)
				$options_where['service_id'][] = $alias->service_id;
		}
		else
		{
			$alias = new stdClass();
			$alias->id = $alias->is_main = $alias->service_id = $alias->seo_robot = $alias->admin_sidebar = $alias->admin_order = 0;
			$alias->alias = $alias->name = $alias_url;
			$alias->admin_ico = $alias->service_name = $alias->service_table = '';
		}

		$alias->url = SITE_URL . $alias_url;
		$alias->api_url = SERVER_URL . 'api/' . $alias_url;
		$alias->admin_url = SERVER_URL . 'admin/' . $alias_url;

		$alias->options = $alias->admin_options = $alias->wl_aliases_cooperation = $alias->imageReSizes = [];
		if ($options = $this->db->getAllDataByFieldInArray('wl_options', $options_where, 'service_id, alias_id'))
			foreach ($options as $opt) {
				if($opt->alias_id < 0 && $opt->name != 'sub-menu')
				{
					$alias->admin_options[$opt->name] = $opt->value;
					$alias->{'a__'.$opt->name} = $opt->value;
				}
				else if($opt->alias_id >= 0)
				{
					$alias->options[$opt->name] = $opt->value;
					$alias->{'__'.$opt->name} = $opt->value;
				}
			}

		$reSizes = ($alias->id) ? array(0, $alias->id) : 0;
		if ($sizes = $this->db->getAllDataByFieldInArray('wl_images_sizes', array('alias_id' => $reSizes, 'active' => 1, 'alias_id DESC')))
			foreach ($sizes as $size)
			{
				$key = $size->prefix;
				if (!$size->prefix)
					$key = 0;
				$alias->imageReSizes[$key] = $size;
			}
			
		if($alias->id)
		{
			if($cooperation = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', $alias->id, 'alias1_id'))
				foreach ($cooperation as $c) {
					if(!isset($alias->wl_aliases_cooperation[$c->type]))
						$alias->wl_aliases_cooperation[$c->type] = [$c->alias2_id];
					else
						$alias->wl_aliases_cooperation[$c->type][] = $c->alias2_id;
				}

			$this->cache->add($alias_url, $alias, 'wl_aliases');
		}
		
		return $alias;
    }

	public function getImageSizes($alias_id = 0)
	{
		if ($alias_id == 0 || $alias_id == $this->alias->id)
			return $this->alias->imageReSizes;

		if ($a = $this->db->getAllDataById('wl_aliases', $alias_id))
		{
			$alias = $this->init($a->alias);
			return $alias->imageReSizes;
		}
		
		return NULL;
	}

}