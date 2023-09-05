<?php

//--- CMS White Lion 2.0 ---//

$time_start = microtime(true);
$mem_start = memory_get_usage();

session_start();

error_reporting(E_ALL);

//задаєм системні константи
define('WL_VERSION', '2.0 beta');
define('DIRSEP', DIRECTORY_SEPARATOR);
define('APP_PATH', getcwd() . DIRSEP . 'app' . DIRSEP);
define('CACHE_PATH', getcwd() . DIRSEP . 'cache' . DIRSEP);
define('SYS_PATH', getcwd() . DIRSEP . 'system' . DIRSEP);

require SYS_PATH . 'base' . DIRSEP . 'framework.php';