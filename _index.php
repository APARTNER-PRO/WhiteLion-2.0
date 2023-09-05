<?php

//--- CMS White Lion 2.0 ---//
//-- Installation package --//

session_start();
$_SESSION['notify'] = new stdClass();

error_reporting(E_ALL);

define('DIRSEP', DIRECTORY_SEPARATOR);
define('APP_PATH', getcwd() . DIRSEP.'app'.DIRSEP);
define('WL_VERSION', '2.0');

$protocol = 'http://';
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
	$protocol = 'https://';

if($_SERVER["SERVER_NAME"] == 'localhost')
{
	$REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
	if(isset($REQUEST_URI[1]))
	{
		define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/'.$REQUEST_URI[1].'/');
		define('SITE_NAME', $REQUEST_URI[1]);
	}
	else
	{
		define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
		define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
		define('SITE_NAME', $_SERVER["SERVER_NAME"]);
	}
}
else
{
	define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
	define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
	define('SITE_NAME', $_SERVER["SERVER_NAME"]);
}

if (file_exists('install/index.php')){
	// Load the Installation package
	return include 'install/index.php';
	exit;
}

?>

<h1>Error! Install folder not exist</h1>
<p>Rename folder "_install/" to "install/"</p>