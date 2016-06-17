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
	
	public function modificarTipo()
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
			
		}
	}
	
	
	public function modificarUsuario()
	{
		$params = array('users' => $this->us->listarUsuario($_SESSION['USUARIO']['usuario']));
		$edad = date("Y-m-d", strtotime("-18 years")); // guarda la fecha de hace 18 años para comprobar la mayoría de edad
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){

			$id = $params['users'][0]['id'];
			$nombre = $this->xss($_POST['nombre']);
			$tel = $this->xss($_POST['tel']);
			$fecha = $this->xss($_POST['fecha']);
			$mail = $this->xss($_POST['mail']);

			$this->us->modificar($id, $nombre, $tel, $fecha, $mail);
			
			$this->setMensaje("Usuario modificado con éxito.");
			
			$_SESSION['USUARIO']['nombre'] = Controller::xss($nombre);

			
			header('Location: ./backend.php');

			
		} else 
			echo $this->twig->render('formModUser.twig.html', array('users' => $params['users'],
																	'usuario' => dameUsuarioYRol(),
																	'mensaje' => $this->msj,
																	'edad' => $edad));
		
	}
	
	public function usuarioPremium(){
		if($this->haySesion()){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			if ($_SESSION['USUARIO']['id']>7){
			  $msj="Usted ya es Usuario Premium!!!";
			  echo $this->twig->render('layoutBackUser.twig.html', array('log' => '1', 'mensaje' => $msj));
			}
			else {
			  echo $this->twig->render('pagoTarjeta.twig.html', array('log' => '1'));

			}
		  }
		  else{
			echo $this->twig->render('usuarioPremium.twig.html', array('log' => '1'));
		  }
		}
	  }

	public function pagar(){
		if(true){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			echo $this->twig->render('pagoTarjeta.twig.html', array('log' => '1', 'msj' => "El pago se realizo correctamente!"));
		  }
		  else{
			echo $this->twig->render('pagoTarjeta.twig.html', array('log' => '1'));
		  }
		}
		else{
		  $msj="Debe iniciar sesion para realizar esta accion";
		  echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $msj));
		}
	  }
	  
	  public function publicar()
	  {
		if($this->haySesion()){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$tituloProp = $this->xss($_POST['tituloP']);
			$cantidad = $this->xss($_POST['capacidad']);
			$descripcion = $this->xss($_POST['descripcion']);
			$encabezado = $this->xss($_POST['encabezado']);
			$direccion = $this->xss($_POST['direccion']);
			$tipo = $this->xss($_POST['tipoViv']);
			$lugar = $this->xss($_POST['lugar']);
			$foto = $_FILES['imagen'];
			$usuario = $_SESSION['USUARIO']['usuario'];
			$this->mPubli->agregar($foto, $tituloProp, $cantidad, $descripcion, $encabezado, $direccion, $usuario, $tipo, $lugar);
			$msj="La Publicacion Fue Realizada";
			echo $this->twig->render('layoutBackUser.twig.html', array(
					'mensaje' => $msj,
			  'publicaciones' => $this->mPubli->listarPublicacion(),
			  'inicio' => '1'));
		  }else{
			$tipos = $this->mTipos->listar();
			echo $this->twig->render('publicacion.twig.html', array('log' => '1', 'tipos' => $tipos));
		  }
		}else{
		  echo $this->twig->render('publicacion.twig.html', array());
		}

	}
	
	public function verPublicacion(){
		$msj = $this->revisarMensajes();
		$func="";
		if (isset($_GET['id'])){
			$id = $this->xss($_GET['id']);
			$params = $this->mPubli->verPublicacion($id);

			// $hoy se pasa por parametro para delimitar el campo de fecha "desde"
			$hoy = new DateTime('tomorrow');
			$hoy = $hoy->format('Y-m-d');
 			if (isset($_GET['func']))
				$func = $_GET['func'];
			elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
				// se accedió a la publicacion a traves de un $id y por formulario de solicitud
				
			}
		} else
			$this->setMensaje("No se seleccionó una publicacion para visualizar");
		echo $this->twig->render('verPublicacion.twig.html', array('log'=>'1',
																   'params' => $params,
																   'mensaje' => $msj,
																   'hoy' => $hoy,
																   'func' => $func));
	}
	
	public function misPublicaciones(){
		echo "muestra las publicaciones que son de mi usuario";
	}
	
	public function misSolicitudes(){
		echo "muestra las solicitudes que me hicieron otros usuarios";
	}
	
	public function solicitudesRealizadas(){
		echo "muestra las solicitudes que realicé y que todavía tengo pendiente de aceptación";
	}
	
	public function lugares(){
		echo "muestra una lista de lugares disponibles para que los usuarios ubiquen sus publicaciones";
	}
	
	public function misReservas(){
		echo "muestra las solicitudes que me fueron aprobadas como reservas";
	}
	
	public function misAlojamientos(){
		echo "muestra las reservas que tienen mis publicaciones, a futuro y las pasadas tambien";
	}
 }
 
?>
