<?php

/*
 * Шлях: SYS_PATH/libraries/paginator.php
 *
 * Відображення блоку з переключенням сторінок.
 * Версія 1.0 (08.10.2015) - основа бібліотеки. Функції public: paginate(), style(), get(); private: make().
 * Використовуються як вхідні дані по замовчуванню: 
 *     $_SESSION['option']->paginator_total - загальна кількість сторінок у розділі
 *     $_SESSION['option']->paginator_per_page - кількість сторінок на сторінці
 *     $_GET['page'] - поточна сторінка
 * Версія 1.1 (12.10.2015) - додано можливість задання стилів по замовчуванню з конфігураційного файлу
 * Версія 1.1.1 (26.07.2016) - адаптовано до php7
 * Версія 1.1.2 (24.11.2016) - виправлено помилку зайвої сторінки
 * Версія 2 (27.09.2021) - Update to wl 2.0
 */

class Paginator {

    public $per_page = 10;
    public $total = 0;
    private $current_page = -1;
    public $ul_tag = 'div';
    public $ul_id = '';
    public $ul_class = 'pagination';
    public $li_tag = 'div';
    public $li_class = 'page-item';
    public $li_class_active = 'active';
    public $li_class_non_active = 'disabled';
    public $li_previous_text = '<';
    public $li_next_text = '>';
    public $li_a_class = 'page-link';
    public $li_a_class_active = '';
    public $li_a_attr = '';

    /*
     * Отримуємо дані для стилю по замовчуванню з конфігураційного файлу
     * Формат [ключ] = [клас по замовчуванню|активний клас (згідно style())]
     */
    function __construct($cfg = array())
    {
        if(!empty($cfg)){
            foreach ($cfg as $element => $class) {
                $class = explode('|', $class);
                $class_active = 'active';
                if(isset($class[1])) {
                    $class_active = $class[1];
                }
                $class = $class[0];
                $this->style($element, $class, $class_active);
            }
        }
    }

    // from app/config.php
    public function load_configs($key)
    {
        require APP_PATH . 'config.php';
        if(!empty($config[$key]) && is_array($config[$key])) {
            foreach ($config[$key] as $element => $class) {
                $class = explode('|', $class);
                $class_active = 'active';
                if (isset($class[1])) {
                    $class_active = $class[1];
                }
                $class = $class[0];
                $this->style($element, $class, $class_active);
            }
        }
    }

    public function get($current = 'auto', $total = null, $per_page = null)
    {
        if($this->current_page < 0) {
            $this->paginate($current, $total, $per_page);
        }
        echo $this->make();
    }

    public function paginate($current = 'auto', $total = null, $per_page = null)
    {
        $this->current_page = 1;
        if($current == 'auto' && isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0){
            $this->current_page = $_GET['page'];
        } elseif(is_numeric($current) && $current > 0){
            $this->current_page = $current;
        }

        if(!is_null($total)) {
            $this->total = $total;
        }

        if(!is_null($per_page)) {
            $this->per_page = $per_page;
        }
    }

    public function style($element = false, $class = '', $class_active = 'active')
    {
        switch ($element) {
            case 'ul_tag':
                $this->ul_tag = $class;
                break;
            case 'ul_id':
                $this->ul_id = $class;
                break;
            case 'ul':
            case 'ul_class':
                $this->ul_class = $class;
                break;

            case 'li_tag':
                $this->li_tag = $class;
                break;
            case 'li':
            case 'li_class':
                $this->li_class = $class;
                $this->li_class_active = $class_active;
                break;
            case 'li_class_non_active':
                $this->li_class_non_active = $class;
                break;

            case 'previous':
            case '-':
                $this->li_previous_text = $class;
                break;
            case 'next':
            case '+':
                $this->li_next_text = $class;
                break;
            case 'previous|next':
            case '-+':
                $this->li_previous_text = $class;
                $this->li_next_text = $class_active;
                break;

            case 'a':
            case 'li a':
            case 'ul li a':
                $this->li_a_class = $class;
                $this->li_a_class_active = $class_active;
                break;
            case 'li_a_attr':
                $this->li_a_attr = $class;
                break;
        }
    }

    private function make()
    {
        $this->li_class_active = trim($this->li_class . ' ' . $this->li_class_active);
        $this->li_class_non_active = trim($this->li_class . ' ' . $this->li_class_non_active);
        $this->li_a_class_active = trim($this->li_a_class . ' ' . $this->li_a_class_active);

        $list = '';
        if($this->total > $this->per_page){
            $pages = ceil($this->total / $this->per_page);

            $start = 1;
            $finish = $pages;
            if($pages > 5) {
                $finish = 5;
                $start = $this->current_page - 2;
                $finish = $this->current_page + 2;
                if($finish > $pages) $finish = $pages;
                if($start < 1) $start = 1;
            }

            if($pages > 1) {
                $link = SERVER_URL . $_GET['request'];
                $link .= '?';
                foreach ($_GET as $key => $value) {
                    if($key != 'request' && $key != 'page'){
                        if(!is_array($value)) {
                            $link .= $key .'='.$value . '&';
                        } else {
                            foreach ($value as $key2 => $value2) {
                                $link .= $key .'%5B%5D='.$value2 . '&';
                            }
                        }
                    }
                }
                $link_1 = substr($link, 0, -1);
                $link .= 'page=';

                $list = "<{$this->ul_tag} id=\"{$this->ul_id}\" class=\"{$this->ul_class}\">";

                if($this->current_page > 1) {
                    // previous btn
                    $list .= "<{$this->li_tag} class=\"{$this->li_class} \">";
                    $list .= '<a href="';
                    if($this->current_page - 1 > 1) {
                        $list .= $link . ($this->current_page - 1);
                    } else {
                        $list .= $link_1;
                    } 
                    $list .= "\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> {$this->li_previous_text} </a>";
                    $list .= "</{$this->li_tag}>";
                } else if ($this->li_previous_text != '') {
                    // disabled previous btn
                    $list .= "<{$this->li_tag} class=\"{$this->li_class_non_active} \"> <a href=\"javascript:;\" class=\"{$this->li_a_class}\"> {$this->li_previous_text} </a> </{$this->li_tag}>";
                }

                // btn 1
                $list .= "<{$this->li_tag} class=\"";
                $list .= ($this->current_page == 1) ? $this->li_class_active : $this->li_class;
                $list .= '">';
                if($start == 1) {
                    $start++; 
                    
                    if($this->current_page > 1) {
                        $list .= "<a href=\"{$link_1}\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> 1 </a>";
                    } else {
                        $list .= "<a href=\"javascript:;\" class=\"{$this->li_a_class_active}\"> 1 </a>";
                    }
                } else {
                    $list .= "<a href=\"{$link_1}\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> 1 </a>";
                }
                $list .= "</{$this->li_tag}>";

                // btn from 2 .. finish
                for($page = $start; $page <= $finish; $page++) {
                    $list .= "<{$this->li_tag} class=\"";

                    if($this->current_page == $page) {
                        $list .= $this->li_class_active . "\"> <a href=\"javascript:;\" class=\"{$this->li_a_class_active}\"> {$page} </a>";
                    } else {
                        $list .= $this->li_class . "\"> <a href=\"{$link}{$page}\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> {$page} </a>";
                    }

                    $list .= "</{$this->li_tag}>";
                }

                // last page number
                if($page < $pages) {
                    $list .= "<{$this->li_tag} class=\"{$this->li_class} \">";
                    $list .= "<a href=\"{$link}{$pages}\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> {$pages} </a>";
                    $list .= "</{$this->li_tag}>";
                }

                // next page
                if($this->li_next_text != '') {
                    $next = $this->current_page + 1;

                    $list .= "<{$this->li_tag} class=\"";
                    $list .= ($next <= $pages) ? $this->li_class : $this->li_class_non_active;
                    $list .= '">';
                    
                    if($next <= $pages) {
                        $list .= "<a href=\"{$link}{$next}\" class=\"{$this->li_a_class}\" {$this->li_a_attr}> {$this->li_next_text} </a>";
                    } else {
                        $list .= "<a href=\"javascript:;\" class=\"{$this->li_a_class_active}\"> {$this->li_next_text} </a>";
                    }

                    $list .= "</{$this->li_tag}>";
                }

                $list .= "</{$this->ul_tag}>";
            }
        }
        return $list;
    }
}

?>