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

 
 // carga de los controladores
 require_once '../app/controllers/Controller.php';
 require_once '../app/controllers/ControllerFront.php';
 require_once '../app/controllers/ControllerBack.php';
 require_once '../app/controllers/ControllerLogin.php';

// enrutamiento
$map = array(
     'inicio' => array('controller' =>'ControllerBack', 'accion' =>'inicio'),
	 
	 'listarAlimentosSoloStock' => array('controller' =>'ControllerBack', 'accion' =>'listarAlimentosSoloStock'),
     'listarDonantes' => array('controller' =>'ControllerBack', 'accion' =>'listarDonantes'),
     'altaDonante' => array('controller' =>'ControllerBack', 'accion' =>'altaDonante'),
     'modificarDonante' => array('controller' =>'ControllerBack', 'accion' =>'modificarDonante'),
     'bajaDonante' => array('controller' =>'ControllerBack', 'accion' =>'bajaDonante'),

     'listarEntidades' => array('controller' =>'ControllerBack', 'accion' =>'listarEntidades'),
     'altaEntidad' => array('controller' =>'ControllerBack', 'accion' =>'altaEntidad'),
     'modificarEntidad' => array('controller' =>'ControllerBack', 'accion' =>'modificarEntidad'),
     'bajaEntidad' => array('controller' =>'ControllerBack', 'accion' =>'bajaEntidad'),

     'listarAlimentos' => array('controller' =>'ControllerBack', 'accion' =>'listarAlimentos'),
     'altaAlimento' => array('controller' =>'ControllerBack', 'accion' =>'altaAlimento'),
     'modificarAlimento' => array('controller' =>'ControllerBack', 'accion' =>'modificarAlimento'),
     'altaDonacion' => array('controller' =>'ControllerBack', 'accion' =>'altaDonacion'),
     'bajaAlimento' => array('controller' =>'ControllerBack', 'accion' =>'bajaAlimento'),

     'quienesSomos' => array('controller' =>'ControllerBack', 'accion' =>'quienesSomos'),
     'contacto' => array('controller' =>'ControllerBack', 'accion' =>'contacto'),
     
     'users' => array('controller' =>'ControllerBack', 'accion' =>'users'),
     'modificarUsuario' => array('controller' =>'ControllerBack', 'accion' =>'modificarUsuario'),
     'altaUsuario' => array('controller' =>'ControllerBack', 'accion' =>'altaUsuario'),
     'insertarUsuario' => array('controller' =>'ControllerBack', 'accion' =>'insertarUsuario'),
     'borrarUsuario' => array('controller' =>'ControllerBack', 'accion' =>'borrarUsuario'),
     'mostrarConfiguracion' => array('controller' => 'ControllerBack', 'accion' => 'mostrarConfiguracion'),
     'modificarConfiguracion' => array('controller' => 'ControllerBack', 'accion' => 'modificarConfiguracion'),
	 
	 'generarPedido' => array('controller' => 'ControllerBack', 'accion' => 'generarPedido'),
	 'actualizarPedido' => array('controller' => 'ControllerBack', 'accion' => 'actualizarPedido'),
	 'mostrarPedidos' => array('controller' => 'ControllerBack', 'accion' => 'mostrarPedidos'),
	 'generarEntrega' => array('controller' => 'ControllerBack', 'accion' => 'generarEntrega'),
	 'verEntregasRealizadas' => array('controller' => 'ControllerBack', 'accion' => 'verEntregasRealizadas'),
	 'mostrarAgenda' => array('controller' => 'ControllerBack', 'accion' => 'mostrarAgenda'),
	 'pedidosConEnvio' => array('controller' => 'ControllerBack', 'accion' => 'pedidosConEnvio'),
	 'entregaDirecta' => array('controller' => 'ControllerBack', 'accion' => 'entregaDirecta'),
	 'borrarDetallePedido' => array('controller' => 'ControllerBack', 'accion' => 'borrarDetallePedido'),
	 
	 'informePorER' => array('controller' => 'ControllerBack', 'accion' => 'informePorER'),
	 'entreFechas' => array('controller' => 'ControllerBack', 'accion' => 'entreFechas'),
	 'alimentosVencidos' => array('controller' => 'ControllerBack', 'accion' => 'alimentosVencidos'),
	 'mostrarEnvio' => array('controller' => 'ControllerBack', 'accion' => 'mostrarEnvio'),
	 	 
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
