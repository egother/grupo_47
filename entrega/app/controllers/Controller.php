<?php

require_once __DIR__ . '/ControllerLogin.php';

 class Controller
 {
	protected $twig; 	 // variable para las plantillas twig
	protected $mPubli; 	 // variable para la conexión del modelo publicaciones
	protected $mSolic; 	 // variable para la conexión del modelo solicitudes
	protected $mReser; 	 // variable para la conexión del modelo reservas
	protected $us;		 // variable para la conexión del modelo usuarios
	protected $mPagos; 	 // variable para la conexion del modelo pagos
	protected $mCalifHue;// variable para la conexion del modelo calificacion huesped
	protected $mCalifHos;// variable para la conexion del modelo calificacion hospedado
	protected $mComent;	 // variable para la conexion del modelo comentarios
	protected $msj;
	
	//configura los parámetros de Twig para el controllerBack
	
	public function __construct($accion)
	{
		$this->twig = $this->configTwig();
		
		
		if (Model::testConect()){ //si la conexión resulta exitosa
		
			if ($accion == 'publico') {
				$this->us = new ModelUsers(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
			}	
			elseif (postaTengoPermiso($accion)) {
				$this->mPubli = new ModelPublicacion(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
							 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);	
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
			} 
			else {
				$msj = "Usted no posee permisos para realizar dicha operación";
				echo $this->twig->render('index.twig.html', array('mensaje' => $msj));
			}
		}
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
	
	public static function xss($text)
	{ 
		// validar texto
		$comment = trim($text);
		 
		// sanitizar texto
		$comment = strip_tags($comment);
		
		return $comment;
	}
	
 }
?>
