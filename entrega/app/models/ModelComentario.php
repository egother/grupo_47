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
					$año=$trozos[0];
					$mes=$trozos[1];
					$dia=$trozos[2];
					if(checkdate ($mes,$dia,$año)){
						return true;
					}
				}
				return false;
	}
  public function agregarComentario($comentario, $idPublicacion, $usuario){

      $sql = $this->conexion->prepare("INSERT INTO `comentario` (`pregunta`, `fec_preg`, `id_publicacion`, `id_usuario`)
									   VALUES (:c , NOW(), :idP, :idU )");

      $sql->bindParam(':c', $comentario, PDO::PARAM_STR);
      $sql->bindParam(':idP', $idPublicacion, PDO::PARAM_INT);
      $sql->bindParam(':idU', $usuario, PDO::PARAM_INT);
      $sql->execute();
  }
  public function responder($idComentario, $respuesta){
    //var_dump($idComentario);die;
    $sql = $this->conexion->prepare("UPDATE `comentario` SET `respuesta`= :res,`fec_resp`= NOW()
                                          WHERE `id_comentario` = :id ");
    $sql->bindParam(':res', $respuesta, PDO::PARAM_STR);
    $sql->bindParam(':id', $idComentario, PDO::PARAM_INT);
    $sql->execute();
  }
  public function listarComentarios($id){
    $sql = $this->conexion->prepare("SELECT * FROM `comentario` WHERE `id_publicacion`= :id");
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
    $sql->execute();
    $listado = $sql->fetchAll(PDO::FETCH_ASSOC);
    //var_dump($listado);die;
    return $listado;
  }
  public function unComentario($id){
    $sql = $this->conexion->prepare("SELECT * FROM `comentario` WHERE `id_comentario`= :id");
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
    $sql->execute();
    $comentario = $sql->fetchAll(PDO::FETCH_ASSOC);
    return $comentario[0];
  }

  public function comentariosDeUsuario($idUsuario){
    $sql = $this->conexion->prepare("SELECT * FROM `comentario` WHERE `id_usuario`= :id");
    $sql->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $sql->execute();
    $comentarios = $sql->fetchAll(PDO::FETCH_ASSOC);
    return $comentarios;
  }
  public function preguntasAresponder($idUsuario){
    $sql = $this->conexion->prepare("SELECT * FROM `comentario` AS com INNER JOIN  `publicacion`As p
                                    ON (com.id_publicacion=p.id_publicacion) WHERE id_usuario = :id");
    $sql->bindParam(':id', $idUsuario, PDO::PARAM_INT);
    $sql->execute();
    $comentarios = $sql->fetchAll(PDO::FETCH_ASSOC);
    return $comentarios;
  }
 }
