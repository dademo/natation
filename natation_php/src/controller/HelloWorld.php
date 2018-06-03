<?php

namespace controller;

/**
 * Description of HelloWorld
 *
 * @author dademo
 */
class HelloWorld extends Controller {

    public function __construct() {
        //echo 'Hello world !';
    }

    public function test(array $args = array()) {
        $this->setToRender('toto.twig');
        return $this->render(array(
                    'msg' => 'Hello World !',
                    'page_title' => 'Hello World !',
                    'body_title' => 'Hello World ! (this title ><)'
        ));
    }

}
