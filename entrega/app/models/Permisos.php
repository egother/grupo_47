<?php    

@session_start(); 

/*
 * En esta clase, se define un array, donde estarán los permisos de cada perfil. En esta misma clase
 * Se validan dichos permisos.
 * 
 * Para cada tipo de usuario hay que agregar en el arreglo del mismo las funciones permitidas:
 * array('funcionUno'=>'0', 'funcionDos'=>'0', ... )
 * El =>'0' tiene que estar para habilitarlo como posicion de arreglo, sino no funciona
 */

class Permisos {

    private static $accesos = array(
		'administrador' => array('inicio'=>'0'
								 ),
		'visitante' => array('inicio'=>'0'
							)
	 );
	 

	public static function tengoPermiso($accionAEjecutar){
		return true; // al finalizar, quitar esta linea
		
/*		if (!(isset($_SESSION['USUARIO']))) {
			return false;
		}
		 elseif (isset(self::$accesos[$_SESSION['USUARIO']['rol']][$accionAEjecutar])) {
			return true;	
		}
		return false;
			
*/
	}
}
?>