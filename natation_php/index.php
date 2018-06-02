<?php

require_once __DIR__ . '/inc/router/RouteException.php';
require_once __DIR__ . '/inc/router/Route.php';
require_once __DIR__ . '/inc/router/Router.php';

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/inc/autoload.php';

use router\Router;
use router\Route;
use tools\tools;

use controller\HelloWorld;

/*$test = new HelloWorld();

//$test->test();

*/
/*
echo $actual_link =
        'http://'.$_SERVER['HTTP_HOST']
        .$_SERVER['PHP_SELF']
        ;*/

/*kint::dump($_SERVER);
kint::dump($_GET);
kint::dump(__FILE__);*/

$router = new Router();

$router->addIgnoreRoute('ressources/');

$router->addRoute(new Route('Hello', 'HelloWorld', 'test'));

$router->addRoute(new Route('', 'HelloWorld', 'test'));

try {
    ob_start();
    $router->match($_GET['_url']);
} catch (Twig_Error $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errMsg' => $ex->getMessage(),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur twig'
        )
    );
} catch (\router\RouteException $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errMsg' => $ex->getMessage(),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur'
        )
    );
}

kint::dump($_SERVER);