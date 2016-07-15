<?php

 class ModelReserva extends Model
 {

     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

	public function agregar($solicitud){
// $solicitud = array('id_publicacion', 'encabezado', 'id_solicitud', 'ocupantes', 'fec_inicio'
//						'fec_fin', 'texto', 'fec_solicitud0')
		$sql = $this->conexion->prepare("
			INSERT INTO reserva(f_inicio, f_fin, ocupantes, id_solicitud, id_publicacion)
			VALUES (:inicio, :fin, :ocupantes, :solicitud, :publicacion)");
			
		$sql->bindParam(':inicio', $solicitud['fec_inicio'], PDO::PARAM_STR);
		$sql->bindParam(':fin', $solicitud['fec_fin'], PDO::PARAM_STR);
		$sql->bindParam(':ocupantes', $solicitud['ocupantes'], PDO::PARAM_INT);
		$sql->bindParam(':solicitud', $solicitud['id_solicitud'], PDO::PARAM_INT);
		$sql->bindParam(':publicacion', $solicitud['id_publicacion'], PDO::PARAM_INT);
		$sql->execute();
		return $sql;
	}

	public function verReservasDePublicacion($id){
		$sql = $this->conexion->prepare("
				SELECT r.*, p.encabezado 
				FROM reserva AS r INNER JOIN publicacion AS p ON (r.id_publicacion = p.id_publicacion)
				WHERE (r.id_publicacion = :id) AND NOT (r.estado = 'E') AND (r.f_fin > :hoy)
				ORDER BY r.f_inicio DESC");
		$hoy = new DateTime();
		$hoy = $hoy->format("Ymd");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		$sql->bindParam(':hoy', $hoy, PDO::PARAM_STR);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
	}
	
	public function verReservasAceptadas($pub){
		
		$sql = $this->conexion->prepare("SELECT reserva.id_reserva, reserva.f_inicio, reserva.f_fin, reserva.ocupantes, publicacion.encabezado, publicacion.id_publicacion, shadow.nombre
											FROM reserva INNER JOIN publicacion ON reserva.id_publicacion = publicacion.id_publicacion
											INNER JOIN solicitud ON reserva.id_solicitud = solicitud.id_solicitud
											INNER JOIN shadow ON publicacion.usuario = shadow.id
											WHERE solicitud.id_usuario = :pub");
			$sql->bindParam(':pub', $pub, PDO::PARAM_INT);
			$sql->execute();
			$res = $sql->fetchAll(PDO::FETCH_ASSOC);
			
		
		return $res;
		
		
     }
	 
	 public function verReservasOtorgadas($pub){
		
				
			$sql = $this->conexion->prepare("SELECT reserva.id_reserva, reserva.f_inicio, reserva.f_fin, reserva.ocupantes, publicacion.encabezado, publicacion.id_publicacion, shadow.nombre
											FROM reserva INNER JOIN publicacion ON reserva.id_publicacion = publicacion.id_publicacion
											INNER JOIN solicitud ON reserva.id_solicitud = solicitud.id_solicitud
											INNER JOIN shadow ON solicitud.id_usuario = shadow.id
											WHERE publicacion.usuario = :pub");
			$sql->bindParam(':pub', $pub, PDO::PARAM_INT);
			$sql->execute();
			$res = $sql->fetchAll(PDO::FETCH_ASSOC);
			
		
		return $res;
		
     }
	 
	 public function verReservasConcretadas(){
		 
		 $sql = $this->conexion->prepare("SELECT r.id_reserva, r.f_inicio, r.f_fin, r.ocupantes, s1.nombre AS nombre_solicitante, s2.nombre AS nombre_publicador, p.id_publicacion
										FROM reserva AS r INNER JOIN solicitud AS s on r.id_solicitud = s.id_solicitud
										INNER JOIN publicacion AS p on p.id_publicacion = r.id_publicacion
										INNER JOIN shadow AS s1 on s.id_usuario = s1.id
										INNER JOIN shadow AS s2 on p.usuario = s2.id
										WHERE DATE(NOW())>DATE(r.f_inicio)"
										);
		$sql->execute();
		$res = $sql->fetchAll(PDO::FETCH_ASSOC);
		return $res;
		 
	 }

 }
	

 ?>
