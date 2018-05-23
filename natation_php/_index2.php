<?php
// At start of script
$time_start = microtime(true); 


require_once __DIR__ . '/vendor/autoload.php';

require_once 'inc/log/err_exec_handler.php';

require_once 'inc/log/my_logger.php';


kint::dump(set_error_handler('_handler'));


try {
    $all_res = [];
    $dbh = new PDO('pgsql:host=localhost;dbname=natation', 'apache', 'apache');
    // On lance une exception lorsque la BDD envoie une erreur
    // voir: http://php.net/manual/fr/pdo.error-handling.php
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //$res = $dbh->query('SELECT * from FOO');
    //$res = $dbh->query("SELECT login('admin@bar.com', 'azert') AS login");
    $res = $dbh->query("SELECT * FROM personne");
    kint::dump($res);
    foreach($res as $row) {
        //print_r($row);
        $all_res[] = $row;
    }
    kint::dump($all_res);
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    kint::dump($e);
    throwable_log($e);
    die();
}

// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start);