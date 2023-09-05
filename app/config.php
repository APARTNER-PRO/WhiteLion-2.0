<?php

/**
 * Main configuration for system/framework.php
 */
$all_languages = ['uk' => 'uk_UA', 'en' => 'en_US']; // List of all languages, the first language is the default language, Array
$mode = 'auto'; // dev, prod, auto (if in link is localhost => 'dev' : 'prod')
$basic_auth = false; // Basic authorization for the whole site. Recommended for test site (close from indexing)
// $basic_auth = ['user' => 'test', 'password' => 'asuser'];
$https = true; // if the address has localhost, the value is ignored
$useWWW = false; // Address url auto-correction
$usePageCache = false;
$skip_ip_statistic = ['::1']; // Array
$onlyAuthUser = false;
$SYS_PASSWORD = 'beEh@mSARF!8';

$config['autoload'] = array('db', 'data', 'user', 'alias', 'cache', 'page', 'notify');

$config['db'] = array(
	'dev' => array(
		'host' 		=> 'localhost',
		'user' 		=> 'root',
		'password'	=> '',
		'database'	=> 'whitelion_v2.cms'
	),
	'prod' => array(
		'host' 		=> 'localhost',
		'user' 		=> 'root',
		'password'	=> '',
		'database'	=> 'whitelion_v2.cms'
	)
);

$config['mail'] = array(
	'host' 		=> '$MAILHOST',
	'user' 		=> '$MAILUSER',
	'password'	=> '$MAILPASSWORD',
	'port'		=> '$MAILPORT'
);

// recaptcha v3
$config['recaptcha'] = array(
	'public' 	=> '$RECAPTCHA_PUBLIC',
	'secret' 	=> '$RECAPTCHA_SECRET'
);

$config['facebook'] = array(
	'appId' => 'FACEBOOK_APP_ID',
	'secret' => 'FACEBOOK_SECRET_KEY'
);

$config['googlesignin'] = array(
	'clientId' => 'GOOGLE_CLIENT_ID',
	'secret' => 'GOOGLE_API_SECRET'
);

$config['paginator'] = array(
	'ul' 		=> 'pagination',
	'-+' 		=> '← Previous | Next →'
);

$config['paginator-admin'] = array(
	'ul' 		=> 'pagination w-50',
	'-+' 		=> '← Previous | Next →',
	'li_a_attr'	=> 'data-toggle="ajax"'
);