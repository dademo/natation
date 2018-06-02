<?php

spl_autoload_register(function ($className) {

    $baseInclude = array(// Chemin de toutes les inclusions
        'src/',      // Dossier des ressources
        'inc/',
    );

    $extensionInclude = array(// Liste des extensions à inclure
        '.class.php', // Fichiers de classe
        '.php'
    );


    $fileInclude = str_replace('\\', '/', $className);

    foreach ($baseInclude as $path) {
        foreach ($extensionInclude as $extension) {
            if (file_exists($path . '/' . $fileInclude . $extension)) {
                require $path . '/' . $fileInclude . $extension;
                return true;
            }
        }
    }

    return false;
});
