<?php

/*
 * Модель для роботи з базою даних користувачів.
 * For White Lion CMS 2.0
 */

class wl_user_model extends Model {

    public $user_errors = '';
    public $model_table = 'wl_users';


    // auto init when Router boots
    public function authorize()
    {
        if (empty($_SESSION['user']))
            $_SESSION['user'] = new stdClass();

        if (isset($_COOKIE['auth_key']))
        {
            if($this->user->is())
            {
                $time5min = $this->user->last_login + 60 * 5;
                if (time() > $time5min)
                {
                    $_SESSION['user']->last_login = time();
                    $this->db->updateRow('wl_user_auth', [
                                            'last_login' => $_SESSION['user']->last_login, 
                                            'from' => $this->user->ip_address()
                                        ], $this->user->auth_id);
                    if($this->user->auth())
                        $this->update($this->user->id, ['last_login' => $_SESSION['user']->last_login]);
                }
            }
            else
                $this->login('auth_key', $_COOKIE['auth_key']);
        }
    }

    /*
     * Отримуємо дані користувача з бази даних
     */
    function getInfo($where = [])
    {
    	if(empty($where))
        {
            if ($this->user->auth())
                $where = $_SESSION['user']->id;
            else
                return false;
        }
        $user = $this->db->select('wl_users as u', '*', $where)
                        ->join('wl_user_types', 'name as type_name, title as type_title', '#u.type_id')
                        ->join('wl_user_status', 'name as status_name, title as status_title, color as status_color, next', '#u.status_id')
                        ->get('single');
        if($user)
        	if($info = $this->db->getAllDataByFieldInArray('wl_user_info', $user->id, 'user_id'))
        		foreach ($info as $i) {
                    $key = $i->field;
                    if(empty($$user->$key))
        			    $user->$key = $i->value;
                    else {
                        $keys = $key.'s';
                        if(empty($user->$keys))
                            $user->$keys = [$user->$key];
                        $user->$keys[] = $i->value;
                    }
        		}
        return $user;
    }

    /*
     * Метод додає користувача до бази.
     * info (array) масив з основними даними користувача (email, phone, name, photo, password)
     * additionall (array) додаткові дані користувача (facebook_id, google_id, city..)
     * comment (text) службовий коментар у реєстр
     */
    public function create($info = array(), $additionall = array(), $comment = '')
    {
        if(empty($info))
        {
            $this->user_errors = '-wl_user_model/add: variable `info` empty!';
            return false;
        }

        if(empty($info['phone']) && empty($info['email']))
        {
            $this->user_errors = 'Fields email and phone are required';
            return false;
        }

        $info['type_id'] = $info['type_id'] ?? $this->alias->__new_user_type;
        $info['type_id'] = $info['type_id'] ?? 3;
        $info['status_id'] = $info['status_id'] ?? 2;
    	$status = $this->db->getAllDataById('wl_user_status', $info['status_id']);

        if(empty($status))
        {
            $this->user_errors = '-wl_user_model/add: status_id error (status in wl_user_status not found)';
            return false;
        }

        $user = false;
        if(!empty($info['phone']))
            $user = $this->getOne(['phone' => $info['phone']]);
        if(empty($user) && !empty($info['email']))
            $user = $this->getOne(['email' => $info['email']]);

        if($user)
        {
            // for subscriber
        	if($user->type_id == 5)
        	{
        		$data = array();
                foreach (['email', 'phone', 'name', 'photo'] as $key) {
                    if(!empty($info[$key]) && $info[$key] != $user->$key)
                        $user->$key = $data[$key] = $info[$key];
                }
                $data['last_login'] = $user->last_login = time();
                $user->uri = $data['uri'] = md5($user->email.$user->phone.$user->name.$user->registered.SYS_PASSWORD);

                if(!empty($info['password']))
		    	    $data['password'] = $user->password = $this->getPassword($user->id, $info['password']);

		    	if($this->update($user->id, $data))
		    		$this->user->register('signup', $comment, $user->id);

                $user->info = array();
                if($info = $this->db->getAllDataByFieldInArray('wl_user_info', $user->id, 'user'))
                    foreach ($info as $i) {
                        $user->info[$i->field] = $i->value;
                    }
        	}
        	else
    		{
    			$this->user_errors = 'User with this email or phone exist';
    			return false;
    		}
            
        }
        else
        {
        	$user = new stdClass();
        	$data = $user->info = array();
            foreach (['email', 'phone', 'name', 'photo', 'type_id', 'status_id'] as $key) {
                $user->$key = $data[$key] = $info[$key] ?? '';
            }
    		$data['registered'] = $user->registered = $data['last_login'] = $user->last_login = time();
            $user->uri = $data['uri'] = md5($user->email.$user->phone.$user->name.$user->registered.SYS_PASSWORD);

	    	if($user->id = $this->insert($data))
	    	{
                if(!empty($info['password']))
                    $this->update($user->id, ['password' => $this->getPassword($user->id, $info['password'])]);

	    		$this->user->register('signup', $comment, $user->id);
	    	}
            else
                return false;
        }

        if($user)
        {
            if(isset($info['auth']))
            {
                if($info['auth'] == false)
                    return $user;
            }
            
            $user->load = $status->load;

            if($this->user->is_temp())
                $this->db->updateRow('wl_user_auth', ['user_id' => $user->id], $this->user->auth_id);
            else
            {
                $data = ['user_id' => $user->id,
                         'auth_key' => sha1($user->id.time()),
                         'last_login' => $user->registered,
                         'from' => $this->user->ip_address(),
                         'title' => '',
                         'created_at' => $user->registered,
                         'created_by' => 'wl_user_model/add'
                        ];
                $user->auth_id = $this->db->insertRow('wl_user_auth', $data);
                setcookie('auth_key', $data['auth_key'], time() + 3600*24*31, '/');
            }

        	if(!empty($additionall))
			{
				foreach ($additionall as $key => $value) {
                    if(empty($user->info[$key]))
                    {
    					$info = array();
    					$info['user'] = $user->id;
    					$info['field'] = $key;
    					$info['value'] = $value;
    					$info['date'] = time();
    					$this->db->insertRow('wl_user_info', $info);
                        $user->info[$key] = $value;
                    }
				}
			}

            $this->setSession($user);
			return $user;
		}

    	$this->user_errors = 'Signup error. Please, try again';
        return false;
    }

    public function delete($user_id = 0, $_ = null)
    {
        if($user = $this->getOne($user_id)) {
            $this->db->deleteRow('wl_users', $user_id);
            $this->db->deleteRow('wl_user_auth', $user_id, 'user_id');
            $this->db->deleteRow('wl_user_info', $user_id, 'user_id');
            // $this->db->deleteRow('wl_user_register', $user_id, 'user_id');

            if($this->user->id != $user_id)
                $this->user->register('user_delete', "#{$user->id}. {$user->email}. {$user->name}. Type id: {$user->type_id}. Registered: " . date('d.m.Y H:i', $user->registered));

            return true;
        }
        return false;
    }
	
	public function checkConfirmed($email, $code)
	{
		if($user = $this->getInfo(['email' => $email, 'auth_id' => $code]))
            if($status = $this->db->getAllDataById('wl_user_status', $user->next))
    		{
    			$this->update($user->id, array('status_id' => $user->next));
    			$user->status_id = $user->next;
    			$this->setSession($user);
    			$this->user->register('confirmed');
    			return $status;
            }
        return false;
	}

    public function login($key = 'email_phone', $key_value = '')
    {
        if ($key == 'auth_key')
        {
            if($auth = $this->db->getAllDataById('wl_user_auth', $key_value, 'auth_key'))
            {
                $_SESSION['user']->auth_id = $auth->id;
                $_SESSION['user']->last_login = time();
                $this->db->updateRow('wl_user_auth', [
                                                        'last_login' => time(),
                                                        'from' => $this->user->ip_address()
                                                     ], $auth->id);

                if($auth->user_id > 0)
                {
                    $user = new stdClass();
                    $user->id = $auth->user_id;
                }
                else
                    $_SESSION['user']->id = -$auth->id;
            }
            else
                return false;
        }
        else if(in_array($key, ['email', 'phone', 'email_phone']))
        {
            if($key == 'email' || $key == 'email_phone')
                $user = $this->getInfo(['email' => $key_value]);
            if(empty($user))
                $user = $this->getInfo(['phone' => $key_value]);

            if (!empty($user))
            {
                if(empty($user->password)) {
                    $this->user_errors = 'User without password. Please use other login methods';
                    return false;
                }

                if (!$this->getPassword($user->id, $_POST['password'], $user->password)) {
                    $this->user->register('login_bad', 'User IP: ' . $this->user->ip_address(), $user->id);
                    $this->user_errors = 'Bad password';
                    return false;
                }
            }
		}
        else 
		{
            $user = $this->db->select('wl_user_info', 'user_id as id', ['field' => $key, 'value' => $key_value])->get('single');
		}
        
		if($user)
		{
            if (empty($user->type_id))
                $user = $this->getInfo($user->id);

			if($user->status_id != 3)
			{
				$this->setSession($user, false);

				$this->update($user->id, ['last_login' => time()]);
                $this->user->register('login_ok', "By {$key}. User IP: " . $this->user->ip_address());
				return $user;
			}
			else
				$this->user_errors = 'User banned';
		}
        else
			$this->user_errors = 'Bad email or phone';
		return false;
    }

    private $wl_userID_info = 0;
    private $wl_user_info = false;
    public function setAdditional($user_id, $key, $value = '', $rewrite = true)
    {
        if(empty($user_id) || empty($key))
            return false;

        if($this->wl_userID_info != $user_id)
        {
            $this->wl_user_info = $this->db->getAllDataByFieldInArray('wl_user_info', $user_id, 'user');
            $this->wl_userID_info = $user_id;
        }

        if($this->wl_user_info)
            foreach ($this->wl_user_info as $additionall) {
                if($additionall->field == $key && $rewrite)
                {
                    if($additionall->value != $value) {
                        $this->db->updateRow('wl_user_info', array('value' => $value, 'date' => time()), $additionall->id);
                        $text = 'Попереднє значення ' . $key.': '.$additionall->value;
                        if($user_id != $_SESSION['user']->id)
                            $text .= ' (менеджер: '. $_SESSION['user']->id. ', '.$_SESSION['user']->name.')'; 
                        $this->user->register('profile_data', $text, $user_id);
                    }
                    return true;
                }
            }

        if(empty($value))
            return true;

        $this->db->insertRow('wl_user_info', ['user_id' => $user_id, 'field' => $key, 'value' => $value, 'date' => time()]);                
        return true;
    }

    public function reset($key, $value)
    {
        if($user = $this->getOne([$key => $value]))
        {
            $data = array();
            $data['reset_key'] = $user->reset_key = md5(SYS_PASSWORD.'|'.$user->id.'|'.time());
            $data['reset_expires'] = $user->reset_expires = mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"));//+ 2 ГОДИНИ!!!
            $this->update($user->id, $data);
            $this->user->register('reset_sent', '', $user->id);
            return $user;
        }
        return false;
    }

	public function getPassword($user_id, $password, $password_verify = false)
    {
        if(!$this->data->post('sequred'))
            $password = md5($password);
        $password = sha1($password . SYS_PASSWORD . $user_id);

        if(!empty($password_verify))
            return password_verify($password, $password_verify);
        return password_hash($password, PASSWORD_BCRYPT);
    }

	public function setSession($user, $updateLastLogin = true)
	{
        if (!empty($user->permissions))
            $user->permissions = unserialize($user->permissions);
        foreach ($user as $key => $value) {
            $_SESSION['user']->$key = $value;
        }
        if(!empty($user->photo))
            $_SESSION['user']->photo = IMG_PATH.'profile/'.$user->photo;
        $_SESSION['user']->admin = $_SESSION['user']->manager = 0;

        if($user->type_id == 1)
            $_SESSION['user']->admin = 1;
        else if($user->type_id == 2)
        {
            $search_forms = array();
            $_SESSION['user']->manager = 1;
            $_SESSION['user']->permissions += array('admin', 'wl_users', 'wl_ntkd', 'wl_photos', 'wl_video', 'wl_audio', 'wl_files');
            
            if($forms = $this->db->getAllDataByFieldInArray('wl_forms', array('id' => $search_forms)))
                foreach ($forms as $form) {
                    $_SESSION['user']->permissions[] = 'form_'.$form->name;
                }
        }

        if($updateLastLogin)
            $this->db->updateRow('wl_users', array('last_login' => time()), $user->id);

        if(empty($_SESSION['user']->auth_id))
        {
            $auth_key = sha1($user->id.'|auth_id|'.time().SYS_PASSWORD);
            setcookie('auth_key', $auth_key, time() + 3600*24*31, '/');
            $data = ['user_id' => $user->id,
                     'auth_key' => $auth_key,
                     'last_login' => time(),
                     'from' => $this->user->ip_address(),
                     'title' => '',
                     'created_at' => time(),
                     'created_by' => 'wl_user_model/setSession'
                    ];
            $_SESSION['user']->auth_id = $this->db->insertRow('wl_user_auth', $data);
        }

        return true;
	}

    public function setPhotoByLink($facebookPhotoLink, $userId = 0, $updateSession = true)
    {
        if($facebookPhoto = file_get_contents($facebookPhotoLink))
            if($info = getimagesize($facebookPhotoLink))
            {
                if($userId == 0)
                    $userId = $_SESSION['user']->id;
                if(empty($info['mime']))
                    return false;
                $mime = explode('/', $info['mime']);
                if($mime[0] != 'image')
                    return false;
                if(empty($mime[1]) || strlen($mime[1]) < 3 || $mime[1] == 'jpeg')
                    $mime[1] = 'jpg';

                $photoName = $userId;
                $photoName .= '-'.md5($userId.time()) .'.'. $mime[1];

                $path = IMG_PATH;
                $path = substr($path, strlen(SITE_URL));
                $path = substr($path, 0, -1);
                if(!is_dir($path))
                {
                    if(mkdir($path, 0777) == false)
                        return false;
                }
                $path .= '/profile';
                if(!is_dir($path))
                {
                    if(mkdir($path, 0777) == false)
                        return false;
                }
                $path .= '/';
                
                if(file_put_contents($path.$photoName, $facebookPhoto))
                {
                    $class_path = SYS_PATH.'libraries'.DIRSEP.'image.php';
                    if(file_exists($class_path))
                    {
                        require $class_path;
                        $image = new image();
                        $image->loadImage($path.$photoName);
                        $image->preview(300, 300, 100);
                        $image->save('s');
                        $image->loadImage($path.$photoName);
                        $image->preview(50, 50, 100);
                        $image->save('p');
                    }

                    $this->db->updateRow('wl_users', array('photo' => $photoName), $userId);
                    if($updateSession && isset($_SESSION['user']->photo))
                        $_SESSION['user']->photo = $path.$photoName;
                    return $photoName;
                }
            }
        return false;
    }
	
}

?>