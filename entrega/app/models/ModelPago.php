<?php

 class ModelPago extends Model
 {
     public function __construct($dbname,$dbuser,$dbpass,$dbhost)
     {
		     parent::__construct($dbname,$dbuser,$dbpass,$dbhost);
     }

     public function agregarPago($idUser, $nombre, $numero, $monto){
       $sql = $this->conexion->prepare("INSERT INTO `pago`
         (`fecha`, `monto`, `datos_tarjeta`, `datos_titular`, `id_usuario`)
         VALUES ( NOW(), :m, :dt, :dtar, :idu)");
       $sql->bindParam(':dt', $nombre, PDO::PARAM_STR);
       $sql->bindParam(':dtar', $numero, PDO::PARAM_STR);
       $sql->bindParam(':idu', $idUser, PDO::PARAM_INT);
       $sql->bindParam(':m', $monto, PDO::PARAM_INT);
       $sql->execute();
     }

 }
