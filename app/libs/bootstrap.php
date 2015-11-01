<?php
define('PRODUCTION', FALSE);
define('APP_CHARSET', 'UTF-8');
$prod  = false;
$cache = dirname(__DIR__).'/temp/cache';
$url   = empty($_SERVER['PATH_INFO'])?'/':$_SERVER['PATH_INFO'];
$query = empty($_SERVER['QUERY_STRING'])?'':'?'.urldecode($_SERVER['QUERY_STRING']);
$exc   = $url.$query;
$vars  = array(
	'%:last-update%'     => time(),
	'%:error_reporting%' => !$prod?'E_ALL':'E_ALL & ~E_DEPRECATED & ~E_STRICT',
	'%:display_error%'   => !$prod?'On':'Off',
	'%:app_path%'        => dirname(__DIR__).'/',
	'%:core_path%'       => dirname(dirname(__DIR__)).'/vendor/core/',
	'%:public%'          => substr(urldecode($_SERVER['REQUEST_URI']), 0, -strlen($exc)).'/',
);
$file = file_get_contents(__DIR__ .'/template.tpl');
$str  = str_replace(array_keys($vars), array_values($vars), $file);
file_put_contents("$cache/vars.php", $str);
include "$cache/vars.php";

require APP_PATH.'libs/autoload.php';
require APP_PATH.'libs/init.php';
require CORE_PATH.'kumbia/router.php';
require CORE_PATH.'kumbia/kumbia_router.php';
require CORE_PATH . 'kumbia/controller.php';
/*Load  backend's config */
\KBackend\Libs\Config::read('backend');
Config::read('config');
\KBackend\Libs\Template::render(Router::execute($url));