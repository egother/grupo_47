<?php
 // web/backend.php
require_once  '../app/twig/lib/Twig/Autoloader.php';
 // carga del modelo
 require_once '../app/Config.php';
 require_once '../app/models/Model.php';
 require_once '../app/models/ModelLogin.php';
 require_once '../app/models/ModelUsers.php';
 require_once '../app/models/Permisos.php';

 require_once '../app/models/ModelPublicacion.php';
 require_once '../app/models/ModelSolicitud.php';
 require_once '../app/models/ModelPago.php';
 require_once '../app/models/ModelReserva.php';
 require_once '../app/models/ModelCalificacionHuesped.php';
 require_once '../app/models/ModelCalificacionHospedado.php';
 require_once '../app/models/ModelComentario.php';
 require_once '../app/models/ModelTipoHospedaje.php';
 require_once '../app/models/ModelComentario.php';


 // carga de los controladores
 require_once '../app/controllers/Controller.php';
 require_once '../app/controllers/ControllerFront.php';
 require_once '../app/controllers/ControllerBack.php';
 require_once '../app/controllers/ControllerLogin.php';

// enrutamiento
$map = array(
     'inicio' => array('controller' =>'ControllerBack', 'accion' =>'inicio'),

     'quienesSomos' => array('controller' =>'ControllerBack', 'accion' =>'quienesSomos'),
     'comoFunciona' => array('controller' =>'ControllerBack', 'accion' =>'comoFunciona'),
     'publicar' => array('controller' =>'ControllerBack', 'accion' =>'publicar'),
     'misPublicaciones' => array('controller' =>'ControllerBack', 'accion' =>'misPublicaciones'),
     'reservasAceptadas' => array('controller' =>'ControllerBack', 'accion' =>'reservasAceptadas'),
     'reservasOtorgadas' => array('controller' =>'ControllerBack', 'accion' =>'reservasOtorgadas'),
     'solicitudesPendientes' => array('controller' =>'ControllerBack', 'accion' =>'solicitudesPendientes'),
     'solicitudesRealizadas' => array('controller' =>'ControllerBack', 'accion' =>'solicitudesRealizadas'),
	 'modificarPublicacion' => array('controller' => 'ControllerBack', 'accion' => 'modificarPublicacion'),
	 'eliminarPublicacion' => array('controller' => 'ControllerBack', 'accion' => 'eliminarPublicacion'),
     'departamentos' => array('controller' => 'ControllerBack', 'accion' => 'listarLocalidadesDeProvincia'),
	 'buscar' => array('controller' => 'ControllerBack', 'accion' => 'buscar'),
	 'aceptarSolicitud' => array('controller' => 'ControllerBack', 'accion' => 'aceptarSolicitud'),
	 'rechazarSolicitud' => array('controller' => 'ControllerBack', 'accion' => 'rechazarSolicitud'),
	 'borrarSolicitud' => array('controller' => 'ControllerBack', 'accion' => 'borrarSolicitud'),



     'usuarioPremium' => array('controller' =>'ControllerBack', 'accion' =>'usuarioPremium'),
     'pagar' => array('controller' =>'ControllerBack', 'accion' =>'pagar'),
     'agregarPago' => array('controller' =>'ControllerBack', 'accion' =>'agregarPago'),
	 'verPublicacion'=> array('controller'=>'ControllerBack', 'accion'=>'verPublicacion'),
   'agregarComentario'=> array('controller'=>'ControllerBack', 'accion'=>'agregarComentario'),
   'responderComentario'=> array('controller'=>'ControllerBack', 'accion'=>'responderComentario'),
   'responder'=> array('controller'=>'ControllerBack', 'accion'=>'responder'),


     'users' => array('controller' =>'ControllerBack', 'accion' =>'users'),
     'modificarUsuario' => array('controller' =>'ControllerBack', 'accion' =>'modificarUsuario'),
	 'modificarPass' => array('controller' =>'ControllerBack', 'accion' =>'modificarPass'),
     'borrarUsuario' => array('controller' =>'ControllerBack', 'accion' =>'borrarUsuario'),
     'mostrarConfiguracion' => array('controller' => 'ControllerBack', 'accion' => 'mostrarConfiguracion'),
     'modificarConfiguracion' => array('controller' => 'ControllerBack', 'accion' => 'modificarConfiguracion'),


	 'tipos' => array('controller' => 'ControllerBack', 'accion' => 'tipos'),
	 'agregarTipo' => array('controller' => 'ControllerBack', 'accion' => 'agregarTipo'),
	 'modificarTipo' => array('controller' => 'ControllerBack', 'accion' => 'modificarTipo'),
	 'borrarTipo' => array('controller' => 'ControllerBack', 'accion' => 'borrarTipo'),

	 'lugares' => array('controller' => 'ControllerBack', 'accion' => 'lugares'),
	 'agregarLugar' => array('controller' => 'ControllerBack', 'accion' => 'agregarLugar'),
	 'modificarLugar' => array('controller' => 'ControllerBack', 'accion' => 'modificarLugar'),
	 'borrarLugar' => array('controller' => 'ControllerBack', 'accion' => 'borrarLugar')

);

@session_start();



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
 if (isset($_GET['id'])) {
	$id = $_GET['id'];
 } else {
     $id = -1;
 }

 $controlador = $map[$ruta];
 // Ejecución del controlador asociado a la ruta

 if (method_exists($controlador['controller'],$controlador['accion'])) {
    call_user_func(array(new $controlador['controller'], $controlador['accion']), $id);
 } else {
		Controller::exepciones('','ERROR 404: No existe el controlador ', $controlador['accion']);

 }
}
