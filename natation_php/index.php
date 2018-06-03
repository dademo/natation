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

/* $test = new HelloWorld();

  //$test->test();

 */
/*
  echo $actual_link =
  'http://'.$_SERVER['HTTP_HOST']
  .$_SERVER['PHP_SELF']
  ; */

/* kint::dump($_SERVER);
  kint::dump($_GET);
  kint::dump(__FILE__); */

$router = new Router();

$router->addIgnoreRoute('ressources/');

$router->addRoute(new Route('Hello', 'HelloWorld', 'test'));

$router->addRoute(new Route('Hello/:arg1', 'HelloWorld', 'test'));

$router->addRoute(new Route('', 'HelloWorld', 'test'));

try {

    ob_start();
    echo $router->match($_GET['_url']);
    // END //
} catch (Twig_Error $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errTitle' => 'Erreur twig',
        'subTitle' => get_class($ex),
        'errMsg' => $ex->getMessage(),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur twig'
            )
    );
} catch (\router\RouteException $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errTitle' => 'Erreur de routage',
        'errMsg' => $ex->getMessage(),
        'subTitle' => get_class($ex),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur'
            )
    );
} catch (\router\ForbiddenAccessException $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errTitle' => 'Accès interdit',
        'errMsg' => $ex->getMessage(),
        'subTitle' => get_class($ex),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur'
            )
    );
} catch (Exception $ex) {
    ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errTitle' => 'Exception non-gérée',
        'errMsg' => $ex->getMessage(),
        'subTitle' => get_class($ex),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur'
            )
    );
} catch (Error $ex) {
    //ob_end_clean();
    tools::render('inc/tools/error.twig', array(
        'errTitle' => 'Erreur fatale',
        'errMsg' => $ex->getMessage(),
        'subTitle' => get_class($ex),
        'stacktrace' => nl2br($ex->getTraceAsString()),
        'page_title' => 'Erreur'
            )
    );
}

kint::dump($_SERVER);
