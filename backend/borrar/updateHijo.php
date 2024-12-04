<?php

require_once __DIR__.'/common.php';
require_once __DIR__.'/commonDB.php';

$flag			=	true;


//apoderado_id
$rutapoderado = $db->getAll("select rut from apoderado where id=?",$_POST[0]['apoderado_id'] );
$rutapoderado = implode("",$rutapoderado[0]);

  //validamos que exista el rut
	$res = $db->getAll("select rut from hijo where apoderado_id=apoderado_id" );
  if( count($res) ){
      //ahora a guardar los datos de los hijos
      for($i = 0; $i < 31; $i++ ){
        if(!empty($_POST[$i]['rut'] )){
          $res2 = $db->execute('update hijo
          set nombre=?, apellidos=?, fecha_nacimiento=?, sexo=?, peso=?, altura=?, alergias=?
              where rut=?
          '
          , array( $_POST[$i]['nombre'], $_POST[$i]['apellidos'], $_POST[$i]['fecha_nacimiento'], $_POST[$i]['sexo'], $_POST[$i]['peso'], $_POST[$i]['altura'], $_POST[$i]['alergias'] 
                , $_POST[$i]['rut']
            )
          );
        }

      }

  
	  // echo json_encode( array('resultado' => $res2),JSON_INVALID_UTF8_IGNORE);
  }
?>