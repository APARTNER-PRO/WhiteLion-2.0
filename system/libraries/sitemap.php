<?php

class sitemap {

    public $db;
    public $data;
    public $alias;
    public $page;

    public function add(array $in = [])
    {
        $sitemap = array();
        foreach(['link' => $this->data->url(true),
                 'alias_id' => $this->alias->id,
                 'content_id' => $this->page->id,
                 'code' => $this->page->code,
                 'data' => NULL,
                 'changefreq' => 'daily',
                 'priority' => 5
                ] as $key => $default) {
            $sitemap[$key] = $in[$key] ?? $default;
        }
        $sitemap['link_sha1'] = sha1($sitemap['link']);
        $sitemap['time'] = time();
        $sitemap['changefreq'] = (in_array($sitemap['changefreq'], array('always','hourly','daily','weekly','monthly','yearly','never'))) ? $sitemap['changefreq'] : 'daily';
        if($sitemap['priority'] < 1)
            $sitemap['priority'] *= 10;
        $sitemap['id'] = $this->db->insertRow('wl_sitemap', $sitemap);
        return (object) $sitemap;
    }

    // to: array or string
    public function redirect($to = '/')
    {
        if(empty($to))
            return false;

        if(is_string($to))
            $to = ['data' => $to];

        $to['code'] = 301;
        return $this->add($to);
    }

    public function update($key = 'link', $value = '', $content_id = NULL, $alias_id = 0)
    {
        $sitemap = $where = array();
        $where['alias_id'] = ($alias_id == 0) ? $this->alias->id : $alias_id;
        $where['content_id'] = is_null($content_id) ? $this->page->id : $content_id;
        if(is_array($key))
        {
            foreach ($key as $k => $v) {
                if($k == 'changefreq')
                {
                    if(in_array($v, array('always','hourly','daily','weekly','monthly','yearly','never')))
                        $sitemap['changefreq'] = $v;
                }
                elseif($k == 'priority')
                {
                    if(is_numeric($v) && $v >= 0)
                    {
                        $sitemap['priority'] = $v;
                        if($sitemap['priority'] < 1)
                            $sitemap['priority'] *= 10;
                    }
                }
                elseif($k == 'redirect' || $k == 301)
                {
                    $sitemap['alias_id'] = $sitemap['content_id'] = 0;
                    $sitemap['code'] = 301;
                    $sitemap['data'] = $v;
                }
                else
                    $sitemap[$k] = $v;
            }
        }
        else
        {
            if($key == 'changefreq')
            {
                if(in_array($value, array('always','hourly','daily','weekly','monthly','yearly','never')))
                    $sitemap['changefreq'] = $value;
            }
            elseif($key == 'priority')
            {
                if(is_numeric($value) && $value >= 0)
                {
                    $sitemap['priority'] = $value;
                    if($sitemap['priority'] < 1)
                        $sitemap['priority'] *= 10;
                }
            }
            elseif($key == 301)
            {
                $sitemap['alias_id'] = $sitemap['content_id'] = 0;
                $sitemap['code'] = 301;
                $sitemap['data'] = $value;
            }
            elseif ($key == 'link')
            {
                $this->deleteRow('wl_sitemap', ['link' => $value, 'alias_id' => '!'.$where['alias_id'], 'content_id' => '!'.$where['content_id']]);
                $sitemap['link'] = $value;
                $sitemap['link_sha1'] = sha1($value);
            }
            else
                $sitemap[$key] = $value;
        }
        if(!empty($sitemap))
        {
            $sitemap['time'] = time();
            $this->db->updateRow('wl_sitemap', $sitemap, $where);
        }
    }

    public function index($content_id = 0, $value = 1, $alias_id = 0)
    {
        if($alias_id == 0)
            $alias_id = $this->alias->id;
        if($value == 0)
            $this->db->executeQuery("UPDATE `wl_sitemap` SET `priority` = `priority` * -1 WHERE `alias_id` = {$alias_id} AND `content_id` = {$content_id} AND `priority` > 0");
        else
            $this->db->executeQuery("UPDATE `wl_sitemap` SET `priority` = `priority` * -1 WHERE `alias_id` = {$alias_id} AND `content_id` = {$content_id} AND `priority` < 0");
    }

    public function remove($content_id = 0, $alias_id = 0)
    {
        if($alias_id == 0)
            $alias_id = $this->alias->id;
        $this->db->deleteRow('wl_sitemap', ['alias_id' => $alias_id, 'content_id' => $content_id]);
        return true;
    }

}