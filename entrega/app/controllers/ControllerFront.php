<?php
@session_start();
require_once __DIR__ . '/ControllerLogin.php';
require_once __DIR__ . '/Controller.php';

class ControllerFront extends Controller
{

  public function __construct()
  {
    parent::__construct('publico');
  }

  public function inicio(){
    $this->revisarMensajes();
    $data=array(
      'mensaje' => $this->msj,
	  'error' => $this->err,
      'publicaciones' => $this->mPubli->listarPublicacion()
    );
    if($this->haySesion()){
      $data['log']='1';
    }
    echo $this->twig->render('index.twig.html', $data );
  }

  public function inicioErr()
  {
    echo $this->twig->render('index.twig.html', array('mensaje' => 'Usuario o contraseña incorrectos.',
													  'error' => '1'
														));
  }

  public function quienesSomos()
  {
    if($this->haySesion()){
      echo $this->twig->render('quienesSomos.twig.html', array('log' => '1'));
    }
    else{
      echo $this->twig->render('quienesSomos.twig.html', array());
    }
  }

  public function comoFunciona()
  {
    if($this->haySesion()){
      echo $this->twig->render('comoFunciona.twig.html', array('log' => '1'));
    }
    else{
      echo $this->twig->render('comoFunciona.twig.html', array());
    }
  }

  // presenta la página con el formulario de registro
  public function registrarse()
  {
    $msj="";
	$err="";
	$edad = date("Y-m-d", strtotime("-18 years")); // guarda la fecha de hace 18 años para comprobar la mayoría de edad

    if($this->haySesion()){
      $msj=("Cierre la sesión actual para registrarse como nuevo usuario");
	  $err=1;
      echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $msj, 'error'=>$err));
	  return;
    }
    elseif (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['p1'])) && (isset($_POST['p2'])))
    {
      $p1 = $this->xss($_POST['p1']);
      $p2 = $this->xss($_POST['p2']);
      if ($p1==$p2) {
        $usuario = $this->xss($_POST['usuario']);
        $nombre = $this->xss($_POST['nombre']);
        $mail = $this->xss($_POST['mail']);
        $telefono = $this->xss($_POST['telefono']);
        $fecha = $this->xss($_POST['fecha']);
        // comprueba que el usuario no exista ya
        if (!$this->us->existeUsuario($usuario, $mail)){
          $this->us->agregar($usuario, $nombre, $mail, $telefono, $p1, $fecha);
          $this->setMensaje("El usuario se ha agregado correctamente.", 0);
          header('Location: ./index.php');
        }
        else {
          $params = array(
            'usuario' => $_POST['usuario'],
            'nombre' => $_POST['nombre'],
            'mail' => $_POST['mail'],
            'telefono' => $_POST['telefono'],
            'fecha' => $_POST['fecha'],
          );
          // mostrar mensaje, lo hiciste mal, llenalo de nuevo
          $msj=("El usuario y/o correo elegidos ya han sido registrados");
		  $err=1;
        }
      } else {
        $params = array(
          'usuario' => $_POST['usuario'],
          'nombre' => $_POST['nombre'],
          'mail' => $_POST['mail'],
          'telefono' => $_POST['telefono'],
          'fecha' => $_POST['fecha'],
        );//prueba
        $msj=("Las contraseñas ingresadas no coinciden");
		$err=1;
      }
    } else $params = array(
      'usuario' => '',
      'nombre' => '',
      'mail' => '',
      'telefono' => '',
      'fecha' => '01-01-1990'
    );
    echo $this->twig->render('registro.twig.html', array('params' => $params,
														 'mensaje' => $msj,
														 'error' => $err,
														 'edad' => $edad));
  }
  
  public function recuperarPass(){
	$msj="";
	$err="";
    if($this->haySesion()){
      $msj=("Existe una sesión abierta. No puede realizar la acción solicitada.");
	  $err=1;
      echo $this->twig->render('index.twig.html', array('log' => '1', 'mensaje' => $msj, 'error' => $err));
	  return;
    }
    elseif (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['mail'])))
    {
		$mail = $this->xss($_POST['mail']);
		$res = $this->us->recuperarPass($mail);
		if (count($res)==1) {
			// aca supuestamente se le envia un correo al usuario $res devuelto con su misma contraseña
			if ($res[0]['estado']=='B'){
				$params = array('mail' => $mail);
				$msj=("El usuario fue dado de baja.");
				$err=1;
			} else {
				$params = array('mail' => $mail);
				echo $this->twig->render('recuperarPassCorrecto.twig.html', array('params' => $params,
																				  'mensaje' => $msj,
																				  'error' => $err));
				return;
			}
		} else {
			$params = array('mail' => $mail);
			$msj=("El correo ingresado no pertenece a una cuenta de usuario.");
			$err=1;
		}
    }else
		$params = array('mail' => '');
    echo $this->twig->render('recuperarPass.twig.html', array('params' => $params,
															  'mensaje' => $msj,
															  'error' => $err));
  }
  
  public function terminosYcondiciones(){
	  echo $this->twig->render('terminos.twig.html', array());
  }
}

?>
