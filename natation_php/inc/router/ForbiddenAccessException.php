<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace router;

/**
 * Description of ForbiddenAccessException
 *
 * @author dademo
 */
class ForbiddenAccessException extends \Exception {

    public function __construct(string $controller, string $function) {
        parent::__construct('Le contrôleur "' . $controller . '" vous a refusé l\'accès pour la fonction ' . $function);
    }

}
