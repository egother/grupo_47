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

     public function listarCiudades(){
       $sql = $this->conexion->prepare("SELECT * FROM localidades ORDER BY nombre");
    	 $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

       return $listado;
     }
     public function listarLocalidadesDeProvincia($id){
       $sql = $this->conexion->prepare("
			SELECT  p.id AS id_provincia, p.nombre AS nombre_provincia,
					d.id AS id_departamento, d.nombre AS nombre_departamento,
					l.id AS id_ciudad, l.nombre AS nombre_ciudad
			FROM provincias AS p INNER JOIN departamentos AS d ON p.id=d.provincia_id INNER JOIN localidades AS l ON d.id=l.departamento_id
			WHERE p.id=:id");
       $sql->bindParam(':id', $id, PDO::PARAM_INT);
    	 $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);

       return $listado;
     }
	 
	 public function departamentos($id){
       $sql = $this->conexion->prepare("SELECT l.id, l.nombre
										FROM localidades as l inner join departamentos as d ON (l.departamento_id = d.id)
										WHERE d.provincia_id = :id ");
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
	   if (count($listado)>0) {
			return $listado[0];
	   } else {
		   $listado = array('nombre' => "cualquier provincia");
		   return $listado;
		}
     }

     public function verCiudad($id){
       $sql = $this->conexion->prepare("SELECT * FROM departamentos WHERE id = :id");
       $sql->bindParam(':id', $id, PDO::PARAM_INT);
       $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
       return $listado[0];
     }


 }
