<?php
$prod = false;
$cache = dirname(__DIR__).'/temp/cache';
$url = empty($_SERVER['PATH_INFO']) ?  '/' : $_SERVER['PATH_INFO'];
$query = empty($_SERVER['QUERY_STRING'])? '': '?' .urldecode($_SERVER['QUERY_STRING']);
$exc = $url . $query;
$vars = array(
    '%:last-update%'     => time(),
    '%:error_reporting%' => !$prod ? 'E_ALL': 'E_ALL & ~E_DEPRECATED & ~E_STRICT',
    '%:display_error%'   => !$prod ? 'On': 'Off',
    '%:app_path%'        => dirname(__DIR__).'/',
    '%:core_path%'       => dirname(dirname(dirname(__DIR__))) . '/core/',
    '%:public%'          => rtrim(($_SERVER['REQUEST_URI']), $exc).'/'
);
$file = file_get_contents(__DIR__.'/template.tpl');
$str = str_replace(array_keys($vars), array_values($vars), $file);
file_put_contents("$cache/vars.php", $str);
include "$cache/vars.php";

require APP_PATH . 'libs/autoload.php';
require APP_PATH . 'libs/init.php';
require CORE_PATH . 'kumbia/bootstrap.php';

