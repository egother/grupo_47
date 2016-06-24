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
		if ((isset($_GET['tipo'])) && (isset($_GET['provincia']))){
			$tipo = $this->xss($_GET['tipo']);
			$provincia = $this->xss($_GET['provincia']);
			$publicaciones = $this->mPubli->buscar($tipo, $provincia);
			$busqueda = 1;
		} else {
			$publicaciones = $this->mPubli->listarPublicacion();
			$busqueda = 0;
		}
		echo $this->twig->render('layoutBackUser.twig.html', array( 'mensaje' => $this->msj,
																	'publicaciones' => $publicaciones,
																	// idUser verifica si cada publicacion es del usuario activo, cosa que no la pueda solicitar
																	'idUser' => $_SESSION['USUARIO']['id'],
																	'provincias' => $this->mLugares->listarProvincias(),
																	'tipos' => $this->mTipos->listar(),
																	'busqueda' => $busqueda,
																	'inicio' => '1')); // habilita la visualizacion de las ultimas publicaciones
    }

	public function tipos()
	{
		$this->revisarMensajes();
		$msj=$this->msj;
		$nom = ''; $idTipo='0';
		if (isset($_GET['id'])){
			$idTipo = $_GET['id'];}
		if (isset($_GET['func'])) {
			$func = $_GET['func'];
			if($func=='modificar'){
				$nom = $this->mTipos->obtenerTipo($idTipo);
				$nom = $nom['tipo'];
			}
		} else {
			$func = 'nada';
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{	$nombre = $_POST['nombre'];
			if ($func == 'agregar'){
				if ($this->mTipos->verificar($nombre)){ // verifica que ya no se haya agregado el mismo nombre
					$this->mTipos->agregar($nombre);
					$msj=("El tipo de hospedaje se ha agregado exitosamente");
				} else {
					$msj=("El tipo de hospedaje ya se encuentra registrado");
				}
			} elseif ($func == 'modificar'){
				if ($this->mTipos->verificar($nombre)){
					$this->mTipos->modificarTipo($idTipo,$nombre);
					$this->setMensaje("El tipo de hospedaje se ha modificado exitosamente");
					header('Location: ./backend.php?accion=tipos');
				}
				else {
					$this->setMensaje=("El tipo de hospedaje ya se encuentra registrado");
					header('Location: ./backend.php?accion=tipos');
				}
			}
		}
		$params = $this->mTipos->listar();
		echo $this->twig->render('listadoTiposHospedaje.twig.html', array('usuario' => dameUsuarioYRol(),
																		  'func' => $func,
																		  'tipos' => $params,
																		  'mensaje' => $msj,
																		  'nom' => $nom,
																		  'id_tipo' => $idTipo
																		  ));
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

	public function modificarPass(){

		$params = array('users' => $this->us->listarUsuario($_SESSION['USUARIO']['usuario']));

		if ($_SERVER['REQUEST_METHOD'] == 'POST'){

			$id = $params['users'][0]['id'];
			$p1 = $this->xss($_POST['p1']);

			$this->us->modificarPass($id, $p1);
			$this->setMensaje("Contraseña modificada con éxito.");
			header('Location: ./backend.php');


		}else
			echo $this->twig->render('formModPass.twig.html', array('users' => $params['users']));


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
		$msj = $this->revisarMensajes();
		if($this->haySesion()){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $usuario = $_SESSION['USUARIO']['id'];
        $monto = 300;
        $nombre = $_POST['nombre'];
        $numero = $_POST['numero'];
        $this->mPagos->agregarPago($usuario,$nombre,$numero, $monto);
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
	  $msj = $this->revisarMensajes();

	  if($this->haySesion()){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$tituloProp = $this->xss($_POST['tituloP']);
			$cantidad = $this->xss($_POST['capacidad']);
			$descripcion = $this->xss($_POST['descripcion']);
			$encabezado = $this->xss($_POST['encabezado']);
			$direccion = $this->xss($_POST['direccion']);
			$tipo = $this->xss($_POST['tipoViv']);
			$provincia = $this->xss($_POST['provincia']);
			$ciudad = $this->xss($_POST['ciudad']);
			$foto = $_FILES['imagen'];
			$usuario = $_SESSION['USUARIO']['id'];
			$this->mPubli->agregar($foto, $tituloProp, $cantidad, $descripcion, $encabezado, $direccion, $usuario, $tipo, $provincia, $ciudad);
			$msj="La Publicacion Fue Realizada";
			echo $this->twig->render('layoutBackUser.twig.html', array(
					'mensaje' => $msj,
			  'publicaciones' => $this->mPubli->listarPublicacion(),
			  'inicio' => '1'));
		  }else{
			$tipos = $this->mTipos->listar();
      $provincias = $this->mLugares->listarProvincias();
			echo $this->twig->render('publicacion.twig.html', array('log' => '1', 'tipos' => $tipos, 'provincias' => $provincias));
		  }
		}else{
		  echo $this->twig->render('publicacion.twig.html', array());
		}

	}

  public function listarLocalidadesDeProvincia(){
    $listado = $this->mLugares->listarLocalidadesDeProvincia($_POST['id']);
    header('Content-type: application/json');
    echo json_encode($listado);
  }

  public function verPublicacion(){
		$msj = $this->revisarMensajes();
		$func="";
		$source = 0;
		if (isset($_GET['id'])){
			$id = $this->xss($_GET['id']);
			$params = $this->mPubli->verPublicacion($id);
			if (!($params)==null){
				// $hoy se pasa por parametro para delimitar el campo de fecha "desde"
				$hoy = new DateTime('tomorrow');
				$hoy = $hoy->format('Y-m-d');
				if (isset($_GET['source'])){
					$source = ($_GET['source']);
				}
				if (isset($_GET['func'])){
					$func = $_GET['func'];
					if ($func == "solicitar"){
						if ($params['usuario']==$_SESSION['USUARIO']['id']){
							$this->setMensaje("Usted no puede auto-solicitarse hospedaje");
							header('Location: ./backend.php');
						}
					}
				}
				elseif ($_SERVER['REQUEST_METHOD'] == 'POST'){
					// se accedió a la publicacion a traves de un $id y por formulario de solicitud
					$cant = $_POST["cant"];
					$desde = $_POST["desde"];
					$hasta = $_POST["hasta"];
					$texto = $_POST["texto"];
					if (($cant>0) && ($cant<=$params['capacidad']) && ($this->check_dates($desde, $hasta))){
						$this->mSolic->agregarSolicitud($id, $_SESSION['USUARIO']['id'], $cant, $desde, $hasta, $texto);
						$this->setMensaje("La solicitud fue ingresada correctamente.");
						// se agrego bien, ahora mostramos el listado de las solicitudes que hice yo
						header('Location: ./backend.php?accion=solicitudesRealizadas');
					} else {
						$msj = "Los datos ingresados no son correctos.";
					}
				}
			} else {
				$this->setMensaje("No existe la publicación buscada.");
				header('Location: ./backend.php');
			}
		} else
			$this->setMensaje("No se seleccionó una publicacion para visualizar");
		echo $this->twig->render('verPublicacion.twig.html', array('log'=>'1',
																   'params' => $params,
																   'mensaje' => $this->revisarMensajes(),
																   'hoy' => $hoy,
																   'func' => $func,
																   'source' => $source));
	}

	public function misPublicaciones(){
		$msj = $this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mPubli->verMisPublicaciones($_SESSION['USUARIO']['id']);
			echo $this->twig->render('misPublicaciones.twig.html', array('log' => '1',
																		 'params' => $params,
																		 'mensaje' => $msj));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.");
			header('Location: ./index.php');
		}
	}

	public function solicitudesPendientes(){
		$msj = $this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mSolic->verSolicitudesPendientes($_SESSION['USUARIO']['id']);
			echo $this->twig->render('listadoSolicitudesPendientes.twig.html', array('log' => '1',
																					 'params' => $params,
																					 'mensaje' => $msj));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.");
			header('Location: ./index.php');
		}
	}

	public function solicitudesRealizadas(){
		$msj = $this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mSolic->verSolicitudesRealizadas($_SESSION['USUARIO']['id']);
			echo $this->twig->render('listadoSolicitudesRealizadas.twig.html', array('log' => '1',
																					 'params' => $params,
																					 'mensaje' => $msj));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.");
			header('Location: ./index.php');
		}
	}

	public function lugares(){
		echo "muestra una lista de lugares disponibles para que los usuarios ubiquen sus publicaciones";
	}

	public function misReservas(){
		echo "muestra las solicitudes que me fueron aprobadas como reservas";
	}

	public function reservasAceptadas(){
		echo "muestra las reservas que me aceptaron, ya puedo viajar";
	}

	public function reservasOtorgadas(){
		echo "muestra las reservas que otorgué, voy a tener huéspedes";
	}

	public function modificarPublicacion($id){
		echo "aca vamos a mostrar el formulario de modificar publicacion";
	}
 }

?>
