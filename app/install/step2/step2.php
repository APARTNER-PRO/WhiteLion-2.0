<?php

if($content = file_get_contents (APP_PATH."config.php"))
{
	$placeholders = array('RECAPTCHA_PUBLIC_KEY', 'RECAPTCHA_SECRET_KEY');
	$stringReplace = array($_POST['public'], $_POST['secret']);
	$newConfig = str_replace($placeholders, $stringReplace, $content);
	$configOpen = fopen(APP_PATH."config.php", "w+");
	fwrite($configOpen, $newConfig);
	fclose($configOpen);

	header("Location: ".SITE_URL."step3");
	exit();
}

?>