<?php

namespace exception;

/**
 * Description of MissingControllerFunctionException
 *
 * @author dademo
 */
class MissingControllerFunctionException extends \Exception {

    public function __construct(string $controller, string $function) {
        parent::__construct('La fonction "' . $function . '" du contrôleur "' . $controller . '" n\'existe pas');
    }

}
