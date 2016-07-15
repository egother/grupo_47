<?php

 class ModelPublicacion extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost){
		     parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

     //foto, titulo de propiedad, capacidad, descripcion, encabezado,direccion, fecha, usuario, tipo, lugar
    public function agregar($foto, $tp, $c, $des, $e, $dir, $u, $t, $p, $cd){
    		$fecha= (new DateTime())->format("Y-m-d");
    		$fotoBlob = fopen($foto['tmp_name'], 'rb');
    		$sql = $this->conexion->prepare('INSERT INTO `publicacion`(`foto`, `fototype`, `titulo_prop`, `capacidad`, `descripcion`, `encabezado`, `direccion`, `fecha_publi`, `usuario`, `tipo`, `lugar`, `provincia`, `ciudad` )
    		VALUES (:foto, :fototype, :titulo, :capacidad, :descripcion, :encabezado, :direccion, :fecha, :usuario, :tipo, :provincia, :provincia, :ciudad)');
    		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
			  $sql->bindParam(':tipo', $t, PDO::PARAM_INT);
    		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
    		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
    		$sql->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
    		$sql->bindParam(':titulo', $tp, PDO::PARAM_STR);
    		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
    		$sql->bindParam(':provincia', $p, PDO::PARAM_INT);
    		$sql->bindParam(':ciudad', $cd, PDO::PARAM_INT);
    		$sql->bindParam(':fototype', $foto['type'], PDO::PARAM_STR);
    		$sql->bindParam(':foto', $fotoBlob, PDO::PARAM_LOB);
    		return $sql->execute();
	 }

    public function modificar($id, $foto, $tp, $c, $des, $e, $dir, $u, $t, $p, $cd, $est){
		if ($foto['error'] == 4){
    		$sql = $this->conexion->prepare('
				UPDATE `publicacion`
				SET `titulo_prop` = :titulo, `capacidad` = :capacidad, `descripcion` = :descripcion,
						`encabezado` = :encabezado, `direccion` = :direccion, `usuario` = :usuario, `tipo` = :tipo,
						`lugar` = :provincia, `provincia` = :provincia, `ciudad` = :ciudad, `estado` = :est
				WHERE `id_publicacion` = :id');
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
    		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
			$sql->bindParam(':tipo', $t, PDO::PARAM_INT);
    		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
    		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
    		$sql->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
    		$sql->bindParam(':titulo', $tp, PDO::PARAM_STR);
    		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
    		$sql->bindParam(':provincia', $p, PDO::PARAM_INT);
    		$sql->bindParam(':ciudad', $cd, PDO::PARAM_INT);
    		$sql->bindParam(':est', $est, PDO::PARAM_STR);
    		return $sql->execute();

		} elseif ($foto['error'] == 0){
			$fotoBlob = fopen($foto['tmp_name'], 'rb');
			$sql = $this->conexion->prepare('
				UPDATE `publicacion`
				SET `foto` = :foto, `fototype` = :fototype, `titulo_prop` = :titulo, `capacidad` = :capacidad, `descripcion` = :descripcion,
						`encabezado` = :encabezado, `direccion` = :direccion, `usuario` = :usuario, `tipo` = :tipo,
						`lugar` = :provincia, `provincia` = :provincia, `ciudad` = :ciudad, `estado` = :est
				WHERE `id_publicacion` = :id');
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
    		$sql->bindParam(':descripcion', $des, PDO::PARAM_STR);
			$sql->bindParam(':tipo', $t, PDO::PARAM_INT);
    		$sql->bindParam(':encabezado', $e, PDO::PARAM_STR);
    		$sql->bindParam(':direccion', $dir, PDO::PARAM_STR);
    		$sql->bindParam(':usuario', $u, PDO::PARAM_STR);
    		$sql->bindParam(':titulo', $tp, PDO::PARAM_STR);
    		$sql->bindParam(':capacidad', $c, PDO::PARAM_STR);
    		$sql->bindParam(':provincia', $p, PDO::PARAM_INT);
    		$sql->bindParam(':ciudad', $cd, PDO::PARAM_INT);
    		$sql->bindParam(':est', $est, PDO::PARAM_STR);
    		$sql->bindParam(':fototype', $foto['type'], PDO::PARAM_STR);
    		$sql->bindParam(':foto', $fotoBlob, PDO::PARAM_LOB);
    		return $sql->execute();
		}
	 }

   public function listarPublicacion(){
    	$sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
							   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.ciudad, p.provincia, p.estado, s.premium
						FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id)
						WHERE (p.estado = 'A')
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
    	$sql = $this->conexion->prepare("SELECT p.*, pr.nombre AS nombre_provincia, l.nombre AS nombre_ciudad, t.tipo AS nombre_tipo
										FROM publicacion AS p INNER JOIN provincias AS pr ON (p.lugar = pr.id)
											INNER JOIN localidades AS l ON (p.ciudad = l.id)
											INNER JOIN tipo_hospedaje AS t ON (p.tipo = t.id_tipo)
										WHERE (id_publicacion = :id)");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();
		$publi = $sql->fetchAll(PDO::FETCH_ASSOC);
		if (count($publi)==1){
			$publi['0']['foto']=base64_encode($publi['0']['foto']);
			return $publi['0'];
		} else return null;

   }

      public function verMisPublicaciones($id){
    	 $sql = $this->conexion->prepare("SELECT publicacion.*, tipo_hospedaje.tipo AS nombre_tipo
										  FROM publicacion INNER JOIN tipo_hospedaje ON (publicacion.tipo = tipo_hospedaje.id_tipo)
    	 								  WHERE (usuario = :id)
    	 								  ORDER BY publicacion.estado, fecha_publi DESC");
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
   
   public function verIdMisPublicaciones($id){
	    
		$sql = $this->conexion->prepare("SELECT id_publicacion FROM publicacion WHERE usuario = :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
    	$sql->execute();
		$publi = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $publi;
	   
   }

   public function verificar($id_publi, $id_user){
   		return true;
   }

   public function buscar($tipo, $prov){
	    if ($tipo > 0){ 			// si se eligió un tipo de hospedaje
			if ($prov > 0 ){		// y se eligió una provincia
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.estado s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (p.tipo = :tipo) AND (pr.id = :prov) AND (p.estado = 'A')
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':tipo', $tipo, PDO::PARAM_INT);
				$sql->bindParam(':prov', $prov, PDO::PARAM_INT);
			} else {				// si solo se eligió un tipo
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.estado, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (p.tipo = :tipo) AND (p.estado = 'A')
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':tipo', $tipo, PDO::PARAM_INT);
			}
		} else {
			if ($prov > 0 ){	// si solo se eligió una provincia
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.estado, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (pr.id = :prov) AND (p.estado = 'A')
								ORDER BY s.premium DESC, p.fecha_publi DESC
							");
				$sql->bindParam(':prov', $prov, PDO::PARAM_INT);
			} else {			// no se eligió nada
				$sql = $this->conexion->prepare("
								SELECT p.id_publicacion, p.foto, p.fototype, p.titulo_prop, p.capacidad, p.descripcion, p.encabezado,
									   p.direccion, p.fecha_publi, p.usuario, p.tipo, p.lugar, p.estado, s.premium, pr.id
								FROM publicacion AS p INNER JOIN shadow AS s ON (p.usuario = s.id) INNER JOIN provincias AS pr ON (p.lugar = pr.id)
								WHERE (p.estado = 'A')
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

     public function borrar($estaNO){
		$sql = $this->conexion->prepare("
			UPDATE publicacion
			SET estado='B'
			WHERE (id_publicacion = :id) ");
		$sql->bindParam(':id', $estaNO['id_publicacion'], PDO::PARAM_INT);
		$sql->execute();

	 }

	 public function buscarPorTipo($id){
		$sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.encabezado, p.usuario, p.tipo, p.estado
						FROM publicacion AS p
						WHERE (p.tipo = :tipo) AND (p.estado = 'A')
					");
		$sql->bindParam(':tipo', $id, PDO::PARAM_INT);
		$sql->execute();
		$listado = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $listado;
	 }
	 
	 public function estaDisponible($d, $h, $id){
		$sql = $this->conexion->prepare("
				SELECT *
				FROM `reserva` as r
				WHERE NOT ((DATE(r.f_inicio) > DATE(:hasta)) OR (DATE(r.f_fin) < DATE(:desde))) 
					  AND (r.id_publicacion = :id)
					  AND NOT (r.estado = 'E')
			");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->bindParam(':desde', $d, PDO::PARAM_STR);
		$sql->bindParam(':hasta', $h, PDO::PARAM_STR);
		$sql->execute();
		$listado = $sql->fetchAll(PDO::FETCH_ASSOC);
		if (count($listado)>0){
			return false;
		} else {
			return true;
		}
	 }
 }
