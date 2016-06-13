<?php

require_once __DIR__ . '/ControllerLogin.php';
require_once __DIR__ . '/Controller.php';

@session_start();

class ControllerPublicacion extends Controller
{

  public function __construct()
  {
    parent::__construct('publico');
  }

  public function publicar()
  {
    if($this->haySesion()){
      if (($_SERVER['REQUEST_METHOD'] == 'POST')){
        $tituloProp = $this->xss($_POST['tituloP']);
        $cantidad = $this->xss($_POST['capacidad']);
        $descripcion = $this->xss($_POST['descripcion']);
        $encabezado = $this->xss($_POST['encabezado']);
        $direccion = $this->xss($_POST['direccion']);
        $tipo = $this->xss($_POST['tipoViv']);
        $lugar = $this->xss($_POST['lugar']);
        $foto = $_FILES['imagen'];
        $usuario = $_SESSION['USUARIO']['usuario'];
        $this->mPubli->agregar($foto, $tituloProp, $cantidad, $descripcion, $encabezado, $direccion, $usuario, $tipo, $lugar);
        $msj="La Publicacion Fue Realizada";
        echo $this->twig->render('layoutBackUser.twig.html', array(
    			'mensaje' => $msj,
          'publicaciones' => $this->mPubli->listarPublicacion(),
          'inicio' => '1'));
      }else{
        $tipos = $this->mTipos->listar();
        echo $this->twig->render('publicacion.twig.html', array('log' => '1', 'tipos' => $tipos));
      }
    }else{
      echo $this->twig->render('publicacion.twig.html', array());
    }

  }
}

?>
