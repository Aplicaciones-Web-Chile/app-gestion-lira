<?php
    require_once __DIR__.'/common.php';
    $_REQUEST = (isset($_POST) && is_array($_POST)) ? $_POST : $_GET;

    if( !isset($_REQUEST['kven']) )
        die();

    require_once __DIR__.'/commonDB.php';

$sql = "DELETE FROM crecimiento
         WHERE ID=".$_REQUEST['kven'];
 
$deleted = $db->execute($sql);

echo json_encode( array('result' => $deleted, 'sql' => $sql) ,JSON_INVALID_UTF8_IGNORE);