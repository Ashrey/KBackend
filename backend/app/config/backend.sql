-- 
-- Structure for table `_action`
-- 

DROP TABLE IF EXISTS `_action`;
CREATE TABLE IF NOT EXISTS `_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `date_at` datetime NOT NULL,
  `action` set('UNKNOW','INSERT','UPDATE','DELETE','LOGIN','EXCEPTION','EVENT','CONFIG') NOT NULL,
  `type` set('DEBUG','NOTICE','ERROR','INFO','EMERGENCY','CRITICAL','ALERT') NOT NULL,
  `extra` varchar(512) DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `_action_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Structure for table `_resource`
-- 

DROP TABLE IF EXISTS `_resource`;
CREATE TABLE IF NOT EXISTS `_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL,
  `controller` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `enable` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- 
-- Data for table `_resource`
-- 

INSERT INTO `_resource` (`id`, `module`, `controller`, `action`, `enable`) VALUES
  ('1', NULL, NULL, NULL, '1');

-- 
-- Structure for table `_role`
-- 

DROP TABLE IF EXISTS `_role`;
CREATE TABLE IF NOT EXISTS `_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- 
-- Data for table `_role`
-- 

INSERT INTO `_role` (`id`, `role`) VALUES
  ('2', 'Admin'),
  ('3', 'Simple'),
  ('1', 'Superuser');

-- 
-- Structure for table `_role_resource`
-- 

DROP TABLE IF EXISTS `_role_resource`;
CREATE TABLE IF NOT EXISTS `_role_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `resource_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `resource_id` (`resource_id`),
  CONSTRAINT `_role_resource_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `_role` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `_role_resource_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `_resource` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- 
-- Data for table `_role_resource`
-- 

INSERT INTO `_role_resource` (`id`, `role_id`, `resource_id`) VALUES
  ('1', '1', '1');

-- 
-- Structure for table `_user`
-- 

DROP TABLE IF EXISTS `_user`;
CREATE TABLE IF NOT EXISTS `_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `_role` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- 
-- Data for table `_user`
-- 

INSERT INTO `_user` (`id`, `login`, `password`, `email`, `enable`, `role_id`, `created_at`) VALUES
  ('1', 'usuario', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'usuario@mail.com', '0', '1', NULL),
  ('2', 'admin', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'admin@mail.com', '1', '2', NULL),
  ('3', 'root', '$2a$05$AcoE7zCEG276ztq4bGUADuLu4zpq2W3Htt2a8HcBJjO4vkylxy2i2', 'user@prueba.com', '1', '1', NULL);


