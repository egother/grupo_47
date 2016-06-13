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
		//$publicaciones = $this->mPubli->listarPublicacionUsuario($_SESSION['usuarios']); //aca va el arreglo con la consulta sql de las ultimas publicaciones

    echo $this->twig->render('layoutBackUser.twig.html', array(
			'mensaje' => $this->msj,
      'publicaciones' => $this->mPubli->listarPublicacion(),
			//'publicaciones'=> $publicaciones,
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
<<<<<<< HEAD

	public function modificarTipo(){

	}

}
=======
	
	public function modificarUsuario()
	{
	
		
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
		
		
		$params = array('users' => $this->us->listarUsuario($_SESSION['USUARIO']['usuario']));
		
		//print_r($params);
		
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			
			$nombre = $this->xss($_POST['nombre']);
			
			
			//$p1 = $this->xss($_POST['p1']);
			
			//printf("Antes consulta");
			$this->us->modificar(($_SESSION['USUARIO']['usuario']), $nombre);
			
			$this->setMensaje("Usuario modificado con éxito.");
			
			//header('Location: ./backend.php');
		} else {
			echo $this->twig->render('formModUser.twig.html', array('users' => $params['users'], 'usuario' => dameUsuarioYRol(), 'mensaje' => $this->msj));
		}
	}
		
 }
>>>>>>> e81c2034cf78d6f5ea6bd7308580a415ace87c51
?>
