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
        if($this->inIgnoreList($url)) {
            \tools\tools::getRessource($url);
        } else {
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

                $_controller = new $controller();

                if (count($arr_route)) {  // S'il reste des paramètres supplémentaires
                    $_controller->$function($arr_route);
                } else {
                    $_controller->$function();
                }
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
        foreach($this->ignoreRoutes as $ignoreRoute) {
            if(strpos($url, $ignoreRoute) === 0) {
                return true;
            }
        }
        
        return false;
    }

}
