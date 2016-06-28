<?php

 class ModelPublicacion extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost){
		     parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

     //foto, titulo de propiedad, capacidad, descripcion, encabezado,direccion, fecha, usuario, tipo, lugar
    public function agregar($foto, $tp, $c, $des, $e, $dir, $u, $t, $p, $c){
    		$fecha= (new DateTime())->format("Y-m-d");
    		$fotoBlob = fopen($foto['tmp_name'], 'rb');
    		$sql = $this->conexion->prepare('INSERT INTO `publicacion`(`foto`, `fototype`, `titulo_prop`, `capacidad`, `descripcion`, `encabezado`, `direccion`, `fecha_publi`, `usuario`, `tipo`, `provincia`, `ciudad` )
    		VALUES (:foto, :fototype, :titulo, :capacidad, :descripcion, :encabezado, :direccion, :fecha, :usuario, :tipo, :lugar :provincia, :ciudad)');
    		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
        $sql->bindParam(':tipo', $tp, PDO::PARAM_INT);
    		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
    		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
    		$sql->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
    		$sql->bindParam(':titulo', $t, PDO::PARAM_STR);
    		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
    		$sql->bindParam(':provincia', $p, PDO::PARAM_INT);
    		$sql->bindParam(':lugar', $p, PDO::PARAM_INT);
    		$sql->bindParam(':ciudad', $c, PDO::PARAM_INT);
    		$sql->bindParam(':fototype', $foto['type'], PDO::PARAM_STR);
    		$sql->bindParam(':foto', $fotoBlob, PDO::PARAM_LOB);
    		return $sql->execute();
	 }

    public function modificar($id, $foto, $tp, $c, $des, $e, $dir, $u, $t, $p, $cd){
		$fecha= (new DateTime())->format("Y-m-d");
		$fotoBlob = fopen($foto['tmp_name'], 'rb');
		$sql = $this->conexion->prepare('
			UPDATE `publicacion` SET (`id`=:id ,`foto`=:foto, `fototype`=:fototype, `titulo_prop`=:titulo,
				`capacidad`=:capacidad, `descripcion`=:descripcion, `encabezado`=:encabezado, `direccion`=:direccion,
				`fecha_publi`=:fecha, `usuario`=:usuario, `tipo`=:tipo, `provincia`=:provincia, `ciudad`=:ciudad, `lugar`=:lugar )');

		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
		$sql->bindParam(':tipo', $tp, PDO::PARAM_INT);
		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
		$sql->bindParam(':fecha', $fecha, PDO::PARAM_STR);
		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
		$sql->bindParam(':titulo', $t, PDO::PARAM_STR);
		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
		$sql->bindParam(':provincia', $p, PDO::PARAM_INT);
		$sql->bindParam(':lugar', $p, PDO::PARAM_INT);
		$sql->bindParam(':ciudad', $cd, PDO::PARAM_INT);
		$sql->bindParam(':fototype', $foto['type'], PDO::PARAM_STR);
		$sql->bindParam(':foto', $fotoBlob, PDO::PARAM_LOB);
		return $sql->execute();
	 }

   public function listarPublicacion(){
    	$sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
							   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.ciudad, p.provincia, s.premium
						FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id)
						ORDER BY s.premium DESC, p.fecha_publi DESC
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
    	$sql = $this->conexion->prepare("SELECT p.*, pr.nombre, t.tipo AS nombre_tipo
										 FROM publicacion AS p INNER JOIN provincias AS pr ON (p.lugar = pr.id)
											INNER JOIN tipo_hospedaje AS t ON (p.tipo = t.id_tipo)
										 WHERE id_publicacion = :id");
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

   public function buscar($tipo, $prov){
	    if ($tipo > 0){ 			// si se eligió un tipo de hospedaje
			if ($prov > 0 ){		// y se eligió una provincia
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (p.tipo = :tipo) AND (pr.id = :prov)
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':tipo', $tipo, PDO::PARAM_INT);
				$sql->bindParam(':prov', $prov, PDO::PARAM_INT);
			} else {				// si solo se eligió un tipo
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (p.tipo = :tipo)
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':tipo', $tipo, PDO::PARAM_INT);
			}
		} else {
			if ($prov > 0 ){	// si solo se eligió una provincia
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (pr.id = :prov)
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':prov', $prov, PDO::PARAM_INT);
			} else {			// no se eligió nada
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
			}
		}
		$sql->execute();
       $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
       $publicaciones=array();
       foreach ($listado as $key => $publicacion) {
         $publicaciones[$key]=$publicacion;
         $publicaciones[$key]['foto']=base64_encode($publicacion['foto']);
       }
       return $publicaciones;
   }

 }
