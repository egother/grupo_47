<?php
/**
 * Libreria para validar un usuario comprobando su usuario y contraseña
 */

/**
 * Veridica si el usuario está logeado
 */
 
require_once __DIR__ . '/../models/Permisos.php';
@session_start(); //inicia sesion (la @ evita los mensajes de error si la session ya está iniciada)

function estoyLogueado () {

    if (!isset($_SESSION['USUARIO'])) return false; //no existe la variable $_SESSION['USUARIO']. No logeado.
    if (!is_array($_SESSION['USUARIO'])) return false; //la variable no es un array $_SESSION['USUARIO']. No logeado.
    if (empty($_SESSION['USUARIO']['usuario'])) return false; //no tiene almacenado el usuario en $_SESSION['USUARIO']. No logeado.

    //cumple las condiciones anteriores, entonces es un usuario validado
    return true;
}

/**
  * Retorna el nombre del usuario
  */
function dameUsuario(){

	if (estoyLogueado()){
		
		return($_SESSION['USUARIO']['usuario']);
	}

}

function dameRol(){
	
	if (estoyLogueado()){

	return($_SESSION['USUARIO']['nombreRol']);
	}

}

function dameUsuarioYRol(){
	if (estoyLogueado()){
	$usr = array('usuario' => $_SESSION['USUARIO']['usuario'],
				 'nombre' => $_SESSION['USUARIO']['nombre'],
				 'nombreRol' => $_SESSION['USUARIO']['nombreRol']);
	 return $usr;
	}
}

function soyAdmin(){
	if (estoyLogueado()){
		if ($_SESSION['USUARIO']['nombreRol'] == 'administrador'){
			return true;
			}
			else{ return false;}
				
	}
}

function postaTengoPermiso($unaAccion){
	
	return (Permisos::tengoPermiso($unaAccion));
	
}

?>
