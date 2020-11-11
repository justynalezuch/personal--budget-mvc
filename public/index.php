<?php

/**
 * Front controller
 */
require_once '../vendor/autoload.php';

///**
// * Autoloader
// */
//spl_autoload_register(function ($class) {
//   $root = dirname(__DIR__); //parent directory
//    $file = $root .'/'. str_replace('\\', '/', $class) . '.php';
//    if(is_readable($file)) {
//        require $file;
//    }
//});


$router = new Core\Router();
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);





