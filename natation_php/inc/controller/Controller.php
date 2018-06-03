<?php

namespace controller;

/**
 * Description of controller
 *
 * @author dademo
 */
abstract class Controller {
    // The page to render; default null, if not render it
    private $toRender;

    /**
     * A function that defines if an user can access the controller and the function
     * @param string $functionName The name of the function that will be called
     * @return boolean If the current user can access the controller
     */
    public function _access(string $functionName) {
        return true;
    }

    /**
     * 
     * @param string $templateName The name of the template to render
     * @return type
     */
    protected final function render(array $renderVars = array()) {
        // On ajoute l'url du site
        $renderVars['site_base'] = str_replace('index.php', '', $_SERVER['PHP_SELF']);
        $renderVars['this_path'] = $_SERVER['REDIRECT_URL'];
        
        $stacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];


        $toRender = ($this->toRender === null) ? $stacktrace['function'] . '.twig' : $this->toRender;
        $_fullObject = explode('\\', $stacktrace['class']);
        $dir = $_fullObject[count($_fullObject) - 1];

        // On charge twig
        $loader = new \Twig_Loader_Filesystem(array(
            // The view dir; contains the twig files (extension .twig)
            'src/twig/' . $dir,
            // The default dir; used if there's no file inside the view dir
            'src/twig/_default',
            // The theme dir; used for inheritance
            'src/twig/_theme'
        ));
        $twig = new \Twig_Environment($loader);

        // On charge le template
        $template = $twig->load($toRender);
        
        return $template->render($renderVars);
    }

    public final function setToRender(string $toRender) {
        $pos = strrpos($toRender, '.twig');

        // Si $toRender ne contient pas .twig, on l'ajoute
        if (!$pos || $pos != strlen($toRender) - 5) {
            $toRender .= '.twig';
        }

        $this->toRender = $toRender;
    }
    
    // Immediate redirection to the given route
    public final function redirect(string $route) {
        $_redirect_url = $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']);
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        header('Location: ' . $protocol . $_redirect_url . $route);
        exit();
    }

}
