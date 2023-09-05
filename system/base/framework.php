<?php  if (!defined('SYS_PATH')) exit('Access denied');
require APP_PATH . 'config.php';

/*
 * Шлях: SYS_PATH/base/framework.php
 *
 * Базове автовиправлення адреси
 * Підключаємо всі необхідні файли і створюєм об'єкт route
 */

if (is_array($basic_auth) && empty($_COOKIE['basic_auth']))
{
    $authenticated = false;
    if (isset($_GET['authorization']))
    {
        if (preg_match('/^Basic\s+(.*)$/i', $_GET['authorization'], $user_pass))
        {
            list($user, $pass) = explode(':', base64_decode($user_pass[1]));
            if ($user == $basic_auth['user'] && $pass == $basic_auth['password'])
            {
                setcookie('basic_auth', 'ok', time() + 3600 * 24 * 31, '/');
                $authenticated = true;
            }
        }
    }

    if (!$authenticated)
    {
        header('WWW-Authenticate: Basic realm="security close"');
        header('HTTP/1.1 401 Unauthorized');
        exit('Access denied: bad login or password');
    }

    unset($_GET['authorization']);
}

if ($https)
{
    if (stripos($_SERVER['HTTP_HOST'], 'localhost') !== false)
        $https = false;
}

$protocol = ($https) ? 'https://' : 'http://';
$request_uri = $_SERVER['REQUEST_URI'] ?? '/';
$request = (empty($_GET['request'])) ? '' : $_GET['request'];
$request = trim($request, '/\\');
$redirectTo = $language = false;

if ($https && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"))
    $redirectTo = true;

if(empty($_POST))
{
    if(is_int(stripos($request_uri, '//')))
    {
        $redirectTo = true;
        $request_uri = preg_replace("#(?<!^http:)/{2,}#i", "/", $request_uri);
    }
    if($request_uri != '/')
    {
        if((substr($request_uri, -1, 1) == '/' && $_SERVER["SERVER_NAME"] != 'localhost') || substr($request_uri, -1, 1) == '?')
        {
            $redirectTo = true;
            $request_uri = substr($request_uri, 0, -1);
        }
        if(is_int(stripos($request_uri, '/?')))
        {
            $redirectTo = true;
            $request_uri = str_replace('/?', '?', $request_uri);
        }
    }
    if (count($_GET) == 1 && preg_match('/[A-Z]/', $request))
    {
        $redirectTo = true;
        $request_uri = mb_strtolower($request_uri);
    }
}

if($redirectTo)
{
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $protocol . $_SERVER['HTTP_HOST'] . $request_uri);
    exit();
}

if($_SERVER["SERVER_NAME"] == 'localhost')
{
    $REQUEST_URI = explode('/', $_SERVER["REQUEST_URI"]);
    if(isset($REQUEST_URI[1]))
    {
        $server_url = 'http://localhost/' . $REQUEST_URI[1];
        define('SERVER_URL', $server_url.'/');
        define('SITE_NAME', $REQUEST_URI[1]);

        if(!empty($all_languages))
        {
            if(isset($REQUEST_URI[2]) && isset($all_languages[$REQUEST_URI[2]]) && $REQUEST_URI[2] != key($all_languages))
            {
                $language = $REQUEST_URI[2];
                define('SITE_URL', SERVER_URL . $language.'/');
                define('SITE_URL_MAIN', SERVER_URL . $language);
                $request = explode('/', $request);
                if($request[0] == $language)
                    array_shift($request);
                $request = implode('/', $request);
            }
            else
            {
                $language = key($all_languages);
                define('SITE_URL', $server_url . '/');
                define('SITE_URL_MAIN', $server_url);
            }
            
            if($request != '')
                $request = '/'.$request;
            foreach ($all_languages as $key => $locale) {
                if ($key != key($all_languages)) {
                    define('SITE_URL_' . strtoupper($key), $protocol . SITE_NAME . '/' . $key . $request);
                }
            }
            define('SITE_URL_'.strtoupper(key($all_languages)), $server_url . $request);
        }
        else
        {
            define('SITE_URL', $server_url . '/');
            define('SITE_URL_MAIN', $server_url);
        }
    }
    else
        exit('Error SITE_URL');
}
else
{
    $uri = explode('.', $_SERVER["SERVER_NAME"]);

    if (!$useWWW && $uri[0] == 'www')
    {
        array_shift($uri);
        $uri = implode(".", $uri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $protocol . $uri . '/' . $request);
        exit();
    }

    if ($useWWW && $uri[0] != 'www')
    {
        array_unshift($uri, 'www');
        $uri = implode(".", $uri);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $protocol . $uri . '/' . $request);
        exit();
    }

    if (!empty($all_languages))
    {
        $request_uri = explode('/', $request);
        if(isset($all_languages[$request_uri[0]]))
        {
            $language = $request_uri[0];
            array_shift($request_uri);
            $request = implode('/', $request_uri);
            if($language == key($all_languages))
            {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: https://' . $_SERVER['HTTP_HOST'] . '/' . $request);
                exit();
            }
            else
            {
                define('SITE_URL', $protocol . $_SERVER["SERVER_NAME"] . '/' . $language . '/');
                define('SITE_URL_MAIN', $protocol . $_SERVER["SERVER_NAME"] . '/' . $language);
            }
        }
        else
        {
            $language = key($all_languages);
            define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
            define('SITE_URL_MAIN', $protocol.$_SERVER["SERVER_NAME"].'/');
        }

        define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
        define('SITE_NAME', $_SERVER["SERVER_NAME"]);
        if($request != '')
            $request = '/'.$request;
        foreach ($all_languages as $key => $locale) {
            if($key != key($all_languages)) {
                define('SITE_URL_' . strtoupper($key), $protocol . SITE_NAME . '/' . $key . $request);
            }
        }
        define('SITE_URL_'.strtoupper(key($all_languages)), $protocol . SITE_NAME . $request);
    }
    else
    {
        define('SERVER_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
        define('SITE_URL', $protocol.$_SERVER["SERVER_NAME"].'/');
        define('SITE_URL_MAIN', $protocol.$_SERVER["SERVER_NAME"].'/');
        define('SITE_NAME', $_SERVER["SERVER_NAME"]);
    }
}

if(!in_array($mode, ['dev', 'prod']))
{
    if(strpos($_SERVER['HTTP_HOST'], 'localhost') === false)
        $mode = 'prod';
    else
        $mode = 'dev';
}

define('SITE_EMAIL', $config['mail']['user']);

define('MEDIA_PATH', SERVER_URL . 'media/');
define('ASSETS_PATH', SERVER_URL . 'assets/');
define('STYLE_PATH', ASSETS_PATH . 'style/');
define('IMAGES_PATH', STYLE_PATH . 'images/');
define('PLUGINS_PATH', ASSETS_PATH . 'plugins/');

define('LANGUAGE', $language);
define('LANGUAGE_LOCALE', ($all_languages[$language] ?? 'uk_UA'));
define('ALL_LANGUAGES', $all_languages);
define('WL_MODE', $mode);
define('WL_PAGE_CACHE', $usePageCache);
define('SYS_PASSWORD', $SYS_PASSWORD); // Сіль для кешування критичних даних (паролі) !!! Після інсталяції НЕ ЗМІНЮВАТИ !!!


include_once 'constant.php';
require_once 'function.php';

$request = ($request == '') ? 'main' : $request;
if($request[0] == '/')
    $request = substr($request, 1);

require 'registry.php';
require 'loader.php';
require 'controller.php';
require 'model.php';
require 'router.php';

$app = new Router($request);
$app->authorize();
if($onlyAuthUser && !$app->user->auth() && !in_array($request, ['login', 'reset', 'logout', 'cron', 'assets', 'style', 'js', 'media']))
    $app->redirect('login');
$app->init($skip_ip_statistic);
