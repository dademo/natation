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
    private $ignoreRoutes = [];

    public function match($url) {
        if ($this->inIgnoreList($url)) {
            \tools\tools::getRessource($url);
        } else {
            // On supprime les '/' en début et fin de l'URL
            $trim_url = trim($url, '/');

            foreach ($this->customRoutes as $route) {
                if (($res = $route->match($trim_url)) !== false) {
                    echo $res;
                    return;
                }
            }
            // default -> call class::method
            $arr_route = explode('/', $trim_url);

            if (count($arr_route) < 2) {  // Comporte au moins le contrôleur et la fonction à appeler
                // Si juste le contrôleur, on appelle la fonction index
                //throw new RouteException($url);
                $controllerName = array_shift($arr_route);
                $controller = 'controller\\' . $controllerName;
                $function = 'index';
            } else {
                $controllerName = array_shift($arr_route);
                $controller = 'controller\\' . $controllerName;
                $function = array_shift($arr_route);
            }

            $_controller = new $controller();

            if ($_controller->_access($function)) {
                if (count($arr_route)) {  // S'il reste des paramètres supplémentaires
                    //echo $_controller->$function($arr_route);
                    echo $_controller->$function($arr_route);
                    return;
                } else {
                    //echo $_controller->$function();
                    echo $_controller->$function();
                    return;
                }
            } else {
                throw new ForbiddenAccessException($controllerName, $function);
            }
        }
    }

    public function addRoute(Route $route) {
        $this->customRoutes[] = $route;
    }

    public function addIgnoreRoute(string $pathToIgnore) {
        $this->ignoreRoutes[] = $pathToIgnore;
    }

    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function inIgnoreList(string $url) {
        foreach ($this->ignoreRoutes as $ignoreRoute) {
            if (strpos($url, $ignoreRoute) === 0) {
                return true;
            }
        }

        return false;
    }

}
