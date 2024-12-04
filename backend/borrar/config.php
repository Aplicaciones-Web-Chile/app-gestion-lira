<?php
    $DB_DRIVER='mysqli';
    $DB_HOST='';
    $DB_NAME='appPediatras';
    $DB_USER='appweb';
    $DB_PASSWORD='_AplicacionesWeb_';
	$CRYPT_IV='1234567891011121';
	$CRYPT_CYP='AES-128-CTR';
	$CRYPT_PWD=intdiv( time()/(60*60*24) , 2 );	//entre 1 y 2 días...
    //$ROOT="/";
?>