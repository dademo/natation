<?php

require_once __DIR__ . '/inc/router/RouteException.php';
require_once __DIR__ . '/inc/router/Route.php';
require_once __DIR__ . '/inc/router/Router.php';

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/autoload.php';

use router\Router;
use router\Route;

use controller\HelloWorld;

/*$test = new HelloWorld();

//$test->test();

*/

echo $actual_link =
        'http://'.$_SERVER['HTTP_HOST']
        .$_SERVER['PHP_SELF']
        ;

kint::dump($_SERVER);
kint::dump($_GET);
kint::dump(__FILE__);

$router = new Router();

$router->addRoute(new Route('Hello', 'HelloWorld', 'test'));

$router->addRoute(new Route('', 'HelloWorld', 'test'));

$router->match($_GET['url']);