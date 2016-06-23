<?php

 class ModelTipoHospedaje extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     { 
		
			parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
	
		
     }

      public function listar(){
		 
		 $sql = $this->conexion->prepare("SELECT * FROM tipo_hospedaje ORDER BY tipo");

		 $sql->execute();
		 
         $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
			
         return $listado; 
		
	  }
	  
	  public function obtenerTipo($idTipo)
     {
         $sql = $this->conexion->prepare("SELECT tipo FROM tipo_hospedaje
										  WHERE id_tipo = :idTipo");
		 $sql->bindParam(':idTipo', $idTipo, PDO::PARAM_STR);
         $sql->execute();
         $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
		 return $resultado[0];
	
	 }	
	 
	 public function modificarTipo($idTipo, $nombre)
	 {
		
		$sql = $this->conexion->prepare("UPDATE tipo_hospedaje
											 SET tipo = UPPER('$nombre')
											 WHERE id_tipo = '$idTipo'");
			 $sql->execute(); 
		
			
	}
		 
	
     
 	 public function verificar($nombre) 
     {
         $sql = $this->conexion->prepare("SELECT tipo FROM tipo_hospedaje
										  WHERE tipo= UPPER(:nombre)");
		 $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
         $sql->execute();
         $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
		if (count($resultado)==0) 
				return true;
		else 	return false;
	}

	 public function agregar($nombre) 
     {
         $sql = $this->conexion->prepare("INSERT INTO tipo_hospedaje (tipo)
												  VALUES (UPPER(:nombre))");
		 $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
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
	
	 public function modificar($id, $n, $r, $p)
     {
		$res = $this->listarUsuario($id);
		
		if ($res!=-1){
			$sql = $this->conexion->prepare("UPDATE shadow
											 SET nombre = :n,
											 id_rol = :r,
											 pass = :p
											 WHERE id = :id");
			 $sql->bindParam(':n', $n, PDO::PARAM_STR);
			 $sql->bindParam(':r', $r, PDO::PARAM_INT);
			 $sql->bindParam(':p', $p, PDO::PARAM_STR);
			 $sql->bindParam(':id', $id, PDO::PARAM_INT);
			 $sql->execute(); 
		} else
			return -1;
	}
	
 }
