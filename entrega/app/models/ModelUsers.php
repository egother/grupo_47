<?php

 class ModelUsers extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {

			parent::__construct($dbname,$dbuser,$dbpass,$dbhost);


     }

     public function listar()
     {

        $sql = $this->conexion->prepare("SELECT shadow.id, shadow.usuario, rol.nombreRol FROM shadow INNER JOIN rol on (shadow.id_rol = rol.id ) ORDER BY shadow.nombre");

		$sql->execute();

        $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $listado;
     }

     public function usuarioConId($id)
     {
        $sql = $this->conexion->prepare("SELECT usuario FROM `shadow` WHERE id= :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();

		$res = $sql->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)==1)
			return $res;
		else
			return -1;
     }

     public function borrarUsuarioConId($id)
     {
        $sql = $this->conexion->prepare("DELETE FROM shadow WHERE id = :id
										");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();

        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)==1)
			return $res;
		else
			return -1;

	}

     public function listarRoles(){

		$sql = $this->conexion->prepare("SELECT nombreRol FROM rol");

		$sql->execute();

        $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $listado;

	 }

      public function listarUsuarios(){

		 $sql = $this->conexion->prepare("SELECT usuario FROM shadow");

		 $sql->execute();

         $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $listado;

	 }

	  public function listarRol($id){

		$sql = $this->conexion->prepare("SELECT id FROM rol WHERE id = :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);

		$sql->execute();

        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)==1)
			return $res;
		else
			return -1;
	 }
	 
	 //Lista por nombre de usuario y no por id, porque por id matcheba con id_rol
	 public function listarUsuario($usuario){
		 
		$sql = $this->conexion->prepare("SELECT * FROM shadow WHERE usuario = :usuario");
		$sql->bindParam(':usuario', $usuario, PDO::PARAM_INT);
		$sql->execute();

        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)==1)
			return $res;
		else
			return -1;
	 }

	 public function listarPorId($id){

		$sql = $this->conexion->prepare("SELECT shadow.id, shadow.usuario, rol.nombreRol, shadow.pass FROM shadow INNER JOIN rol on (shadow.id_rol = rol.id ) WHERE shadow.id = :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();

        $res = $sql->fetchAll(PDO::FETCH_ASSOC);

		if (count($res)==1)
			return $res;
		else
			return -1;

	}

	//Retorna si el usuario ya existe.
	public function existeUsuario($nombreUsuario, $mailUsuario){

		 $sql = $this->conexion->prepare("SELECT usuario FROM shadow WHERE (usuario = :nombreUsuario) OR (correo = :mailUsuario)");
		 $sql->bindParam(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);
		 $sql->bindParam(':mailUsuario', $mailUsuario, PDO::PARAM_STR);
		 $sql->execute();

         $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);


         if (count($resultado) == 0){


			return false;
		}
		else{

			return true;
			}
	}

	// usuario, nombre, correo, telefono, password
	 public function agregar($u, $n, $c, $t, $p, $f)
     {
         $sql = $this->conexion->prepare("INSERT into shadow
										(usuario, nombre, id_rol, pass, correo, f_nacimiento, telefono)
												  VALUES (:u, :n, 2, :p, :c, :f, :t)");
		 $sql->bindParam(':u', $u, PDO::PARAM_STR);
		 $sql->bindParam(':n', $n, PDO::PARAM_INT);
		 $sql->bindParam(':p', $p, PDO::PARAM_STR);
		 $sql->bindParam(':c', $c, PDO::PARAM_STR);
		 $sql->bindParam(':f', $f, PDO::PARAM_STR);
		 $sql->bindParam(':t', $t, PDO::PARAM_STR);
         $sql->execute();

	}

	 public function borrar($id)
    {
		$res = $this->listarUsuario($id);

		if ($res!=-1){
			$sql = $this->conexion->prepare("DELETE from shadow	WHERE id = :id");
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
			$sql->execute();
		} else
			return -1;
	}
	
	 public function modificar($usu, $n)
	 
     {
		
		$res = $this->listarUsuario($usu);
		$idaux = $res[0]['id'];
		//print_r($idaux);
		if ($res!=-1){
			
			$sql = $this->conexion->prepare("UPDATE shadow SET nombre = :n WHERE id = :idaux ");
			$sql->bindParam(':n', $n, PDO::PARAM_STR);
			 //print_r($sql);
			 //$sql->bindParam(':r', $r, PDO::PARAM_INT);
			 //$sql->bindParam(':usu', $usu, PDO::PARAM_STR);
			 //$sql->bindParam(':id', $idaux, PDO::PARAM_INT);
			
			$sql->execute(); 

			  
		} else
			return -1;
	}

	 public function verConfiguracion()
     {

		$sql = $this->conexion->prepare("SELECT * FROM configuracion ORDER BY id");

		 $sql->execute();

         $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

        return $listado;

	}

	public function modificarConfig($dias, $lat, $lon, $dato1, $dato2, $dato3, $dato4){

		//actualizar dias
		$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :dias
										WHERE id = 1");
		$sql->bindParam(':dias', $dias, PDO::PARAM_INT);
		$sql->execute();

		//actualizar latitud
				$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :lat
										WHERE id = 2");
		$sql->bindParam(':lat', $lat, PDO::PARAM_INT);
		$sql->execute();

		//actualizar longitud
		$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :lon
										WHERE id = 3");
		$sql->bindParam(':lon', $lon, PDO::PARAM_INT);
		$sql->execute();

		//actualizar clave de api
				$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :dato1
										WHERE id = 4");
		$sql->bindParam(':dato1', $dato1, PDO::PARAM_INT);
		$sql->execute();
		//actualizar clave secreta
				$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :dato2
										WHERE id = 5");
		$sql->bindParam(':dato2', $dato2, PDO::PARAM_INT);
		$sql->execute();
		//actualizar credencial del usuario oauth
				$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :dato3
										WHERE id = 6");
		$sql->bindParam(':dato3', $dato3, PDO::PARAM_INT);
		$sql->execute();
		//actualizar secreto del usuario oauth
				$sql = $this->conexion->prepare("UPDATE configuracion
										SET valor = :dato4
										WHERE id = 7");
		$sql->bindParam(':dato4', $dato4, PDO::PARAM_INT);
		$sql->execute();


		$_SESSION['UBICACION']['lat'] = $lat;
		$_SESSION['UBICACION']['lon'] = $lon;

	}

	public function link()
	{
		$sql = $this->conexion->prepare("
					SELECT  clave, valor
					FROM configuracion
					WHERE (clave = 'clave_api') OR (clave = 'clave_secreta') OR
						  (clave = 'credencial_oauth') OR (clave = 'secreto_oauth')
			");
		$sql->execute();

		$sql = $sql->fetchAll(PDO::FETCH_ASSOC);

		$res = array();

		foreach ($sql as $tupla)
		{
			$res[$tupla['clave']] = $tupla['valor'];  // aca le damos un formato accesible y amigable al arreglo resultado
		}

		return $res;

	}

 }
