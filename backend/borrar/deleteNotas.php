<?php
    require_once __DIR__.'/common.php';
    // $_REQUEST = (isset($_POST) && is_array($_POST)) ) ? $_POST : $_GET;

    if( !isset($_POST['kven']) )
        die();

    require_once __DIR__.'/commonDB.php';

$sql = "DELETE FROM notas
         WHERE ID=".$_POST['kven'];
 
$deleted = $db->execute($sql);

echo json_encode( array('result' => $deleted) ,JSON_INVALID_UTF8_IGNORE);