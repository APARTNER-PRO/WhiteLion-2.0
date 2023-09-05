<?php if (!defined('SYS_PATH')) exit('Access denied');

class Page {

    public $db;
    public $alias;
    public $data;

    protected $id = NULL; // id of the content: `content_id` in db queries
    protected $code = 200;
    protected $amp_view = false;
    protected $name;
    protected $title;
    protected $description;
    protected $text_short;
    protected $text_full;
    protected $meta;
    protected $files;
    protected $audios;
    protected $image;
    protected $images;
    protected $videos;
    protected $section;
    protected $sections;
    protected $css;
    protected $js_load;
    protected $js_init;
    protected $breadcrumbs;

    public function __construct()
    {
        $this->name = $this->title = $this->description = $this->text_full = $this->text_short = $this->meta = '';
		$this->files = $this->audios = $this->image = $this->images = $this->videos = false;
		$this->css = $this->js_load = $this->js_init = $this->breadcrumbs = array();
    }

    public function addTo($key, $value, $addToExist = PHP_EOL)
    {
        if(empty($addToExist) || empty($this->$key))
            $this->$key = $value;
        else
            $this->$key .= $addToExist . $value;
    }

    public function __set($key, $value)
    {
        if(isset($this->$key))
        {
            if(is_array($this->$key))
            {
                if(is_array($value))
                    $this->$key = array_merge($this->$key, $value);
                else
                    $this->$key[] = $value;
            }
            else
                $this->$key = $value;
            
            return $value;
        }
        return null;
    }

    public function __get($key)
    {
        return $this->$key ?? null;
    }

    public function __isset($key)
    {
        return !empty($this->$key);
    }

    public function setArrayValue($key, $i, $value)
    {
        if (isset($this->$key) && is_array($this->$key)) {
            $this->$key[$i] = $value;
            return $value;
        }
        return null;
    }

    public function setContent($content_id = 0, $code = 200)
    {
        if (!is_numeric($content_id))
            return false;

        $this->id = $content_id;
        $this->code = $code;
        $this->meta = html_entity_decode($this->alias->options['global_MetaTags'], ENT_QUOTES);

        $where = $this->data->array_language(['alias_id' => $this->alias->id, 'content_id' => $content_id]);

        if ($data = $this->db->getAllDataById('wl_ntkd', $where))
        {
            $this->name = html_entity_decode($data->name, ENT_QUOTES);
            $this->title = html_entity_decode($data->title, ENT_QUOTES);
            $this->description = html_entity_decode($data->description, ENT_QUOTES);
            $this->keywords = html_entity_decode($data->keywords, ENT_QUOTES);
            $this->text_full = html_entity_decode($data->text_full, ENT_QUOTES);
            $this->text_short = html_entity_decode($data->text_short, ENT_QUOTES);
            $this->addTo('meta', html_entity_decode($data->meta, ENT_QUOTES));
        }
        if (empty($this->breadcrumbs))
        {
            if ($content_id == 0)
            {
                $this->alias->name = $this->name;
                $this->breadcrumbs = array($this->alias->name => '');
            }
            else
            {
                $this->breadcrumbs = array($this->alias->name => $this->alias->alias, $this->name => '');
            }
        }

        unset($where['language']);

        if($wl_media = $this->db->getAllDataWithWlUsers('wl_media', $where, 'position ASC'))
        {
            foreach ($wl_media as $media) {
                $media->title = $this->data->serialized_language($media->title);
                $media->description = $this->data->serialized_language($media->description);
                if(empty($media->title))
                    $media->title = $this->name;
                $media_type = $media->media_type . 's';
                if (empty($this->$media_type))
                    $this->$media_type = [$media];
                else
                    $this->$media_type[] = $media;
            }

            if (!empty($this->images)) {
                foreach ($this->images as $image) {
                    if ($this->alias->imageReSizes)
                        foreach ($this->alias->imageReSizes as $resize) {
                            $image->{$resize->prefix . '_path'} = SERVER_URL . $this->data->get_file_path($image, $resize->prefix . '-');
                        }
                    $image->path = SERVER_URL . $this->data->get_file_path($image);
                }
                if (isset($this->images[0]->header_path))
                    $this->image = $this->images[0]->header_path;
                else
                    $this->image = $this->images[0]->path;
            }
        }

        return true;
    }

    public function getVideosFromText()
    {
        $video = false;
        if (preg_match_all("#\{video-[0-9]+\}#is", $this->text_full, $video) > 0)
        {
            $videos = array();
            $videos_id = array();
            foreach ($video[0] as $v)
            {
                $id = substr($v, 7);
                $id = substr($id, 0, -1);
                $videos_id[$id] = $v;
            }
            foreach ($videos_id as $id => $text)
            {
                $video = $this->db->getAllDataById('wl_video', $id);
                if ($video)
                {
                    $video->replace_text = $text;
                    $videos[] = $video;
                }
            }
            return $videos;
        }
        return false;
    }

    public function setContentRobot($data = array())
    {
        if (!is_numeric($this->id))
            return false;

        /*$ntkd = $where = array();
        $keys = array('title', 'description', 'keywords', 'text_full', 'text_short', 'meta');
        if ($_SESSION['language'])
        $where['language'] = $_SESSION['language'];

        if ($this->alias->id > 0)
        {
            $where['alias'] = array(0, $this->alias->id);
            $keyCacheContent = 'ntkd_robot__1'; // -1;
            if ($this->id > 0)
            {
                $where['content_id'] = array(0, 1);
                $keyCacheContent = 'ntkd_robot_1'; // +1;
            }
            else
                $where['content_id'] = array(0, -1);
            if ($_SESSION['language'])
            $keyCacheContent .= '_' . $_SESSION['language'];
            $all = false;
            if (isset($_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent))
                $all = $_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent;
            else
                $all = $this->db->getAllDataByFieldInArray('wl_ntkd_robot', $where, 'alias DESC');
            if ($all)
                foreach ($all as $row)
                {
                    foreach ($row as $key => $value)
                    {
                        if (in_array($key, $keys) && $value != '')
                            $ntkd[$key] = htmlspecialchars_decode($value);
                    }
                }
            if (isset($_SESSION['alias-cache'][$this->alias->id]))
                $_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent = $all;
        }
        else
        {
            $where['alias'] = $where['content_id'] = 0;
            $keyCacheContent = 'ntkd_robot_0'; // 0;
            if ($_SESSION['language'])
            $keyCacheContent .= '_' . $_SESSION['language'];
            $all = false;
            if (isset($_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent))
                $all = $_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent;
            else
                $all = $this->db->getAllDataById('wl_ntkd_robot', $where);
            if ($all)
                foreach ($all as $key => $value)
                {
                    if (in_array($key, $keys) && $value != '')
                        $ntkd[$key] = htmlspecialchars_decode($value);
                }
            if (isset($_SESSION['alias-cache'][$this->alias->id]))
                $_SESSION['alias-cache'][$this->alias->id]->$keyCacheContent = $all;
        }
        if (!empty($ntkd))
        {
            $keys = array();
            if (!empty($data))
                foreach ($data as $key => $value)
                {
                    $name = '{';
                    if (is_object($value))
                    {
                        $name .= $key . '.';
                        foreach ($value as $keyO => $valueO)
                        {
                            if (!is_object($valueO) && !is_array($valueO))
                                $keys[$name . $keyO . '}'] = $valueO;
                        }
                    }
                }
            $keys['{name}'] = $this->name;
            $keys['{SITE_URL}'] = SITE_URL;
            $keys['{IMG_PATH}'] = IMG_PATH;
            foreach ($ntkd as $key => $value)
            {
                if ($this->alias->$key == '')
                {
                    foreach ($keys as $keyR => $valueR)
                    {
                        $value = str_replace($keyR, $valueR, $value);
                    }
                    $this->alias->$key = $value;
                }
            }
        } */
        if ($this->title == '')
            $this->title = $this->name;
        if ($this->description == '')
            $this->description = $this->data->getShortText($this->text_short);
    }

}