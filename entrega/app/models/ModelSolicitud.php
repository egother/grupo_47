<?php

 class ModelSolicitud extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }
     
     public function agregarSolicitud($id_publi, $id_user, $c, $d, $h, $t){
    		$sql = $this->conexion->prepare("
    			INSERT INTO solicitud(ocupantes, fec_inicio, fec_fin, 
    									texto, fec_solicitud, id_publicacion, id_usuario)
    			VALUES (:c, :d, :h, :t, :hoy, :id_publi, :id_user)");
    		$hoy = new DateTime();
    		$hoy = $hoy->format("Y-m-d");
    		$sql->bindParam(':c', $c, PDO::PARAM_INT);
    		$sql->bindParam(':d', $d, PDO::PARAM_STR);
    		$sql->bindParam(':h', $h, PDO::PARAM_STR);
    		$sql->bindParam(':t', $t, PDO::PARAM_STR);
    		$sql->bindParam(':hoy', $hoy, PDO::PARAM_STR);
    		$sql->bindParam(':id_publi', $id_publi, PDO::PARAM_INT);
    		$sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
			$sql->execute();
    		return $sql;

     }
	
	public function verIdSolicitud($id){
		 $sql = $this->conexion->prepare("SELECT id_solicitud FROM solicitud WHERE id_usuario = :id");
		 $sql->bindParam(':id', $id, PDO::PARAM_INT);
		 $sql->execute();
		 $res = $sql->fetchAll(PDO::FETCH_ASSOC);
		 return $res;
	 }
	
	 public function verIdPublicacion($id){
		$sql = $this->conexion->prepare("select p.id_publicacion
										from publicacion as p inner join solicitud as s ON p.id_publicacion = s.id_publicacion
										where s.id_solicitud = :id");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res[0]['id_publicacion'];
		 
	 }
     
     public function verSolicitudesRealizadas($id){
		$sql = $this->conexion->prepare("SELECT solicitud.*, publicacion.encabezado 
										  FROM solicitud INNER JOIN publicacion ON (solicitud.id_publicacion = publicacion.id_publicacion)
										  WHERE (solicitud.id_usuario = :id) AND NOT ((solicitud.estado = 'B') OR (solicitud.estado = 'A'))
										  ORDER BY fec_solicitud DESC");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
     }
	 
	 public function verSolicitudesPendientes($id, $publi){
		 if ($publi == 0){
			$sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.encabezado, s.id_solicitud, s.ocupantes, s.fec_inicio, s.fec_fin,
								s.texto, s.fec_solicitud, s.estado
						FROM solicitud AS s INNER JOIN publicacion AS p ON (s.id_publicacion = p.id_publicacion)
						WHERE  (p.usuario = :id) AND ((s.estado = 'E') OR (s.estado = 'P'))
						ORDER BY s.fec_solicitud");
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
		 } else {
			$sql = $this->conexion->prepare("
						SELECT p.id_publicacion, p.encabezado, s.id_solicitud, s.ocupantes, s.fec_inicio, s.fec_fin,
								s.texto, s.fec_solicitud, s.estado
						FROM solicitud AS s INNER JOIN publicacion AS p ON (s.id_publicacion = p.id_publicacion)
						WHERE  (p.usuario = :id) AND ((s.estado = 'E') OR (s.estado = 'P')) AND (p.id_publicacion = :idP)
						ORDER BY s.fec_solicitud");
			$sql->bindParam(':id', $id, PDO::PARAM_INT);
			$sql->bindParam(':idP', $publi, PDO::PARAM_INT);
		 }
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
	 }
	 
	 // descarta las que se superponen y cambia el estado de la (A)ceptada
	 public function descartar($estasNO, $estaSI){
		$sql = $this->conexion->prepare("
			UPDATE solicitud
			SET estado='A'
			WHERE (id_solicitud = :id) ");
		$sql->bindParam(':id', $estaSI['id_solicitud'], PDO::PARAM_INT);
		$sql->execute();
		
		foreach ($estasNO as $elem){
			$sql = $this->conexion->prepare("
				UPDATE solicitud
				SET estado='R'
				WHERE (id_solicitud = :id) ");
			$sql->bindParam(':id', $elem['id_solicitud'], PDO::PARAM_INT);
			$sql->execute();
		}
		
	 }
	 
	 public function descartar_solo($estasNo){
 		foreach ($estasNo as $elem){
			$sql = $this->conexion->prepare("
				UPDATE solicitud
				SET estado='R'
				WHERE (id_solicitud = :id) ");
			$sql->bindParam(':id', $elem['id_solicitud'], PDO::PARAM_INT);
			$sql->execute();
		}
	 }
 	 
	 public function rechazar($estaNO){
		$sql = $this->conexion->prepare("
			UPDATE solicitud
			SET estado='R'
			WHERE (id_solicitud = :id) ");
		$sql->bindParam(':id', $estaNO['id_solicitud'], PDO::PARAM_INT);
		$sql->execute();
		
	 }
	 
 	 public function borrar($estaNO){
		$sql = $this->conexion->prepare("
			UPDATE solicitud
			SET estado='B'
			WHERE (id_solicitud = :id) ");
		$sql->bindParam(':id', $estaNO['id_solicitud'], PDO::PARAM_INT);
		$sql->execute();
		
	 }

     public function verSolicitudesDePublicacion($id){
		$sql = $this->conexion->prepare("
				SELECT s.*, p.encabezado 
				FROM solicitud AS s INNER JOIN publicacion AS p ON (s.id_publicacion = p.id_publicacion)
				WHERE (s.id_publicacion = :id) AND (s.estado = 'E') AND (s.fec_inicio > :hoy)
				ORDER BY s.fec_solicitud DESC");
		$hoy = new DateTime();
		$hoy = $hoy->format("Ymd");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->bindParam(':hoy', $hoy, PDO::PARAM_STR);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
     }
	 
	 public static function revisarSolicitudes($id){
		 $cn= New ModelSolicitud(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
		 $sql = $cn->conexion->prepare("
				UPDATE `solicitud`
				SET `estado`='P'
				WHERE DATE(NOW()) >= DATE(`fec_inicio`)
			");
		 $sql->execute();
	 }
	 
}

?>
