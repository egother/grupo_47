<?php
 // web/index.php

 // carga del modelo
 require_once '../app/Config.php';
 require_once '../app/models/Model.php';
 require_once '../app/models/ModelLogin.php';
 require_once '../app/models/ModelUsers.php';

 // carga de los controladores
 require_once '../app/controllers/Controller.php';
 require_once '../app/controllers/ControllerFront.php';
 require_once '../app/controllers/ControllerBack.php';
 require_once  '../app/twig/lib/Twig/Autoloader.php';


 // enrutamiento
   $map = array(
       'inicio' => array('controller' =>'ControllerFront', 'accion' =>'inicio'),
       'quienesSomos' => array('controller' =>'ControllerFront', 'accion' =>'quienesSomos'),
       'comoFunciona' => array('controller' =>'ControllerFront', 'accion' =>'comoFunciona'),
       'inicioErr' => array('controller' =>'ControllerFront', 'accion' =>'inicioErr'),
       'registrarse' => array('controller' =>'ControllerFront', 'accion' =>'registrarse'),
	   'recuperarPass' => array('controller' =>'ControllerFront', 'accion' =>'recuperarPass'),
	   'terminosYcondiciones' => array('controller' =>'ControllerFront', 'accion' =>'terminosYcondiciones')
  	);
$errors=false;
 // Parseo de la ruta
 if (isset($_GET['accion'])) {
     if (isset($map[$_GET['accion']])) {
         $ruta = $_GET['accion'];
     } else {

			Controller::exepciones('','ERROR 404: No existe la ruta ', $_GET['accion']);
				$errors=true;
     }
	} else {
     $ruta = 'inicio';
 }
if(!$errors){
 $controlador = $map[$ruta];
 // Ejecución del controlador asociado a la ruta
 if (method_exists($controlador['controller'],$controlador['accion'])) {
     call_user_func(array(new $controlador['controller'], $controlador['accion']));
 } else {

		Controller::exepciones('','ERROR 404: No existe el controlador ', $controlador['accion']);

 }
 }
?>
