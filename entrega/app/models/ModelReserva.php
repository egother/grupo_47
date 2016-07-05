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
			
		$sql->bindParam(':f_inicio', $solicitud['fec_inicio'], PDO::PARAM_STR);
		$sql->bindParam(':f_fin', $solicitud['fec_fin'], PDO::PARAM_STR);
		$sql->bindParam(':ocupantes', $solicitud['ocupantes'], PDO::PARAM_INT);
		$sql->bindParam(':id_solicitud', $solicitud['id_solicitud'], PDO::PARAM_INT);
		$sql->bindParam(':id_publicacion', $solicitud['id_publicacion'], PDO::PARAM_INT);
		$sql->execute();
		return $sql;
	}

}     

 ?>
