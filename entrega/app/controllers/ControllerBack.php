﻿<?php

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
			$paramsBusqueda = array('prov' => $this->mLugares->verProvincia($_GET['provincia']),
									'tipo' => $this->mTipos->obtenerTipo($_GET['tipo']));
		} else {
			$publicaciones = $this->mPubli->listarPublicacion();
			$busqueda = 0;
			$paramsBusqueda = null;
		}
		echo $this->twig->render('layoutBackUser.twig.html', array( 'mensaje' => $this->msj,
																	'error' => $this->err,
																	'publicaciones' => $publicaciones,
																	// idUser verifica si cada publicacion es del usuario activo, cosa que no la pueda solicitar
																	'idUser' => $_SESSION['USUARIO']['id'],
																	'provincias' => $this->mLugares->listarProvincias(),
																	'tipos' => $this->mTipos->listar(),
																	'busqueda' => $busqueda,
																	'paramsBusqueda' => $paramsBusqueda,
																	'inicio' => '1')); // habilita la visualizacion de las ultimas publicaciones
    }

	public function tipos()
	{
		$this->revisarMensajes();
		$msj=$this->msj;
		$err=$this->err;
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
					$err=0;
				} else {
					$msj=("El tipo de hospedaje ya se encuentra registrado");
					$err=1;
				}
			} elseif ($func == 'modificar'){
				if ($this->mTipos->verificar($nombre)){
					$this->mTipos->modificarTipo($idTipo,$nombre);
					$msj=("El tipo de hospedaje se ha modificado exitosamente");
					$err=0; $func = 'nada';
				}
				else {
					$msj=("El tipo de hospedaje ya se encuentra registrado");
					$err=1; $func = 'nada';
				}
			}
		}
		$params = $this->mTipos->listar();
		echo $this->twig->render('listadoTiposHospedaje.twig.html', array('usuario' => dameUsuarioYRol(),
																		  'func' => $func,
																		  'tipos' => $params,
																		  'mensaje' => $msj,
																		  'error' => $err,
																		  'nom' => $nom,
																		  'id_tipo' => $idTipo
																		  ));
	}


	public function modificarUsuario()
	{
		$this->revisarMensajes();
		$params = array('users' => $this->us->listarUsuario($_SESSION['USUARIO']['usuario']));
		$edad = date("Y-m-d", strtotime("-18 years")); // guarda la fecha de hace 18 años para comprobar la mayoría de edad

		if ($_SERVER['REQUEST_METHOD'] == 'POST'){

			$id = $params['users'][0]['id'];
			$nombre = $this->xss($_POST['nombre']);
			$tel = $this->xss($_POST['tel']);
			$fecha = $this->xss($_POST['fecha']);

			$this->us->modificar($id, $nombre, $tel, $fecha);

			$this->setMensaje("Usuario modificado con éxito.", 0);

			$_SESSION['USUARIO']['nombre'] = Controller::xss($nombre);


			header('Location: ./backend.php');


		} else
			echo $this->twig->render('formModUser.twig.html', array('users' => $params['users'],
																	'usuario' => dameUsuarioYRol(),
																	'mensaje' => $this->msj,
																	'error' => $this->err,
																	'edad' => $edad));

	}

	public function modificarPass(){

		$params = array('users' => $this->us->listarUsuario($_SESSION['USUARIO']['usuario']));

		if ($_SERVER['REQUEST_METHOD'] == 'POST'){

			$id = $params['users'][0]['id'];
			$p1 = $this->xss($_POST['p1']);

			$this->us->modificarPass($id, $p1);
			$this->setMensaje("Contraseña modificada con éxito.", 0);
			header('Location: ./backend.php');


		}else
			echo $this->twig->render('formModPass.twig.html', array('users' => $params['users']));


	}

	public function usuarioPremium(){
		if($this->haySesion()){
		  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			if ($this->us->soyPremium($_SESSION['USUARIO']['id'])){
			  $msj="Usted ya es Usuario Premium!!!";
			  echo $this->twig->render('layoutBackUser.twig.html', array('log' => '1', 'mensaje' => $msj, 'error' => 1));
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
				$this->us->convertirPremium($usuario);
				echo $this->twig->render('pagoTarjeta.twig.html', array('log' => '1', 'mensaje' => "El pago se realizo correctamente!", 'error' => 0));
			}
			else{
				echo $this->twig->render('pagoTarjeta.twig.html', array('log' => '1'));
			}
		}
		else{
		  $msj="Debe iniciar sesion para realizar esta accion";
		  echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $msj, 'error' => 1));
		}
	  }

	public function publicar(){
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
				$this->setMensaje("La Publicacion Fue Realizada", 0);
				header('Location: ./backend?accion=misPublicaciones');
			} else {
				$tipos = $this->mTipos->listar();
				$provincias = $this->mLugares->listarProvincias();
				$ciudades = $this->mLugares->departamentos(1);
				echo $this->twig->render('publicacion.twig.html', array('log' => '1',
																		'form' => 'A', // A = Agregar
																		'tipos' => $tipos,
																		'provincias' => $provincias,
																		'ciudades' => $ciudades));
			}
		}else{
			$this->setMensaje("Usted no ha iniciado sesión", 1);
			header('Location: ./index.php');
		}
	}

	public function modificarPublicacion($id){ // no se cómo, pero recibe el id desde el $_GET
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
				if (! isset($_POST['habilitado'])) {
					$habilitado = 'A';
				}
				elseif ($_POST['habilitado'] = 'on'){
					$habilitado = 'B';
				}
				$res = $this->mPubli->modificar($id, $foto, $tituloProp, $cantidad, $descripcion,
							$encabezado, $direccion, $usuario, $tipo, $provincia, $ciudad, $habilitado);
				$this->setMensaje("La modificación fue realizada con éxito", 0);
				header('Location: ./backend.php?accion=misPublicaciones');
			} else {
				$params = $this->mPubli->verPublicacion($id);
				$tipos = $this->mTipos->listar();
				$provincias = $this->mLugares->listarProvincias();
				$ciudades = $this->mLugares->departamentos($params['provincia']);
				echo $this->twig->render('publicacion.twig.html', array('log' => '1',
																		'form' => 'M', // M = Modificar
																		'params' => $params,
																		'tipos' => $tipos,
																		'provincias' => $provincias,
																		'ciudades' => $ciudades));
			}
		}else{
			$this->setMensaje("Usted no ha iniciado sesión", 1);
			header('Location: ./index.php');
		}
	}

  public function listarLocalidadesDeProvincia(){
    $listado = $this->mLugares->departamentos($_POST['id']);
    header('Content-type: application/json');
    echo json_encode($listado);
  }

  public function agregarComentario(){
    $id=$_POST['idPublicacion'];
    $pregunta=$_POST['pregunta'];
    $usuario=$_POST['usuarioActual'];
    $this->mComent->agregarComentario($pregunta, $id, $usuario);
    $this->setMensaje("La Pregunta fue enviada", 0);
    header('Location: ./backend.php?accion=verPublicacion&id='.$id);
  }

  public function responderComentario(){
    if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
      $comentario = $this->mComent->unComentario($id);
      echo $this->twig->render('responderComentario.twig.html', array('log' => '1',
                                                            'comentario' => $comentario));
    }
  }

  public function responder(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
      $id=$_POST['idComentario'];
      $idP=$_POST['idPublicacion'];
      $respuesta=$_POST['respuesta'];
      $this->mComent->responder($id, $respuesta);
      $this->setMensaje("La respuesta fue enviada", 0);
      header('Location: ./backend.php?accion=verPublicacion&id='.$idP);

    }
  }

  public function misPreguntas(){
    $this->revisarMensajes();
		if($this->haySesion()){
			$comentarios = $this->mComent->comentariosDeUsuario($_SESSION['USUARIO']['id']);
			echo $this->twig->render('misPreguntas.twig.html', array('log' => '1',
																		 'comentarios' => $comentarios,
																		 'mensaje' => $this->msj,
																		 'error' => $this->err));
		}
  }

  public function preguntasAresponder(){
      $this->revisarMensajes();
  		if($this->haySesion()){
  			$comentarios = $this->mComent->preguntasAresponder($_SESSION['USUARIO']['id']);
  			echo $this->twig->render('misPreguntasAresponder.twig.html', array('log' => '1',
  																		 'comentarios' => $comentarios,
  																		 'mensaje' => $this->msj,
  																		 'error' => $this->err));
    }
  }

  public function verPublicacion(){
		$this->revisarMensajes();
		$func="";
		$source = 0;
		if (isset($_GET['id'])){
			$id = $this->xss($_GET['id']);
			$params = $this->mPubli->verPublicacion($id);
			$usuarioActual = $_SESSION['USUARIO']['id'];
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
							$this->setMensaje("Usted no puede auto-solicitarse hospedaje", 1);
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
					$ok = $this->mPubli->estaDisponible($desde, $hasta, $id);
					if (($ok) && ($cant>0) && ($cant<=$params['capacidad']) && ($this->check_dates($desde, $hasta))){
						$this->mSolic->agregarSolicitud($id, $_SESSION['USUARIO']['id'], $cant, $desde, $hasta, $texto);
						$this->setMensaje("La solicitud fue ingresada correctamente.", 0);
						// se agrego bien, ahora mostramos el listado de las solicitudes que hice yo
						header('Location: ./backend.php?accion=solicitudesRealizadas');
					} else {
						if (!($ok)){
							$this->msj = "Las fechas seleccionadas no están completamente disponibles.";
							$this->err = 1;
						} else {
							$this->msj = "Los datos ingresados no son correctos.";
							$this->err = 1;
						}
					}
				}
			} else {
				$this->setMensaje("No existe la publicación buscada.", 1);
				header('Location: ./backend.php');
			}
		} else
			$this->setMensaje("No se seleccionó una publicacion para visualizar", 1);
    $comentarios = $this->mComent->listarComentarios($id);
    //var_dump($comentarios);die;
		echo $this->twig->render('verPublicacion.twig.html', array('log'=>'1',
																   'params' => $params,
																   'mensaje' => $this->msj,
																   'error' => $this->err,
																   'hoy' => $hoy,
																   'func' => $func,
																   'source' => $source,
                                   'comentarios' => $comentarios,
                                    'usuarioActual' => $usuarioActual));
	}

	public function misPublicaciones(){
		$this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mPubli->verMisPublicaciones($_SESSION['USUARIO']['id']);
			echo $this->twig->render('misPublicaciones.twig.html', array('log' => '1',
																		 'params' => $params,
																		 'mensaje' => $this->msj,
																		 'error' => $this->err));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.", 1);
			header('Location: ./index.php');
		}
	}

	public function solicitudesPendientes(){
		$this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mSolic->verSolicitudesPendientes($_SESSION['USUARIO']['id'], 0);
			$detalle = 0;
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				// esto busca el comentario dentro de la lista de solicitudes que coincida con el id de la solicitud
				$key = array_search($id, array_column($params, 'id_solicitud'));
				if (is_bool($key) === false){
					$detalle = $params[$key];
				}
			}
			echo $this->twig->render('listadoSolicitudesPendientes.twig.html', array('log' => '1',
																					 'params' => $params,
																					 'mensaje' => $this->msj,
																					 'error' => $this->err,
																					 'detalle' => $detalle));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.", 1);
			header('Location: ./index.php');
		}
	}

	public function solicitudesRealizadas(){
		$this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mSolic->verSolicitudesRealizadas($_SESSION['USUARIO']['id']);
			$detalle = 0;
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				// esto busca el comentario dentro de la lista de solicitudes que coincida con el id de la solicitud
				$key = array_search($id, array_column($params, 'id_solicitud'));
				if (is_bool($key) === false){
					$detalle = $params[$key];
				}
			}
			echo $this->twig->render('listadoSolicitudesRealizadas.twig.html', array('log' => '1',
																					 'params' => $params,
																					 'mensaje' => $this->msj,
																					 'error' => $this->err,
																					 'detalle' => $detalle));
		} else {
			$this->setMensaje("Usted no ha iniciado sesión.", 1);
			header('Location: ./index.php');
		}
	}

	public function aceptarSolicitud(){
		if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
			$idPubli = $this->mSolic->verIdPublicacion($id);
			$miLista = $this->mSolic->verSolicitudesPendientes($_SESSION['USUARIO']['id'], $idPubli);
			$key = array_search($id, array_column($miLista, 'id_solicitud'));
// verificamos que el usuario actual sea dueño de la publicacion a la que pertenece la solicitud
			if ($key > -1){
				// verificamos cuales de la lista se cruzan en fecha con la solicitud
				$seCruzan = $this->seCruzanFechas($miLista, $key);
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					$this->mReser->agregar($miLista[$key]);
					$this->mSolic->descartar($seCruzan, $miLista[$key]);
					$this->setMensaje("La reserva ha sido concretada con éxito.", 0);
					header('Location: ./backend.php?accion=reservasAceptadas');
					return;
				} else {
					echo $this->twig->render('confirmarSolicitud.twig.html', array('log' => '1',
																				   'id' => $id,
																				   'func' => 'aceptar',
																				   'params' => $seCruzan,
																				   'solicitud' => $miLista[$key],
																				   'mensaje' => $this->msj,
																				   'error' => $this->err));
					return;
				}
			} else {
				$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
			}
		} else
			$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
		header('Location: ./backend.php?accion=solicitudesPendientes');
	}

	public function rechazarSolicitud(){
		if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
			$idPubli = $this->mSolic->verIdPublicacion($id);
			$miLista = $this->mSolic->verSolicitudesPendientes($_SESSION['USUARIO']['id'], $idPubli);
			$key = array_search($id, array_column($miLista, 'id_solicitud'));
// verificamos que el usuario actual sea dueño de la publicacion a la que pertenece la solicitud
			if ($key > -1){
				// verificamos cuales de la lista se cruzan en fecha con la solicitud
				$seCruzan = $this->seCruzanFechas($miLista, $key);
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					$this->mSolic->rechazar($miLista[$key]);
					$this->setMensaje("La solicitud se ha rechazado.", 0);
				} else {
					echo $this->twig->render('confirmarSolicitud.twig.html', array('log' => '1',
																				   'id' => $id,
																				   'func' => 'rechazar',
																				   'params' => $seCruzan,
																				   'solicitud' => $miLista[$key],
																				   'mensaje' => $this->msj,
																				   'error' => $this->err));
					return;
				}
			} else {
				$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
			}
		} else
			$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
		header('Location: ./backend.php?accion=solicitudesPendientes');
	}

	public function borrarSolicitud(){
		if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
			$miLista = $this->mSolic->verSolicitudesRealizadas($_SESSION['USUARIO']['id']);
			$key = array_search($id, array_column($miLista, 'id_solicitud'));
// verificamos que el usuario actual sea dueño de la publicacion a la que pertenece la solicitud
			if ($key > -1){
				// verificamos cuales de la lista se cruzan en fecha con la solicitud
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					$this->mSolic->borrar($miLista[$key]);
					$this->setMensaje("La solicitud se ha eliminado.", 0);
				} else {
					echo $this->twig->render('confirmarSolicitud.twig.html', array('log' => '1',
																				   'id' => $id,
																				   'func' => 'borrar',
																				   'params' => array(),
																				   'solicitud' => $miLista[$key],
																				   'mensaje' => $this->msj,
																				   'error' => $this->err));
					return;
				}
			} else {
				$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
			}
		} else
			$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
		header('Location: ./backend.php?accion=solicitudesRealizadas');
	}

	public function eliminarPublicacion(){
		if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
			$miLista = $this->mPubli->verMisPublicaciones($_SESSION['USUARIO']['id']);
			$key = array_search($id, array_column($miLista, 'id_publicacion'));
// verificamos que el usuario actual sea dueño de la publicacion a la que pertenece la solicitud
			if ($key > -1){
				// verificamos cuales de la lista se cruzan en fecha con la solicitud
				$solicitudes = $this->mSolic->verSolicitudesDePublicacion($id);
				$reservas = $this->mReser->verReservasDePublicacion($id);
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					$this->mSolic->descartar_solo($solicitudes);
					$this->mPubli->borrar($miLista[$key]);
					$this->setMensaje("La Publicación se ha deshabilitado correctamente.", 0);
				} else {
					echo $this->twig->render('confirmarPublicacion.twig.html', array('log' => '1',
																				   'id' => $id,
																				   'func' => 'borrar',
																				   'solicitudes' => $solicitudes,
																				   'reservas' => $reservas,
																				   'mensaje' => $this->msj,
																				   'error' => $this->err));
					return;
				}
			} else {
				$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
			}
		} else
			$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
		header('Location: ./backend.php?accion=misPublicaciones');
	}

	public function borrarTipo(){
		if (isset($_GET['id']) && $this->haySesion()){
			$id = $_GET['id'];
			$res = 0;
			$res = $this->mPubli->buscarPorTipo($id);
			if (sizeof($res)>0){
				$this->setMensaje("No se pudo eliminar. El Tipo de Hospedaje está en uso.", 1);
			} else {
				$this->mTipos->borrar($id);
				$this->setMensaje("El Tipo de Hospedaje fue eliminado correctamente.", 0);
			}
		} else {
			$this->setMensaje("Hubo un problema en el sistema. Reinténtelo.", 1);
		}
		header('Location: ./backend.php?accion=tipos');
	}

	public function misReservas(){
		echo "muestra las solicitudes que me fueron aprobadas como reservas";
	}

	public function reservasAceptadas(){
 		$this->revisarMensajes();
		if($this->haySesion()){
			//$aux = $this->mSolic->verIdSolicitud($_SESSION['USUARIO']['id']);
			$params = $this->mReser->verReservasAceptadas($_SESSION['USUARIO']['id']);

			echo $this->twig->render('listadoReservasAceptadas.twig.html', array('log' => '1',
																			 'params' => $params));

		}
	}

	public function reservasOtorgadas(){
 		$this->revisarMensajes();
		if($this->haySesion()){
			//$aux = $this->mPubli->verIdMisPublicaciones($_SESSION['USUARIO']['id']);
			$params = $this->mReser->verReservasOtorgadas($_SESSION['USUARIO']['id']);

			echo $this->twig->render('listadoReservasOtorgadas.twig.html', array('log' => '1',
																			'params' => $params));

		}
		 else {
			$this->setMensaje("Usted no ha iniciado sesión.", 1);
			header('Location: ./index.php');
		}
	}
	
	public function reservasConcretadas(){
		
		$this->revisarMensajes();
		if($this->haySesion()){
		$params = $this->mReser->verReservasConcretadas();
			
			echo $this->twig->render('listadoReservasConcretadas.twig.html', array('log' => '1',
																			'params' => $params));
				
			
			
		}
		
	}
	
	public function ganancia(){
		$this->revisarMensajes();
		if($this->haySesion()){
			$params = $this->mPagos->verGanancia();
			$tot = $this->mPagos->totalGanancia();
			
			
			echo $this->twig->render('listadoGanancia.twig.html', array('log' => '1',
																			'params' => $params,
																			'tot' => $tot['0']['valor']));
			
			
			
		}
		
	}


 }

?>
