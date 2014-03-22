<?php
$menu = array(
	(object)array(
		"name" => "Admin",
		"url" => "#",
		"class"=> null,
		"sub" => array(
			(object)array(
				"name" => "Usuarios",
				"url" => "user"
			),
			(object)array(
				"name" => "Configurar",
				"url" => "config"
			),
			(object)array(
				"name" => "Roles",
				"url" => "role"
			),
			(object)array(
				"name" => "Accesos",
				"url" => "access"
			),
			(object)array(
				"name" => "Acciones",
				"url" => "action"
			),
			(object)array(
				"name" => "Recursos",
				"url" => "resource"
			)
		)
	)
);
return $menu;
