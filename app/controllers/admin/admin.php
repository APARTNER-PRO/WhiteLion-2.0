<?php

class admin__admin extends Controller {
                
    public function _remap($method, $data = array())
    {
        if (method_exists($this, $method) && $method != 'library' && $method != 'db')
        {
            if(empty($data)) $data = null;
            return $this->$method($data);
        }
        else
            $this->index($method);
    }

    public function index()
    {
        $this->page->name = 'Dashboard';
        $this->load->admin_view('index_view');
    }
    
}

?>