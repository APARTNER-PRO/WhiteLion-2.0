<?php 

/*
 * Google Recaptcha V3
 * https://developers.google.com/recaptcha/intro
 *
 * v2 01.03.2021 	recaptcha v3 (button()) with support v2 (form_v2())
 * v1.1 09.01.2020 	add callback and expired_callback
 * v1.0				base
 *
 */

class Recaptcha {
	
	public $initJs = true;
	private $secret = false;
	public $public = false;
	private $secret_v2 = false;
	public $public_v2 = false;

	function __construct($data)
	{
		if(isset($data['secret']))
		{
			$this->public = $data['public'];
			$this->secret = $data['secret'];
		}
		if(isset($data['secret_v2']))
		{
			$this->public_v2 = $data['public_v2'];
			$this->secret_v2 = $data['secret_v2'];
		}
	}


    public function check($response, $version = 3)
    {
    	$secret = $version == 2 ? $this->secret_v2 : $this->secret;
    	if($secret)
    	{
	    	$siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";

	    	$callback = file_get_contents($siteVerifyUrl.'secret='.$secret.'&response='.$response);
	    	$callback = json_decode($callback);
	    	if($callback->success == true)
	    		return true;
	    }
	    return true;
    }

    public function form_v2($callback = false, $expired_callback = false)
    {
    	if($this->secret_v2)
    	{
	    	$callback = ($callback) ? 'data-callback="'.$callback.'"' : '';
	    	$expired_callback = ($expired_callback) ? 'data-expired-callback="'.$expired_callback.'"' : '';
	    	if($this->initJs)
	    	{
	    		echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
	    		$this->initJs = false;
	    	}
	    	echo '<div class="g-recaptcha" data-sitekey="'.$this->public_v2.'" '.$callback.' '.$expired_callback.'></div>';
	    }
    }

    public function button($btnText = "Submit", $formId = false, $btnClass = '', $callback = false)
    {
    	if(!$formId)
    	{
    		echo "form Id param required / recaptcha v2";
    		return false;
    	}

    	if($this->initJs && $this->public)
    	{
    		echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
    		$this->initJs = false;
    	}

    	if(!$callback)
    	{
    		$callback = 'recaptchaSubmit'.ucfirst($formId);
    		echo "<script> function {$callback}(token) {
		       	var canSubmit = true;
		       	$('#{$formId}').find('input,textarea,select').filter('[required]:visible').each(function(){
		       		if(!$(this).val())
		       		{
		       			if(canSubmit)
		       				$(this).focus();
		       			$(this).addClass('is-invalid').change(function () { $(this).removeClass('is-invalid'); });
		       			canSubmit = false;
		       		}
		       	});
		        if(canSubmit)
		        {
		        	$('#divLoading').addClass('show');
		          	{$formId}.submit();
		        }
	       } </script>";
    	}
    	
    	echo '<button class="g-recaptcha '.$btnClass.'" 
	         data-sitekey="'.$this->public.'" 
	         data-callback="'.$callback.'" 
        	 data-action="submit">'.$btnText.'</button>';
    }
}

/* use js:
var recaptchaVerifyCallback = function(response) {
	$('#colToUs form button').attr('disabled', false);
	$('#colToUs form button').attr('title', false);
};
var recaptchaExpiredCallback = function(response) {
	$('#colToUs form button').attr('disabled', true);
	$('#colToUs form button').attr('title', 'Заповніть "Я не робот"');
};
*/
?>