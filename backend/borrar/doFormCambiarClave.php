<?php
require_once __DIR__ . '/common.php';
require_once __DIR__ . '/commonDB.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/sendGridMail.php';


$sqlID = "SELECT correo FROM apoderado WHERE correo=?";
$exist = $db->GetRow($sqlID, $_POST['email']);

if ($exist && $exist['correo']) {
	$time = date('dmy') . date('h') + 1 . date('is');

	$random_string = "";
	for ($i = 1; $i <= 15; $i++) {
		$random_string = $random_string . chr(rand(65, 90));
	}
	$encrypt = $_POST['email'] . $random_string . $time;

	$cr = openssl_encrypt($encrypt, $CRYPT_CYP, $CRYPT_PWD, 0, $CRYPT_IV);

	$hour = date('h') + 2;
	$fecha_limite = date('Y-m-d') . ' ' . $hour . ':' . date('i:s');

	$sqlInsert = "INSERT INTO apoderado_cambiar_pass 
					(codigo, fecha_limite, correo, usado) 
					values 
					('" . $cr . "','" . $fecha_limite . "','" . $_POST['email'] .  "','" . 0 . "') ";
	$res = $db->execute($sqlInsert);

	if (!$res) {
		echo "Ocurrió un error, verifica tu correo y vuelve a intentarlo.";
	} else {
		echo sendGridMail(
			'Recuperación de clave',
			[$_POST['email'] => $_POST['email']],
			'text/html',
			'<strong>Recupera tu clave en el siguiente enlace:</strong><br>
							https://appediatra.aplicacionesweb.dev/frontend/#/NuevaClave?id=' . $cr . '<br>
						  <strong>Este enlace es válido durante 60 minutos y es de un solo uso. Por tu seguridad no lo compartas con nadie.</strong>'
		);
	}
} else {
	echo "Ocurrió un error, verifica tu correo y vuelve a intentarlo.";
}
