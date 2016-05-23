<?php

require_once __DIR__ . '/ControllerLogin.php';
require_once __DIR__ . '/Controller.php';
@session_start(); 

 class ControllerBack extends Controller
 {
	var $info;
	
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
																   'inicio' => '1',
																   'mensaje'=>$this->msj));
     }
	 
	public function contacto()
    {
		echo $this->twig->render('contacto.twig.html', array('usuario' => dameUsuarioYRol()));
    }

	public function check_date($str){ // verifica si una fecha del tipo yyyy-mm-dd es correcta
                trim($str);
				$trozos = explode ("-", $str);
				if (count($trozos)==3){
					$año=$trozos[0];
					$mes=$trozos[1];
					$dia=$trozos[2];
					if(checkdate ($mes,$dia,$año)){
						return true;
					}
				}
				return false;
	} 


}
?>
