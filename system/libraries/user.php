<?php 

/*
 * created by Ostap Matskiv for White Lion CMS 2.0
 * 1.0       17.02.2021     __get(), auth(), can(), ip_address()
             21.07.2021     is(), is_temp(), is_real(), create_temp(), register()
 */

class user
{
    public $db;
    public $alias;

    public function __get($key)
    {
        if (!$this->is())
            return null;

        if($key == 'type')
            $key = 'type_id';

        if($key == 'status')
            $key = 'status_id';

        return $_SESSION['user']->$key ?? null;
    }

    // -id: temp user from wl_user_auth | +id real user from wl_users
    public function is()
    {
        if (isset($_SESSION['user']->id) && $_SESSION['user']->id != 0 && $_SESSION['user']->auth_id > 0)
            return true;
        return false;
    }

    public function is_temp()
    {
        if (isset($_SESSION['user']->id) && $_SESSION['user']->id < 0 && $_SESSION['user']->auth_id > 0)
            return true;
        return false;
    }

    public function is_real()
    {
        return $this->auth();
    }

    // real user from wl_users
    public function auth()
    {
        if (isset($_SESSION['user']->id) && $_SESSION['user']->id > 0)
            return true;
        return false;
    }

    public function can($permission = '', $rules = 'all')
    {
        if ($this->auth())
        {
            if ($_SESSION['user']->admin == 1)
                return true;
            else if($_SESSION['user']->manager == 1)
            {
                if (empty($permission))
                    $permission = $this->alias->alias;
                if (in_array($permission, $_SESSION['user']->permissions))
                    return true;
            }
        }
        return false;
    }

    public function create_temp($created_by = '')
    {
        if($this->auth())
            return false;

        $data = ['user_id' => 0,
                 'auth_key' => sha1('0|temp_user|'.SYS_PASSWORD.time()),
                 'last_login' => time(),
                 'from' => $this->ip_address(),
                 'title' => '',
                 'created_at' => time(),
                 'created_by' => $created_by];
        $_SESSION['user']->auth_id = $this->db->insertRow('wl_user_auth', $data);
        $_SESSION['user']->id = -$_SESSION['user']->auth_id;
        $_SESSION['user']->last_login = time();
        setcookie('auth_key', $data['auth_key'], time() + 3600*24*31, '/');
        return true;
    }

    // $do - `name` (register action) from `wl_user_register_actions`
    public function register($do, $additionally = '', $user_id = 0)
    {
        if ($action = $this->db->getAllDataById('wl_user_register_actions', $do, 'name'))
        {
            if ($user_id == 0)
                $user_id = $_SESSION['user']->id;
            $data = ['user_id' => $user_id,
                     'action_id' => $action->id,
                     'action_at' => time(),
                     'additionally' => $additionally];
            if ($this->db->insertRow('wl_user_register', $data))
                return true;
        }
        return false;
    }

    public function ip_address()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            return $_SERVER['REMOTE_ADDR'];
        return NULL;
    }
    
}
