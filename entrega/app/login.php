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
	
		$_SESSION['USUARIO']['userName']= Controller::xss($_POST["usuario"]);
		$_SESSION['USUARIO']['rol'] = Controller::xss($intentoLogin[0]['nombreRol']);
		$_SESSION['USUARIO']['id'] = Controller::xss($intentoLogin[0]['id']);
		
		//almacena las coordenadas del banco de alimentos
		$coordenadas= ModelLogin::obtenerCoordenadas();
		
		$lat= $coordenadas[0]['valor'];
		$lon= $coordenadas[1]['valor'];
		
		$_SESSION['USUARIO']['lat']= $lat;
		$_SESSION['USUARIO']['lon']= $lon;

		header('Location: ../web/backend.php');
	
	}
}
	
?>
