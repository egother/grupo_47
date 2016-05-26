<?php

require_once __DIR__ . '/ControllerLogin.php';
require_once __DIR__ . '/Controller.php';

 class ControllerBack extends Controller
 {

	public function __construct()
	{		
		if (isset($_GET['accion'])) {
			parent::__construct($_GET['accion']);
		} else {
			parent::__construct('inicio');
		}
	}
	

	public function inicio()
     {
		$this->revisarMensajes();
		echo $this->twig->render('layoutBackUser.twig.html', array('usuario' => dameUsuarioYRol(),
																   'mensaje' => $this->msj,
																   'inicio' => '1')); // habilita la visualizacion de las ultimas publicaciones
     }
	 
	public function tipos()
	{
		$this->revisarMensajes();
		$msj=$this->msj;
		if (isset($_GET['func'])) {
			$func = $_GET['func'];
		} else {
			$func = 'nada';
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$nombre = $_POST['nombre'];
			if ($this->mTipos->verificar($nombre)){ // verifica que ya no se haya agregado el mismo nombre
				$this->mTipos->agregar($nombre);
				$msj=("El tipo de hospedaje se ha agregado exitosamente");
			} else {
				$msj=("El tipo de hospedaje ya se encuentra registrado");
			}
		}
		$params = $this->mTipos->listar();
		echo $this->twig->render('listadoTiposHospedaje.twig.html', array('usuario' => dameUsuarioYRol(),
																		  'func' => $func,
																		  'tipos' => $params,
																		  'mensaje' => $msj));
	}
	
	public function modificarUsuario(){
		
	}
	
}
?>
