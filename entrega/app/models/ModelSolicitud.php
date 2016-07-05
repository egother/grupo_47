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
     
     public function verSolicitudesRealizadas($id){
		$sql = $this->conexion->prepare("SELECT solicitud.*, publicacion.encabezado 
										  FROM solicitud INNER JOIN publicacion ON (solicitud.id_publicacion = publicacion.id_publicacion)
										  WHERE (solicitud.id_usuario = :id) AND (solicitud.estado = 'E')
										  ORDER BY fec_solicitud DESC");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
     }
	 
	 public function verSolicitudesPendientes($id){
		$sql = $this->conexion->prepare("
					SELECT p.id_publicacion, p.encabezado, s.id_solicitud, s.ocupantes, s.fec_inicio, s.fec_fin,
							s.texto, s.fec_solicitud, s.estado
					FROM solicitud AS s INNER JOIN publicacion AS p ON (s.id_publicacion = p.id_publicacion)
					WHERE  (p.usuario = :id) AND (s.estado = 'E')
					ORDER BY s.fec_solicitud");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
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
 	 
	 public function rechazar($estaNO){
		$sql = $this->conexion->prepare("
			UPDATE solicitud
			SET estado='R'
			WHERE (id_solicitud = :id) ");
		$sql->bindParam(':id', $estaNO['id_solicitud'], PDO::PARAM_INT);
		$sql->execute();
		
	 }
	 
 	 public function eliminar($estaNO){
		$sql = $this->conexion->prepare("
			UPDATE solicitud
			SET estado='B'
			WHERE (id_solicitud = :id) ");
		$sql->bindParam(':id', $estaNO['id_solicitud'], PDO::PARAM_INT);
		$sql->execute();
		
	 }

}

?>
