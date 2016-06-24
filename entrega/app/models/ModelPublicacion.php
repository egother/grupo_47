<?php

 class ModelPublicacion extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost){
		     parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

     //foto, titulo de propiedad, capacidad, descripcion, encabezado,direccion, fecha, usuario, tipo, lugar
    public function agregar($foto, $tp, $c, $des, $e, $dir, $u, $t, $l){
    		$fecha= (new DateTime())->format("Y-m-d");
    		$fotoBlob = fopen($foto['tmp_name'], 'rb');
    		$sql = $this->conexion->prepare('INSERT INTO `publicacion`(`foto`, `fototype`, `titulo_prop`, `capacidad`, `descripcion`, `encabezado`, `direccion`, `fecha_publi`, `usuario`, `tipo`, `lugar`)
    		VALUES (:foto, :fototype, :titulo, :capacidad, :descripcion, :encabezado, :direccion, :fecha, :usuario, :tipo, :lugar)');
    		$sql->bindParam(':tipo', $tp, PDO::PARAM_STR);
    		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
    		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
    		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
    		$sql->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
    		$sql->bindParam(':titulo', $t, PDO::PARAM_STR);
    		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
    		$sql->bindParam(':lugar', $l, PDO::PARAM_STR);
    		$sql->bindParam(':fototype', $foto['type'], PDO::PARAM_STR);
    		$sql->bindParam(':foto', $fotoBlob, PDO::PARAM_LOB);
    		return $sql->execute();
	 }

   public function listarPublicacion(){
    	 $sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
							   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, s.premium
						FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id)
						ORDER BY s.premium DESC, p.fecha_publi		 
					");
    	 $sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
       $publicaciones=array();
       foreach ($listado as $key => $publicacion) {
         $publicaciones[$key]=$publicacion;
         $publicaciones[$key]['foto']=base64_encode($publicacion['foto']);
       }
       return $publicaciones;
   }
  // public function listarPublicacionUsuario($_SESSION['usuarios']){

   //}

   public function verPublicacion($id){
    	$sql = $this->conexion->prepare("SELECT * FROM publicacion WHERE id_publicacion = :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();
		$publi = $sql->fetchAll(PDO::FETCH_ASSOC);
		if (count($publi)==1){
			$publi['0']['foto']=base64_encode($publi['0']['foto']);
			return $publi['0'];
		} else return null;
	   
   }
   
      public function verMisPublicaciones($id){
    	 $sql = $this->conexion->prepare("SELECT * FROM publicacion
    	 								  WHERE usuario = :id
    	 								  ORDER BY fecha_publi DESC");
    	 $sql->bindParam(':id', $id, PDO::PARAM_INT);
    	 $sql->execute();
         $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
         $publicaciones=array();
         foreach ($listado as $key => $publicacion) {
           $publicaciones[$key]=$publicacion;
           $publicaciones[$key]['foto']=base64_encode($publicacion['foto']);
         }
         return $publicaciones;
   }
   
   public function verificar($id_publi, $id_user){
   		return true;
   }


 }
