<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace router;

/**
 * Description of route
 *
 * @author dademo
 */
class Route {

    // String de la route
    private $route = '';
    // Arguments
    private $args = [];
    // Nom de l'objet à créer
    private $controller = '';
    // Fonction à appeler dans l'objet
    private $function = '';

    /**
     * Appelle la fonction associée si une correspondance est trouvée
     * @param string $route La route associée
     * @param string $controller Le contrôleur à appeler
     * @param string $function La fonction à appeler dans le contrôleur
     * @return boolean Si la route est correcte et a été appliquéeException
     */
    public function __construct(string $route, string $controller, string $function) {
        $this->route = trim($route, '/');   // Suppression des routes en début et fin
        $this->controller = 'controller\\' . $controller;
        $this->function = $function;
    }

    /**
     * Appelle la fonction associée si une correspondance est trouvée
     * @param string $route La route à tester
     * src: https://www.grafikart.fr/tutoriels/php/router-628
     */
    public function match(string $route) {
        $matches = [];
        // On supprime les '/' en début et fin de l'URL
        $route = trim($route, '/');

        $route = preg_replace('#:([\w]+)#', '([^/]+)', $route);

        $regex = "#^$this->route$#i";

        if (!preg_match($regex, $route, $matches)) {
            // No results
            return false;
        } else {
            // On supprime le chemin de la route extraite
            array_shift($matches);
            // Et on sauvegarde les résultats
            $this->args = $matches;
            // On crée le controller
            $_controller = new $this->controller();
            // On exécute la fonction associée
            $_controller->{$this->function}($this->args);
            //$this->controller::{$this->function}($this->args);
            return true;
        }
    }

}
