<?php
@session_start(); 

 class ControllerFront extends Controller
 {
	
	public function __construct()
	{
		parent::__construct('publico');
	}
 
	public function inicio()
     {
		$this->revisarMensajes();

		if($this->haySesion()){
			echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $this->msj));
			}
		else{
			echo $this->twig->render('index.twig.html', array('mensaje' => $this->msj));}
		
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
		$msj="";
		if($this->haySesion()){
			$msj=("Cierre la sesión actual para registrarse como nuevo usuario");
			echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $msj));
		}
		elseif (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['p1'])) && (isset($_POST['p2'])))
			{
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
						header('Location: ./index.php');
					}
					else {
						$params = array(
							'usuario' => $_POST['usuario'],
							'nombre' => $_POST['nombre'],
							'mail' => $_POST['mail'],
							'telefono' => $_POST['telefono'],
							'fecha' => $_POST['fecha'],
							);
						// mostrar mensaje, lo hiciste mal, llenalo de nuevo
						$msj=("El usuario y/o correo elegidos ya han sido registrados");
					}
				} else {
					$params = array(
						'usuario' => $_POST['usuario'],
						'nombre' => $_POST['nombre'],
						'mail' => $_POST['mail'],
						'telefono' => $_POST['telefono'],
						'fecha' => $_POST['fecha'],
						);//prueba
					$msj=("Las contraseñas ingresadas no coinciden");
				}
			} else $params = array(
						'usuario' => '',
						'nombre' => '',
						'mail' => '',
						'telefono' => '',
						'fecha' => ''
					);
		echo $this->twig->render('registro.twig.html', array('params' => $params,
															 'mensaje' => $msj));
	}
 }
 
?>