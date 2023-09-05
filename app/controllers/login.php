<?php

class Login extends Controller {

    public function index()
    {
    	if(!empty($_POST['password']))
    	{
    		$this->process();
    		exit;
    	}

        if($this->user->auth())
        	$this->__after_login();

        $this->page->setContent(0);
		$this->load->library('recaptcha');

        if($this->alias->__userSignUp)
        {
        	// $this->load->library('facebook');
        	// $this->load->library('googlesignin');
        	// if($this->googlesignin->clientId)
        	// 	$this->page->set('meta', '<meta name="google-signin-client_id" content="'.$this->googlesignin->clientId.'">');

            $this->load->page_view('profile/login-signup_view');
        }
        else
            $this->load->view('profile/login_page');
    }

    public function process()
    {
		$res = ['status' => 'error'];
    	$this->load->library('recaptcha');
		if($this->recaptcha->check($this->data->post('g-recaptcha-response')) == false)
		{
			$res['title'] = 'Please, try again.';
			$res['text'] = 'Google Recaptcha Error!';
			$this->notify->add_error('Please, try again.', 'Google Recaptcha Error!');
		}
		else
		{
	        $this->load->library('validator');
			$login_by = $email_phone = false;
	    	if($email_phone = $this->data->post('email'))
			{
				$email_phone = strtolower($email_phone);
				$this->validator->setRules('E-mail', $email_phone, 'required|email');
				$login_by = 'email';
			}
			elseif ($email_phone = $this->data->post('phone'))
			{
				$this->validator->setRules($this->text('Телефон', 0), $email_phone, 'required|phone');
				$email_phone = $this->validator->getPhone($email_phone);
				$login_by = 'phone';
			}
			elseif ($email_phone = $this->data->post('email_phone'))
			{
				if($email_phone = $this->validator->getPhone($email_phone))
				{
					$login_by = 'phone';
					$this->validator->setRules($this->text('Телефон', 0), $email_phone, 'required|phone');
				}
				elseif($email_phone = $this->data->post('email_phone'))
				{
					$email_phone = strtolower($email_phone);
					// $this->validator->setRules('E-mail', $email_phone, 'required|email');
					if($this->validator->email($this->text('Телефон або E-mail', 0), $email_phone))
						$login_by = 'email';
				}
				else
				{
					$this->validator->setRules($this->text('Телефон або E-mail', 0), '', 'required');
				}
			}
	        $this->validator->setRules($this->text('Password'), $this->data->post('password'), 'required|5..40');

	        if($this->validator->run() && !empty($email_phone))
	        {
	            $this->load->model('wl_user_model');
	            if($user = $this->wl_user_model->login($login_by, $email_phone))
	            {
					$this->init__wl_cooperation('__user_login');

					if(!isset($_POST['ajax']))
						$this->__after_login($user);
					else
					{
						$res['status'] = 'success';
						$this->load->json($res);
					}
					exit;
	            }
	            else
	            {
					if($this->data->post('ajax'))
					{
						$res['title'] = $this->wl_user_model->user_errors;
						$this->load->json($res);
					}
					else
						$this->notify->add_error($this->wl_user_model->user_errors);
	            }
	        }
	        else
	        {
				if($this->data->post('ajax'))
				{
					$res['title'] = $this->validator->getErrors('');
					$this->load->json($res);
				}
				else
					$this->notify->add_error($this->validator->getErrors());
	        }
	    }

	    if($redirect = $this->data->post('redirect'))
			$this->redirect($redirect);
		else
        	$this->redirect('login');
    }

	/*
	public function facebook()
	{
		$_SESSION['alias']->code = 201;
		$res = array('result' => false, 'message' => 'Error validate facebook access Token');
		$this->load->library('facebook');
		if($_SESSION['option']->userSignUp && $_SESSION['option']->facebook_initialise)
		{
			$user_profile = null;

			if ($accessToken = $this->data->post('accessToken'))
			{
				$this->facebook->setAccessToken($accessToken);

				try {
					$user_profile = $this->facebook->api('/me?fields=email,id,name,link');
				} catch (FacebookApiException $e) {
					error_log($e);
					$user_profile = null;
				}
			}

			if ($user_profile)
			{
				$this->load->model('wl_user_model');
				if($user = $this->wl_user_model->login('facebook', $user_profile['id']))
				{
					$res['result'] = true;
					if(!empty($user_profile['link']))
                    	$this->wl_user_model->setAdditional($_SESSION['user']->id, 'facebook_link', $user_profile['link']);
					if(empty($_SESSION['user']->photo))
					{
						$facebookPhotoLink = 'https://graph.facebook.com/'.$user_profile['id'].'/picture?width=9999';
						$this->wl_user_model->setPhotoByLink($facebookPhotoLink);
					}

					if($actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => 0, 'type' => '__user_login')))
						foreach ($actions as $action) {
							$this->load->function_in_alias($action->alias2, '__user_login');
						}

					if(!isset($_POST['ajax']))
						$this->__after_login($user);
				}
				else
				{
					$info = array();
					$info['email'] = $user_profile['email'];
				    $info['name'] = $user_profile['name'];
				    $info['status'] = 1;
				    $additionall['facebook'] = $user_profile['id'];
				    if(!empty($user_profile['link']))
				    	$additionall['facebook_link'] = $user_profile['link'];
				    if(empty($info['email']))
				    	$info['email'] = $user_profile['id'] . '@facebook.com';
					if($user = $this->wl_user_model->add($info, $additionall, 0, false, 'by facebook'))
					{
						$res['result'] = true;
						$this->wl_user_model->setSession($user);
						$auth_id = md5($_SESSION['user']->email.'|facebook auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');
						$this->db->updateRow('wl_users', array('auth_id' => $auth_id), $_SESSION['user']->id);

						$facebookPhotoLink = 'https://graph.facebook.com/'.$user_profile['id'].'/picture?width=9999';
						$this->wl_user_model->setPhotoByLink($facebookPhotoLink);

						if($actions = $this->db->getAllDataByFieldInArray('wl_aliases_cooperation', array('alias1' => 0, 'type' => '__user_login')))
							foreach ($actions as $action) {
								$this->load->function_in_alias($action->alias2, '__user_login');
							}

						if(!isset($_POST['ajax']))
							$this->__after_login($user);
					}
					else
						$res['message'] = $this->wl_user_model->user_errors;
				}
			}
			else
			{
				// $statusUrl = $facebook->getLoginStatusUrl();
				$loginUrl = $this->facebook->getLoginUrl();
				header('Location: '.$loginUrl);
				exit;
			}
		}
		else
			$res['message'] = 'Login by facebook is closed';
		$this->load->json($res);
	}

	public function google()
	{
		$_SESSION['alias']->code = 201;
		$res = array('result' => false, 'message' => 'Error validate google access Token');
		if($_SESSION['option']->userSignUp)
		{
			$this->load->library('googlesignin');
			if($user = $this->googlesignin->validate())
			{
				$this->load->model('wl_user_model');
				if($status = $this->wl_user_model->login('google', $user['id']))
				{
					if($status->id != 1 && $user['verified_email'])
					{
						$auth_id = md5($_SESSION['user']->email.'|google auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');

						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);
					}
					if(!empty($user['picture']) && empty($_SESSION['user']->photo))
						$this->wl_user_model->setPhotoByLink($user['picture']);
					$res['result'] = true;
				}
				else
				{
					$info = array();
					$info['email'] = $user['email'];
				    $info['name'] = $user['name'];
				    $info['status'] = 1;
				    $info['photo'] = NULL;
				    $additionall['google'] = $user['id'];
				    $additionall['google_link'] = $user['link'];
				    $additionall['gender'] = $user['gender'];
					if($__user = $this->wl_user_model->add($info, $additionall, 0, false, 'by google'))
					{
						$this->wl_user_model->setSession($__user);
						$auth_id = md5($_SESSION['user']->email.'|google auto login|auth_id|'.time());
						setcookie('auth_id', $auth_id, time() + 3600*24*31, '/');
						$this->db->updateRow('wl_users', array('status' => 1, 'auth_id' => $auth_id), $_SESSION['user']->id);

						if(!empty($user['picture']) && empty($__user->photo))
							$this->wl_user_model->setPhotoByLink($user['picture']);

						if(!isset($_POST['ajax']))
							$this->redirect($__user->load);
						else
							$res['result'] = true;
					}
					else
						$res['message'] = $this->wl_user_model->user_errors;
				}
			}
		}
		else
			$res['message'] = 'Login by google is closed';
		$this->load->json($res);
	}
	*/

	private function __after_login($user = null)
	{
		if($redirect = $this->data->post('redirect'))
			$this->redirect($redirect);
		elseif($redirect = $this->data->get('redirect'))
			$this->redirect($redirect);
		elseif(!empty($user) && !empty($user->load))
			$this->redirect($user->load);
		elseif($this->user->can('admin'))
			$this->redirect('admin');
		else
			$this->redirect('profile');
	}

}

?>