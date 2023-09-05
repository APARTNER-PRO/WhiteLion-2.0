<?php
define('AUTHOR', 'webspirit.com.ua');

define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('CACHE_PATH',  'cache' . DIRSEP);

if(!defined('MEDIA_PATH'))
    define('MEDIA_PATH',  SERVER_URL . 'media' . DIRSEP);

define('ASSETS_PATH', SERVER_URL . 'assets'. DIRSEP);
define('STYLE_PATH', ASSETS_PATH . 'style'. DIRSEP);
define('JS_PATH', ASSETS_PATH . 'js'. DIRSEP);

if(!defined('SITE_NAME'))
    define('SITE_NAME', $_SERVER["SERVER_NAME"]);
define('SITE_EMAIL', $config['mail']['user']);
define('LANGUAGE', $language);
define('ALL_LANGUAGES', $all_languages);
define('WL_MODE', $mode);
define('WL_PAGE_CACHE', $usePageCache);
define('SYS_PASSWORD', $SYS_PASSWORD); // Salt for caching critical data (passwords) !!! Do NOT change after installation !!!