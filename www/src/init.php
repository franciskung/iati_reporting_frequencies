<?php

header('Content-Type: text/html; charset=utf-8');
mb_language('uni'); mb_internal_encoding('UTF-8');

require_once('config.php');

require_once 'lib/Twig/Autoloader.php';
Twig_Autoloader::register();


$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);







$filter = new Twig_SimpleFilter('round2', function ($string) {
    return round($string,2);
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('toAscii', function ($string) {
    return toAscii($string);
});
$twig->addFilter($filter);

$filter = new Twig_SimpleFilter('strtotime', function ($string) {
    return strtotime($string);
});
$twig->addFilter($filter);

?>
