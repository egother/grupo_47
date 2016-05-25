<?php
session_start();

require_once './Config.php';
require_once './models/Model.php';
require_once './models/ModelLogin.php';
require_once './controllers/ControllerLogin.php';


if ($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$usuario= Controller::xss($_POST["usuario"]);
	$pass= Controller::xss($_POST["pass"]);

	$intentoLogin = ModelLogin::consultar($usuario,$pass);
	
	if ($intentoLogin == 0)	{
		//echo falló en la autenticación de usuario
		header('Location: ../web/index.php?accion=inicioErr');
    }
	else{
		//login correcto. Se asignan variables de sesiones.
		$_SESSION['USUARIO']['usuario'] = Controller::xss($_POST["usuario"]);
		$_SESSION['USUARIO']['nombre'] = Controller::xss($intentoLogin[0]['nombre']);
		$_SESSION['USUARIO']['nombreRol'] = Controller::xss($intentoLogin[0]['nombreRol']);
		$_SESSION['USUARIO']['id'] = Controller::xss($intentoLogin[0]['id']);
		
		header('Location: ../web/backend.php');
	
	}
}
	
?>
