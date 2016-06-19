<?php

 class ModelSolicitud extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }
     
     public function agregarSolicitud($id_publi, $id_user, $c, $d, $h, $t){
    		$sql = $this->conexion->prepare("
    			INSERT INTO 'solicitud'('ocupantes', 'fec_inicio', 'fec_fin', 
    									'texto', 'fec_solicitud', 'id_publicacion', 'id_usuario')
    			VALUES (:c, :d, :h, :t, :hoy, :id_publi, :id_user)");
    		$hoy = new DateTime();
    		$hoy->format("Y-m-d");
    		$sql->bindParam(':c', $c, PDO::PARAM_INT);
    		$sql->bindParam(':d', $d, PDO::PARAM_STR);
    		$sql->bindParam(':h', $h, PDO::PARAM_STR);
    		$sql->bindParam(':t', $t, PDO::PARAM_STR);
    		$sql->bindParam(':hoy', $hoy, PDO::PARAM_STR);
    		$sql->bindParam(':id_publi', $id_publi, PDO::PARAM_INT);
    		$sql->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    		return $sql->execute();

     }
     
     public function verSolicitudesPorMi($id){
		$sql = $this->conexion->prepare("SELECT * FROM solicitud WHERE id_usuario = :id ORDER BY fec_solicitud DESC");
		$sql->bindParam(':id', $id, PDO::PARAM_INT);
		return $sql->execute();		
     }
 }

?>
