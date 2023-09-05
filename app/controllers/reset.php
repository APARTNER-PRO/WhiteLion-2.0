<?php

class Reset extends Controller {

    public function index()
    {
    	if(!empty($_POST))
    	{
    		$this->process();
    		exit;
    	}

		if ($this->user->auth())
			$this->redirect('/');

		$this->page->setContent(0);
		$this->load->library('recaptcha');

		if ($this->alias->__userSignUp) {
			// $this->load->library('facebook');
			// $this->load->library('googlesignin');
			// if($this->googlesignin->clientId)
			// 	$this->page->set('meta', '<meta name="google-signin-client_id" content="'.$this->googlesignin->clientId.'">');

			$this->load->page_view('profile/reset-login_view');
		} else
			$this->load->view('profile/reset_page');
    }

	// Step 1:
    public function process()
    {
    	$this->load->library('recaptcha');
		if($this->recaptcha->check($this->data->post('g-recaptcha-response')) == false)
		{
			$this->notify->add_error('Please, try again.', 'Google Recaptcha Error!');
		}
		else
		{
			$this->load->library('validator');
			$this->validator->setRules('email', $this->data->post('email'), 'required|email');
			if($this->validator->run())
			{
				$this->load->model('wl_user_model');
				if($user = $this->wl_user_model->reset('email', $this->data->post('email')))
				{
					$info['id'] = $user->id;
					$info['name'] = $user->name;
					$info['reset_key'] = $user->reset_key;
					$info['reset_expires'] = $user->reset_expires;
					$this->load->library('mail');
					if($this->mail->sendTemplate('reset/sent_code', $user->email, $info))
						$this->notify->add_success(
							$this->text('An e-mail with a RECOVERY CODE and further instructions has been sent to your mailbox.').
							'<br>'.
							$this->text('ATTENTION! The email may be in the SPAM folder!'));
					else
						$this->notify->add_error($this->text('Error sending mail'));
				}
				else
					$this->notify->add_error($this->text('Email').' <strong>'.$this->data->post('email'). '</strong> '.$this->text('not found! Please, check data.'));
			}
			else
				$this->notify->add_error($this->validator->getErrors());
		}
		$this->redirect('reset');
	}

	// Step 2:
	public function go()
	{
		if(isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['code']))
		{
			$this->load->model('wl_user_model');
            if($user = $this->wl_user_model->getOne($this->data->get('id')))
            {
				if($_GET['code'] == $user->reset_key) // Якщо співпадають ключі відновлення
				{
					if($user->reset_expires > time()) // Чи ключ відновлення не застарів
					{
						$_SESSION['reset'] = $user->id;
						$this->page->setContent(0);
						$this->notify->add_success(	$this->text('The recovery key is correct! Enter a new password.') );
						$this->load->view('profile/reset-2_page', compact('user'));
						exit();
					}
					else
						$this->notify->add_error($this->text('The recovery key is NOT VALID! Repeat the password recovery procedure again.'));
				}
				else
					$this->notify->add_error($this->text('The recovery key is NOT CORRECT! Check the recovery address.'));
			}
			else
				$this->notify->add_error($this->text('The recovery key is NOT CORRECT! Check the recovery address.'));
		}
		else
			$this->notify->add_error($this->text('Access error! Check the entered data again.'));
		$this->redirect('reset');
	}

	public function SetNewPassword()
	{
		if(isset($_SESSION['reset']) && isset($_POST['id']) && is_numeric($_POST['id']) && $_SESSION['reset'] == $_POST['id'])
		{
			$this->load->library('validator');
			$this->validator->setRules('Password', $this->data->post('password'), 'required|5..20');
			$this->validator->password($this->data->post('password'), $this->data->post('re-password'));
	        if($this->validator->run())
	        {
				$this->load->model('wl_user_model');
				if($user = $this->wl_user_model->getOne($this->data->post('id')))
				{
					unset($_SESSION['reset']);
					if($_POST['reset_key'] == $user->reset_key && $user->reset_expires > time())
					{
						$this->wl_user_model->update($user->id, ['password' => $this->wl_user_model->getPassword($user->id, $_POST['password']), 'reset_key' => '']);
						$this->user->register('reset', $user->password, $user->id);

						$this->load->library('mail');
						$this->mail->sendTemplate('reset/notify_success', $user->email, ['name' => $user->name]);

						$this->notify->add_success($this->text('New password set!'), $this->text('Password recovery'));
						$this->redirect('login');
					}
					else
					{
						$this->notify->add_error($this->text('Error setting new password: Recovery key is INVALID or OBSOLETE.'));
						$this->redirect('reset');
					}
				}
			}
	        else
	            $this->notify->add_error($this->validator->getErrors());
	        $this->redirect();
		}
		else
		{
			unset($_SESSION['reset']);
			$this->redirect('reset');
		}
	}

	public function __get_SiteMap_Links()
    {
        $row = array();
        $row['link'] = $_SESSION['alias']->alias;
        $row['alias'] = $_SESSION['alias']->id;
        $row['content'] = 0;
        return array($row);
    }

    public function __get_Search($content = 0)
    {
    	$search = new stdClass();
		$search->id = $_SESSION['alias']->id;
		$search->link = $_SESSION['alias']->alias;
		$search->date = 0;
		$search->author = 1;
		$search->author_name = '';
		$search->additional = false;
		$search->folder = false;
		if(isset($_SESSION['option']->folder))
			$search->folder = $_SESSION['option']->folder;
		return $search;
    }

}

?>