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

    public function test() {
        $this->setToRender('toto.twig');
        $this->render(array(
            'msg' => 'Hello World !',
            'page_title' => 'Hello World !',
            'body_title' => 'Hello World ! (this title ><)'
        ));
    }

}
