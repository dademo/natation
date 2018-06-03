<?php

namespace controller;

/**
 * Description of Test
 *
 * @author dademo
 */
class Test extends Controller {

    public function _access(string $functionName) {
        switch ($functionName) {
            case 'index':
                return true;
            default:
                return false;
        }
    }
    
    public function index(){
        return 'Hello !';
    }

}
