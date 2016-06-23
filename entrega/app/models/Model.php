<?php
require_once __DIR__ . './../controllers/ControllerBack.php';
 class Model
 {
     protected $conexion;
	 
	 

     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {

	   try {
			$this->conexion = new PDO("mysql:dbname=".$dbname.";host=".$dbhost,$dbuser,$dbpass);
			$this->conexion->exec("set names utf8");    //  lo desactive porque dejo de funcionar
			
		}
		catch(PDOException $e){
			
			Controller::exepciones($e, 'Hubo un error al intentar conectarse a la base de datos.', '');
			return false;
		}
     }
	 
	 public static function testConect(){
	 
		$prueba= new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,
						 Config::$mvc_bd_clave, Config::$mvc_bd_hostname);	
		
		if($prueba==false){
		return false;}
		else{
			return true;}
			
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
