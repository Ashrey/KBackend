
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias`
--

CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuarios_id` int(11) DEFAULT NULL,
  `fecha_at` datetime NOT NULL,
  `accion` set('UNKNOW','INSERT','UPDATE','DELETE','LOGIN','EXCEPTION') NOT NULL,
  `tipo` set('DEBUG','NOTICE','ERROR','INFO','EMERGENCY','CRITICAL','ALERT') NOT NULL,
  `detalles` varchar(512) DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `usuarios_id` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menus_id` int(11) DEFAULT NULL,
  `recursos_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  `posicion` int(11) NOT NULL DEFAULT '100',
  `clases` varchar(50) DEFAULT NULL,
  `visible_en` int(11) NOT NULL DEFAULT '1',
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `menus_id` (`menus_id`),
  KEY `recursos_id` (`recursos_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id`, `menus_id`, `recursos_id`, `nombre`, `url`, `posicion`, `clases`, `visible_en`, `activo`) VALUES
(1, 18, 1, 'Usuarios', 'admin/usuarios', 10, NULL, 2, 1),
(2, 18, 6, 'Auditorias', 'admin/auditorias', 900, NULL, 2, 1),
(3, 18, 2, 'Roles', 'admin/roles', 20, NULL, 2, 1),
(4, 18, 3, 'Recursos', 'admin/recursos', 30, NULL, 2, 1),
(5, 18, 4, 'Menu', 'admin/menu', 100, NULL, 2, 1),
(7, 18, 5, 'Privilegios', 'admin/privilegios', 90, NULL, 2, 1),
(18, NULL, 6, 'Administración', 'admin/usuarios/index', 100, NULL, 2, 1),
(19, NULL, 9, 'Mi Perfil', 'admin/usuarios/perfil', 90, NULL, 2, 1),
(21, 18, 8, 'Config. Aplicacion', 'admin/config', 1000, NULL, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE IF NOT EXISTS `recursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modulo` varchar(50) DEFAULT NULL,
  `controlador` varchar(50) NOT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `recurso` varchar(200) NOT NULL,
  `descripcion` text,
  `activo` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id`, `modulo`, `controlador`, `accion`, `recurso`, `descripcion`, `activo`) VALUES
(1, 'admin', 'usuarios', NULL, 'admin/usuarios/*', 'Gestor de usuarios del sistema', 1),
(2, 'admin', 'roles', NULL, 'admin/roles/*', 'Gestor de los roles de la aplicación\n', 1),
(3, 'admin', 'recursos', NULL, 'admin/recursos/*', 'Gestor de los recursos de la aplicación', 1),
(4, 'admin', 'menu', NULL, 'admin/menu/*', 'Gestor del menú de la aplicación', 1),
(5, 'admin', 'privilegios', NULL, 'admin/privilegios/*', 'Gestor de  privilegios para cada rol', 1),
(6, 'admin', 'auditorias', NULL, 'admin/auditorias/*', 'Registro de las acciones realizadas por los usuarios', 1),
(7, 'admin', 'config', NULL, 'admin/config/*', 'Configuraciones del sistema', 1),
(8, 'admin', 'index', NULL, 'admin/index/*', 'Entrada al sistema', 1),
(9, 'admin', 'usuarios', 'perfil', 'admin/usuarios/perfil', 'Perfil del usuario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `rol` varchar(50) NOT NULL,
  `plantilla` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rol` (`rol`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`, `plantilla`) VALUES
(1, 'Básico', NULL),
(2, 'Administrador', NULL),
(3, 'Super Usuario', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_recursos`
--

CREATE TABLE IF NOT EXISTS `roles_recursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roles_id` int(11) NOT NULL,
  `recursos_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_id` (`roles_id`),
  KEY `recursos_id` (`recursos_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Volcado de datos para la tabla `roles_recursos`
--

INSERT INTO `roles_recursos` (`id`, `roles_id`, `recursos_id`) VALUES
(48, 2, 6),
(49, 3, 6),
(50, 3, 7),
(52, 2, 8),
(53, 3, 8),
(54, 2, 4),
(55, 3, 4),
(56, 3, 5),
(57, 2, 3),
(58, 3, 3),
(59, 2, 2),
(60, 3, 2),
(61, 2, 1),
(62, 3, 1),
(66, 2, 9),
(67, 3, 9),
(74, 1, 9),
(75, 1, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `clave` varchar(40) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `roles_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `roles_id` (`roles_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;


CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `clave` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT '1',
  `roles_id` int(10) NOT NULL,
  `creado_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `roles_id` (`roles_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `login`, `clave`, `email`, `activo`, `roles_id`, `creado_at`) VALUES
(1, 'usuario', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'usuario@mail.com', 0, 1, NULL),
(2, 'admin', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'admin@mail.com', 1, 2, NULL),
(3, 'root', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'user@prueba.com', 1, 3, NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `auditorias`
--
ALTER TABLE `auditorias`
  ADD CONSTRAINT `auditorias_ibfk_1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_ibfk_1` FOREIGN KEY (`menus_id`) REFERENCES `menus` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `menus_ibfk_2` FOREIGN KEY (`recursos_id`) REFERENCES `recursos` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `roles_recursos`
--
ALTER TABLE `roles_recursos`
  ADD CONSTRAINT `roles_recursos_ibfk_5` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `roles_recursos_ibfk_6` FOREIGN KEY (`recursos_id`) REFERENCES `recursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
