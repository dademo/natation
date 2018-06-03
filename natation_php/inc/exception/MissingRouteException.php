<?php

namespace exception;

/**
 * Description of routeException
 *
 * @author dademo
 */
class MissingRouteException extends \Exception {

    public function __construct(string $route) {
        parent::__construct('La route "' . $route . '" n\'a pas trouvé de correspondance');
    }

}
