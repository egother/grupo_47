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
		$publicaciones = array(); //aca va el arreglo con la consulta sql de las ultimas publicaciones
		echo $this->twig->render('layoutBackUser.twig.html', array('usuario' => dameUsuarioYRol(),
																   'mensaje' => $this->msj,
																   'publicaciones'=> $publicaciones,
																   
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
	
	public function modificarUsuario()
	{
	print_r($_SESSION['USUARIO']);	
		
	/*	if (isset($_GET['id'])) {
			$id = $this->xss($_GET['id']);
			$params = array('users' => $this->us->listarPorId($id));
			if ($params==-1){
				$this->setMensaje("Error al eliminar el usuario.");
				header('Location: ./backend.php?accion=users');
			}
		} else {
			$this->setMensaje("Error al entrar a la opción de eliminación de usuario.");
			header('Location: ./backend.php?accion=users');
		} */
		$params = array('users' => $this->us->listarPorId($_SESSION['USUARIO']['id']));
		
		print_r($params);
		
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$nombre = $this->xss($_POST['nombre']);
			//$rol = $this->xss($_POST['rol']);
			$p1 = $this->xss($_POST['p1']);
			$this->us->modificar($_SESSION['USUARIO']['id'], $nombre, $rol, $p1);
			$this->setMensaje("Usuario modificado con éxito.");
			//header('Location: ./backend.php?accion=users');
		} else {
			echo $this->twig->render('formModUser.twig.html', array('users' => $params['users'], 'usuario' => dameUsuarioYRol(), 'mensaje' => $this->msj));
		}
	}
		
 }
?>
