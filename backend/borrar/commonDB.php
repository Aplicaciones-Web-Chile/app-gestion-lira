<?php

    putenv("NLS_LANG=AMERICAN.utf8");// dejar en american porque en el frontend tengo que formatear y calcular...
    require_once __DIR__.'/config.php';
    require_once __DIR__.'/adodb5.22/adodb.inc.php';

    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    set_time_limit(300);

    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $db = newAdoConnection($DB_DRIVER);
    $db->connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME  );

?>