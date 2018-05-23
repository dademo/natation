<?php
namespace exception;
/**
 * Description of pgQueryException
 *
 * @author dademo
 */
class pgQueryException extends \Exception {
    private $errorMessage;
    private $fullErrorMessage;
    private $query;
    
    public function __construct($query, $errorMessage, $fullErrorMessage) {
        parent::__construct();
        $this->query = $query;
        $this->errorMessage = $errorMessage;
        $this->fullErrorMessage = $fullErrorMessage;
    }
    
    function getErrorMessage() {
        return $this->errorMessage;
    }

    function getFullErrorMessage() {
        return $this->fullErrorMessage;
    }

    function getQuery() {
        return $this->query;
    }
    
    public function __toString() {
        return parent::__toString();
    }
}
