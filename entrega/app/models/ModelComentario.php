<?php

 class ModelComentario extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

	 public function check_date($str){ // verifica si una fecha del tipo yyyy-mm-dd es correcta
                trim($str);
				$trozos = explode ("-", $str);
				if (count($trozos)==3){
					$a�o=$trozos[0];
					$mes=$trozos[1];
					$dia=$trozos[2];
					if(checkdate ($mes,$dia,$a�o)){
						return true;
					}
				}
				return false;
	}
  public function agregarComentario($comentario, $idPublicacion){
      $sql = $this->conexion->prepare("INSERT INTO `comentario`( `pregunta`, `fec_preg`, `id_publicacion`)
      VALUES (:c, NOW(), :idp)");
      $sql->bindParam(':c', $comentario, PDO::PARAM_STR);
      $sql->bindParam(':idP', $idPublicacion, PDO::PARAM_INT);
      $sql->execute();
  }
  public function responder($idComentario, $respuesta){
    $sql = $this->conexion->prepare("UPDATE comentario
										 SET respuesta = :res, fec_rsp = NOW()
										 WHERE id_comentario = :id ");
    $sql->bindParam(':res', $respuesta, PDO::PARAM_STR);
    $sql->bindParam(':id', $idComentario, PDO::PARAM_INT);
    $sql->execute(); 
  }
 }
