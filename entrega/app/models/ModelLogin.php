<?php

 class ModelLogin extends Model
 {

     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }


	public static function consultar($user,$pass){

		$user = Model::xss($user);
		$pass = Model::xss($pass);

		$cn= New ModelLogin(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
		
		
		$sql = $cn->conexion->prepare("SELECT shadow.id, shadow.usuario, shadow.nombre, shadow.id_rol,
											shadow.correo, shadow.f_nacimiento, shadow.telefono,
											shadow.premium, rol.nombreRol
									   FROM shadow INNER JOIN rol on (shadow.id_rol = rol.id )
									   WHERE (usuario = :user) and (pass = :pass)");
		$sql->bindParam(':user', $user, PDO::PARAM_STR);
		$sql->bindParam(':pass', $pass, PDO::PARAM_STR);
		$sql->execute();
		 
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        
           
        //consulto la cantidad de coincidencias.
        if (count($resultado) == 0){ 
			
			return 0;
		}
		else{
			return $resultado;
			}
	}
	

}     

 ?>
