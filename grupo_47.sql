-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-05-2016 a las 22:11:56
-- Versión del servidor: 5.7.9
-- Versión de PHP: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `grupo_47`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion_hospedado`
--

DROP TABLE IF EXISTS `calificacion_hospedado`;
CREATE TABLE IF NOT EXISTS `calificacion_hospedado` (
  `id_calif` int(11) NOT NULL AUTO_INCREMENT,
  `puntaje` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `id_reserva` int(11) NOT NULL,
  PRIMARY KEY (`id_calif`),
  KEY `id_reserva` (`id_reserva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion_huesped`
--

DROP TABLE IF EXISTS `calificacion_huesped`;
CREATE TABLE IF NOT EXISTS `calificacion_huesped` (
  `id_calif` int(11) NOT NULL AUTO_INCREMENT,
  `puntaje` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `id_reserva` int(11) NOT NULL,
  PRIMARY KEY (`id_calif`),
  KEY `id_reserva` (`id_reserva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

DROP TABLE IF EXISTS `comentario`;
CREATE TABLE IF NOT EXISTS `comentario` (
  `id_comentario` int(11) NOT NULL AUTO_INCREMENT,
  `pregunta` varchar(255) NOT NULL,
  `respuesta` varchar(255) NOT NULL,
  `fec_preg` date NOT NULL,
  `fec_resp` date DEFAULT NULL,
  `id_publicacion` int(11) NOT NULL,
  PRIMARY KEY (`id_comentario`),
  KEY `id_publicacion` (`id_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clave` varchar(50) NOT NULL,
  `valor` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `clave`, `valor`) VALUES
(1, 'limiteDias', '30'),
(2, 'latitud', '-34.909450'),
(3, 'longitud', '-57.913609'),
(4, 'clave_api', '77knoenb9s88fz'),
(5, 'clave_secreta', 'KJIJucZ56Y5YKDSl'),
(6, 'credencial_oauth', 'c6201594-b459-4b3f-b55c-6314a80c6e0f'),
(7, 'secreto_oauth', 'e2caba90-3ce8-4918-94c8-78bb6498711c');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lugar`
--

DROP TABLE IF EXISTS `lugar`;
CREATE TABLE IF NOT EXISTS `lugar` (
  `id_lugar` int(11) NOT NULL AUTO_INCREMENT,
  `ciudad` varchar(20) NOT NULL,
  `provincia` varchar(20) NOT NULL,
  PRIMARY KEY (`id_lugar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

DROP TABLE IF EXISTS `pago`;
CREATE TABLE IF NOT EXISTS `pago` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `nro_comprobante` varchar(30) NOT NULL,
  `datos_tarjeta` varchar(16) NOT NULL,
  `datos_titular` varchar(50) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

DROP TABLE IF EXISTS `publicacion`;
CREATE TABLE IF NOT EXISTS `publicacion` (
  `id_publicacion` int(11) NOT NULL AUTO_INCREMENT,
  `foto` mediumblob NOT NULL,
  `titulo_prop` longblob NOT NULL,
  `capacidad` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `encabezado` varchar(60) NOT NULL,
  `direccion` varchar(60) NOT NULL,
  `fecha_publi` date NOT NULL,
  `usuario` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `lugar` int(11) NOT NULL,
  PRIMARY KEY (`id_publicacion`),
  KEY `id_tipo` (`tipo`),
  KEY `id_lugar` (`lugar`),
  KEY `id` (`usuario`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

DROP TABLE IF EXISTS `reserva`;
CREATE TABLE IF NOT EXISTS `reserva` (
  `id_reserva` int(11) NOT NULL AUTO_INCREMENT,
  `f_inicio` date NOT NULL,
  `f_fin` date NOT NULL,
  `ocupantes` int(11) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `id_solicitud` (`id_solicitud`),
  KEY `id_publicacion` (`id_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id del rol',
  `nombreRol` varchar(20) NOT NULL COMMENT 'nombre del rol',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombreRol`) VALUES
(1, 'administrador'),
(2, 'visitante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shadow`
--

DROP TABLE IF EXISTS `shadow`;
CREATE TABLE IF NOT EXISTS `shadow` (
  `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Id de usuario',
  `usuario` varchar(30) NOT NULL,
  `nombre` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `id_rol` int(11) NOT NULL COMMENT 'id rol usuario',
  `pass` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `correo` varchar(60) NOT NULL,
  `f_nacimiento` date NOT NULL,
  `telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='usuarios del sistema';

--
-- Volcado de datos para la tabla `shadow`
--

INSERT INTO `shadow` (`id`, `usuario`, `nombre`, `id_rol`, `pass`, `correo`, `f_nacimiento`, `telefono`) VALUES
(1, '', 'admin', 1, 'admin', '', '0000-00-00', ''),
(2, '', 'visitante', 2, 'visitante', '', '0000-00-00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE IF NOT EXISTS `solicitud` (
  `id_solicitud` int(11) NOT NULL AUTO_INCREMENT,
  `ocupantes` int(11) NOT NULL,
  `fec_inicio` date NOT NULL,
  `fec_fin` date NOT NULL,
  `texto` text NOT NULL,
  `fec_solicitud` date NOT NULL,
  `id_publicacion` int(11) NOT NULL,
  PRIMARY KEY (`id_solicitud`),
  KEY `id_publicacion` (`id_publicacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_hospedaje`
--

DROP TABLE IF EXISTS `tipo_hospedaje`;
CREATE TABLE IF NOT EXISTS `tipo_hospedaje` (
  `id_tipo` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) NOT NULL,
  PRIMARY KEY (`id_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
