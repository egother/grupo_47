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
 }
	

 ?>
