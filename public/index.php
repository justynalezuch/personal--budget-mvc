<?php

/**
 * Front controller
 */

/**
 * Routing
 */
//require '../Core/Router.php';

/**
 * Autoloader
 */
spl_autoload_register(function ($class) {
   $root = dirname(__DIR__); //parent directory
    $file = $root .'/'. str_replace('\\', '/', $class) . '.php';
    if(is_readable($file)) {
        require $file;
    }
});


$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);



