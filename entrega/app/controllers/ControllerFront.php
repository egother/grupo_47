<?php
@session_start(); 

 class ControllerFront extends Controller
 {
	
	public function __construct()
	{
		parent::__construct('publico');
	}
 
	private function haySesion(){
	 	return ((isset($_SESSION['USUARIO']) && (isset($_SESSION['USUARIO']['rol'])) && (isset($_SESSION['USUARIO']['id']))));
	}
 
	public function inicio()
     {
		$this->revisarMensajes();

		if($this->haySesion()){
			echo $this->twig->render('index.twig.html', array('log' => '1'));
			}
		else{
			echo $this->twig->render('index.twig.html', array());}
		
     }
	 
	 public function inicioErr()
     {
		echo $this->twig->render('index.twig.html', array('mensaje' => 'Usuario o contraseña incorrectos.'));
     }
	 
    public function quienesSomos()
    {
		if($this->haySesion()){
			echo $this->twig->render('quienesSomos.twig.html', array('log' => '1'));}
		else{
         echo $this->twig->render('quienesSomos.twig.html', array());}
     }

	 public function comoFunciona()
    {
		if($this->haySesion()){
			echo $this->twig->render('comoFunciona.twig.html', array('log' => '1'));}
		else{
         echo $this->twig->render('comoFunciona.twig.html', array());}
     }
	 
	 // presenta la página con el formulario de registro
	 public function registrarse()
	 {
		$this->revisarMensajes();
		if($this->haySesion()){
			echo $this->twig->render('index.twig.html', array('log' => '1'));
			}
		else{
			$this->setMensaje("dale, registrate, gil");
			echo $this->twig->render('registro.twig.html', array());}
	 }

	// verifica si esta todo bien y da de alta el usuario cargado en el formulario
	 public function registrarUsuario()
	 {
		$this->revisarMensajes();

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['p1'])) && (isset($_POST['p2']))) {
			$p1 = $this->xss($_POST['p1']);
			$p2 = $this->xss($_POST['p2']);
			if ($p1==$p2) {
				$usuario = $this->xss($_POST['usuario']);
				$nombre = $this->xss($_POST['nombre']);
				$mail = $this->xss($_POST['mail']);
				$telefono = $this->xss($_POST['telefono']);
				$fecha = $this->xss($_POST['fecha']);
				// comprueba que el usuario no exista ya
				if (!$this->us->existeUsuario($usuario, $mail)){
					 $this->us->agregar($usuario, $nombre, $mail, $telefono, $p1, $fecha);
					 $this->setMensaje("El usuario se ha agregado correctamente.");
				}
				else { // mostrar mensaje, lo hiciste mal, llenalo de nuevo
					 $this->setMensaje("El usuario elegido ya existe");
				}
			} else $this->setMensaje("Las contraseñas dadas no son iguales");			
		}
		header('Location: ./index.php');
	 }

 }
 
?>