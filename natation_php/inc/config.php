<?php
// DB Connexion
// voir: http://php.net/manual/fr/function.pg-connect.php
$pg_db = [
    'host'      =>  'localhost',
    'port'      =>  5432,
    'dbname'    =>  'natation',
    'user'      =>  'apache',
    'password'  =>  'apache'
];

function pg_create_connexion_string(array $connexionTab) {
    $toReturn = '';
    foreach ($connexionTab as $key => $value) {
        $toReturn .= $key . '=' . $value . ' ';
    }
    return $toReturn;
}