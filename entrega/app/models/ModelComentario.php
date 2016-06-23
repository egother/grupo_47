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
	
 }
