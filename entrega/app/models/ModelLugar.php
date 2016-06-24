<?php

 class ModelLugar extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

     public function listarProvincias(){
       $sql = $this->conexion->prepare("SELECT * FROM provincias ORDER BY nombre");
    	 $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

       return $listado;
     }

     public function listarLocalidadesDeProvincia($id){
       $sql = $this->conexion->prepare("SELECT * FROM departamentos WHERE provincia_id = :id ORDER BY nombre DESC");
       $sql->bindParam(':id', $id, PDO::PARAM_INT);
    	 $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

       return $listado;
     }

     public function verProvincia($id){
       $sql = $this->conexion->prepare("SELECT * FROM provincias WHERE id = :id");
       $sql->bindParam(':id', $id, PDO::PARAM_INT);
       $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
       return $listado[0];
     }

     public function verCiudad($id){
       $sql = $this->conexion->prepare("SELECT * FROM departamentos WHERE id = :id");
       $sql->bindParam(':id', $id, PDO::PARAM_INT);
       $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
       return $listado[0];
     }


 }
