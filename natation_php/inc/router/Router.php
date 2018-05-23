<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace router;

/**
 * Description of router
 *
 * @author dademo
 */
class Router {

    private $customRoutes = [];

    public function match($url) {
        // On supprime les '/' en début et fin de l'URL
        $trim_url = trim($url, '/');

        foreach ($this->customRoutes as $route) {
            if ($route->match($trim_url)) {
                return;
            }
        }
        // default -> call class::method
        $arr_route = explode('/', $trim_url);

        if (count($arr_route) < 2) {  // Comporte au moins le contrôleur et la fonction à appeler
            throw new RouteException($url);
        } else {
            $controller = 'controller\\' . array_shift($arr_route);
            $function = array_shift($arr_route);

            if (count($arr_route)) {  // S'il reste des paramètres supplémentaires
                $controller::$function($arr_route);
            } else {
                $controller::$function();
            }
        }
    }

    public function addRoute(Route $route) {
        $this->customRoutes[] = $route;
    }

    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

}
