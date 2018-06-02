<?php

namespace tools;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of tools
 *
 * @author dademo
 */
class tools {

    public static function render(string  $file, array $renderVars = array()) {
        $fullPath = explode('/', $file);
        
        $_file = $fullPath[count($fullPath)-1];
        unset($fullPath[count($fullPath)-1]);
        
        $_dir = '';
        foreach($fullPath as $dir) {
            $_dir .= $dir . '/';
        }
        
        
        $loader = new \Twig_Loader_Filesystem(array(
            $_dir
        ));
        $twig = new \Twig_Environment($loader);

        // On charge le template
        $template = $twig->load($_file);

        echo $template->render($renderVars);
    }
    
    public static function getRessource(string $file) {
        echo file_get_contents($file);
    }

}
