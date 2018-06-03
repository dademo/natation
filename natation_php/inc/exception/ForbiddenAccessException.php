<?php

namespace exception;

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
