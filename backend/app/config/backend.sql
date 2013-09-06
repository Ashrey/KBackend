-- phpMyAdmin SQL Dump
-- version 4.0.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 06-09-2013 a las 16:00:18
-- Versión del servidor: 5.5.32-0ubuntu0.12.10.1
-- Versión de PHP: 5.4.6-1ubuntu1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `backend`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_action`
--

CREATE TABLE IF NOT EXISTS `_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `date_at` datetime NOT NULL,
  `action` set('UNKNOW','INSERT','UPDATE','DELETE','LOGIN','EXCEPTION','EVENT','CONFIG') NOT NULL,
  `type` set('DEBUG','NOTICE','ERROR','INFO','EMERGENCY','CRITICAL','ALERT') NOT NULL,
  `extra` varchar(512) DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_resource`
--

CREATE TABLE IF NOT EXISTS `_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL,
  `controller` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `_resource`
--

INSERT INTO `_resource` (`id`, `module`, `controller`, `action`, `enable`) VALUES
(1, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_role`
--

CREATE TABLE IF NOT EXISTS `_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `_role`
--

INSERT INTO `_role` (`id`, `role`) VALUES
(2, 'Admin'),
(3, 'Simple'),
(1, 'Superuser');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_role_resource`
--

CREATE TABLE IF NOT EXISTS `_role_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Volcado de datos para la tabla `_role_resource`
--

INSERT INTO `_role_resource` (`id`, `role_id`, `resource_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `_user`
--

CREATE TABLE IF NOT EXISTS `_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `_user`
--

INSERT INTO `_user` (`id`, `login`, `password`, `email`, `enable`, `role_id`, `created_at`) VALUES
(1, 'usuario', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'usuario@mail.com', 1, 3, NULL),
(2, 'admin', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'admin@mail.com', 1, 2, NULL),
(3, 'root', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'user@prueba.com', 1, 1, NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `_action`
--
ALTER TABLE `_action`
  ADD CONSTRAINT `_action_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `_role_resource`
--
ALTER TABLE `_role_resource`
  ADD CONSTRAINT `_role_resource_ibfk_3` FOREIGN KEY (`role_id`) REFERENCES `_role` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `_role_resource_ibfk_4` FOREIGN KEY (`resource_id`) REFERENCES `_resource` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `_user`
--
ALTER TABLE `_user`
  ADD CONSTRAINT `_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `_role` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
