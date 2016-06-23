<?php
///////////////////////////////////////////////////
//Convierte fecha de mysql a español
////////////////////////////////////////////////////
function mysqlToFecha($fechadb){
//vamos a suponer que recibmos el formato MySQL básico de YYYY-MM-DD
//lo primero es separar cada elemento en una variable
    list($yy,$mm,$dd)=explode("-",$fechadb);
//si viniera en otro formato, adaptad el explode y el orden de las variables a lo que necesitéis
//creamos un objeto DateTime (existe desde PHP 5.2)
    $fecha = new DateTime();
//definimos la fecha pasándole las variabes antes extraídas
        $fecha->setDate($yy, $mm, $dd);
//y ahora el propio objeto nos permite definir el formato de fecha para imprimir que queramos      
    //echo $fecha->format('d-m-Y');
    return $fecha;
}

///////////////////////////////////////////////////
//Convierte fecha de español a mysql
////////////////////////////////////////////////////
function fechaToMysql($fecha){
//vamos a suponer que recibmos el formato MySQL básico de YYYY-MM-DD
//lo primero es separar cada elemento en una variable
    list($dd,$mm,$yy)=explode("/",$fecha);
//si viniera en otro formato, adaptad el explode y el orden de las variables a lo que necesitéis
//creamos un objeto DateTime (existe desde PHP 5.2)
    $fecha = new DateTime();
//definimos la fecha pasándole las variabes antes extraídas
        $fecha->setDate($yy, $mm, $dd);
//y ahora el propio objeto nos permite definir el formato de fecha para imprimir que queramos      
    //echo $fecha->format('d-m-Y');
    return $fecha;
}



?>




