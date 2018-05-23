<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace router;

/**
 * Description of routeException
 *
 * @author dademo
 */
class RouteException extends \Exception {

    public function __construct(string $route) {
        parent::__construct('La route "' . $route . '" n\'a pas trouvé de correspondance');
    }

}
