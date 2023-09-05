<?php

class Main extends Controller {

    public function index()
    {
        $this->page->setContent();
        $this->load->page_view('index_view');
    }

    public function __get_Search($content = 0)
    {
    	$search = new stdClass();
		$search->id = $this->alias->id;
		$search->link = '';
		$search->date = 0;
		$search->author = 1;
		$search->author_name = '';
		$search->additional = false;
		return $search;
    }

    public function __get_SiteMap_Links()
    {
        $row = array();
        $row['link'] = 'main';
        $row['alias'] = $this->alias->id;
        $row['content'] = 0;
        return array($row);
    }

}

?>