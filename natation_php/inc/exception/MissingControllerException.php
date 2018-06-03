<?php

namespace exception;

/**
 * Description of MissingControllerException
 *
 * @author dademo
 */
class MissingControllerException extends \Exception {

    public function __construct(string $controller) {
        parent::__construct('Le contrôleur "' . $controller . '" n\'existe pas ');
    }

}
