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
		'administrador' => array('inicio'=>'0', 'tipos'=>'0', 'modificarUsuario'=>'0', 'modificarPass'=>'0',
								 'verPublicacion' => '0', 'publicar' => '0',
								 'misPublicaciones' => '0', 'solicitudesPendientes' => '0',
								 'reservasAceptadas' => '0', 'solicitudesRealizadas' => '0',
								 'reservasOtorgadas' => '0', 'modificarTipo'=>'0', 'provincias' => '0',
								 'modificarPublicacion' => '0', 'buscar' => '0', 'lugares' => '0',
								 'aceptarSolicitud' => '0', 'aceptarSolicitud' => '0',
								 'rechazarSolicitud' => '0', 'borrarSolicitud' => '0', 'eliminarPublicacion' => '0',
								 'borrarTipo' => '0', 'departamentos' => '0',
								 'agregarComentario' => '0', 'responderComentario' => '0', 'responder' => '0',
               'misPreguntas' => '0', 'preguntasAresponder' => '0','reservasConcretadas' => '0'
								 ),
		'visitante' => array('inicio'=>'0', 'modificarUsuario'=>'0', 'modificarPass'=>'0', 'usuarioPremium'=>'0',
							 'publicar'=>'0', 'pagar'=>'0', 'verPublicacion' => '0',
							 'misPublicaciones' => '0', 'solicitudesPendientes' => '0',
							 'reservasAceptadas' => '0', 'solicitudesRealizadas' => '0',
							 'reservasOtorgadas' => '0', 'provincias' => '0',
							 'departamentos' => '0', 'listarLocalidadesDeProvincia' => '0',
							 'modificarPublicacion' => '0', 'buscar' => '0',
							 'aceptarSolicitud' => '0', 'aceptarSolicitud' => '0',
							 'rechazarSolicitud' => '0', 'borrarSolicitud' => '0', 'eliminarPublicacion' => '0',
							 'agregarComentario' => '0', 'responderComentario' => '0', 'responder' => '0',
               'misPreguntas' => '0', 'preguntasAresponder' => '0'
							)
	 );


	public static function tengoPermiso($accionAEjecutar){

		if (!(isset($_SESSION['USUARIO']))) {
			return false;
		}
		 elseif (isset(self::$accesos[$_SESSION['USUARIO']['nombreRol']][$accionAEjecutar])) {
			return true;
		}
		return false;

	}
}
?>
