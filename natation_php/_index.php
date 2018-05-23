<?php

// At start of script
$time_start = microtime(true); 

/*
 * CREATE USER apache WITH PASSWORD 'apache';
 * 
 * GRANT ALL PRIVILEGES ON DATABASE natation TO apache;
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once 'inc/config.php';
require_once 'inc/pg_utiles/my_pg_convert_result.php';
require_once 'inc/pg_utiles/my_pg_query.php';
require_once 'class/exception/pgQueryException.php';

function db_err($conn = null) {
    //die(pg_last_error($conn));
    die('FIN');
}

Kint::dump(true);

//echo pg_create_connexion_string($pg_db);
// Connexion à la BDD
$conn = pg_connect(pg_create_connexion_string($pg_db));

if (!$conn) {
    db_err($conn);
} else {
    // Exécution de la requête SQL
    //$query = 'SELECT * FROM personne';
    $query = "SELECT login('admin@bar.com', 'azerty')";
    //$query = "SELECT ARRAY[1,2.5,3,4]";
    //$query = "SELECT ARRAY['aze', 'rty', 'uio']";
    //$result = pg_query($conn, $query);
    try {
        //echo my_pg_query($conn, $query);
        kint::dump(my_pg_query($conn, $query));
        
        kint::dump(my_pg_query($conn, 'SELECT * FROM personne'));
    } catch (\exception\pgQueryException $e){
        echo $e->getErrorMessage();
        //echo $e;
    }
//    pg_send_query($conn, $query);
//
//    $result = pg_get_result($conn);
//    echo '\'' . pg_result_error_field($result, PGSQL_DIAG_MESSAGE_PRIMARY) . '\'<br/>';
//    echo '\'' . pg_result_error($result) . '\'<br/>';
//
//
//    if ($result) {
//        /*
//          // Affichage des résultats en HTML
//          echo "<table>\n";
//          while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
//          echo "\t<tr>\n";
//          foreach ($line as $col_value) {
//          echo "\t\t<td>$col_value</td>\n";
//          }
//          echo "\t</tr>\n";
//          }
//          echo "</table>\n"; */
//
//        // while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
//        //while ($line = pg_fetch_object($result)) {
//        //while ($line = pg_fetch_assoc($result)) {
//        while ($line = my_pg_convert_result($result)) {
//            Kint::dump($line);
//        }
//
//        // Libère le résultat
//        pg_free_result($result);
//    } else {
//        db_err($conn);
//    }
}

pg_close($conn);

// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);