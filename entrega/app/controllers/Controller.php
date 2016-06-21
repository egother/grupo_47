<?php
@session_start();

require_once __DIR__ . '/ControllerLogin.php';

 class Controller
 {
	protected $twig; 	 // variable para las plantillas twig
	public $mPubli; 	 // variable para la conexión del modelo publicaciones
	protected $mSolic; 	 // variable para la conexión del modelo solicitudes
	protected $mReser; 	 // variable para la conexión del modelo reservas
	protected $us;		 // variable para la conexión del modelo usuarios
	protected $mPagos; 	 // variable para la conexion del modelo pagos
  protected $mLugares; // variable para la conexion del modelo lugares
	protected $mCalifHue;// variable para la conexion del modelo calificacion huesped
	protected $mCalifHos;// variable para la conexion del modelo calificacion hospedado
	protected $mComent;	 // variable para la conexion del modelo comentarios
	protected $mTipos;	 // variable para la conexion del modelo tipos de hospedaje
	protected $msj;

	//configura los parámetros de Twig para el controllerBack

	public function __construct($accion)
	{
    $this->twig = $this->configTwig();
    $this->twig->addGlobal('usuario', dameUsuarioYRol());
		include_once(__DIR__.'/../models/ModelPublicacion.php');
		$this->mPubli = new ModelPublicacion(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		include_once(__DIR__.'/../models/ModelTipoHospedaje.php');
    $this->mTipos = new ModelTipoHospedaje(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
           Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

    include_once(__DIR__.'/../models/ModelLogin.php');
    $this->us = new ModelLogin(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
            Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

    include_once(__DIR__.'/../models/ModelLugar.php');
    $this->mLugares = new ModelLugar(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
            Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if (Model::testConect()){ //si la conexión resulta exitosa

			if ($accion == 'publico') {
				$this->us = new ModelUsers(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
			}
			elseif (postaTengoPermiso($accion)) {
				$this->mSolic = new ModelSolicitud(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->mReser = new ModelReserva(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->us = new ModelUsers(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->mPagos = new ModelPago(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->mCalifHue = new ModelCalificacionHuesped(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->mCalifHos = new ModelCalificacionHospedado(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
				$this->mComent = new ModelComentario(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
        $this->mLugares = new ModelLugar(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
       					Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
			}
			else {
				$this->setMensaje("Usted no posee permisos para realizar dicha operación");
				if ($this->haySesion()){
					header('Location: ./backend.php');
				} else {
					header('Location: ./index.php');
				}

			}
		}
	}

	protected function haySesion(){
	 	return ((isset($_SESSION['USUARIO']) && (isset($_SESSION['USUARIO']['nombreRol'])) && (isset($_SESSION['USUARIO']['id']))));
	}


	protected function revisarMensajes()
	{
		if (isset($_COOKIE['mensaje'])){
			$this->msj = $_COOKIE['mensaje'];
			setcookie('mensaje', 'content', 1);
		}
	}

	protected function setMensaje($m)
	{
		setcookie('mensaje', $m);
	}

	private static function configTwig(){
		require_once __DIR__ . '/../twig/lib/Twig/Autoloader.php';
		Twig_Autoloader::register();

		$loader = new Twig_Loader_Filesystem('./../app/twig/templates');

		$twig_temp = new Twig_Environment($loader, array(
			'debug' => 'true'));
		return $twig_temp;
	}

		//la siguiente función se usa para el manejo de excepciones
	public static function exepciones($e, $mensajes, $error){
			require_once __DIR__ . '/../twig/lib/Twig/Autoloader.php';
			Twig_Autoloader::register();

			$loader = new Twig_Loader_Filesystem('./../app/twig/templates');
			$newTwig = new Twig_Environment($loader, array());


			echo $newTwig->render('errorBlue.twig.html', array('mensaje' => $mensajes, 'error' => $error));


	}

	public function publicacion(){
			return $this->mPubli;
	}

	public static function xss($text)
	{
		// validar texto
		$comment = trim($text);

		// sanitizar texto
		$comment = strip_tags($comment);

		return $comment;
	}

	public function check_dates($d, $h){
                $d = new DateTime($d);
                $h = new DateTime($h);
                if ($d < $h){
                	return true;
                }
                return false;
	}

 }
?>
