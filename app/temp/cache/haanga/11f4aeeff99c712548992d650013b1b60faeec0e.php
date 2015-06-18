<?php
use \KBackend\Libs\AuthACL;
$HAANGA_VERSION  = '1.0.4';
/* Generated from /home/beto/www/backend/app/views/index/index.phtml */
function haanga_11f4aeeff99c712548992d650013b1b60faeec0e($vars155830f55f3d98, $return=FALSE, $blocks=array())
{
    extract($vars155830f55f3d98);
    if ($return == TRUE) {
        ob_start();
    }
    $buffer1  = '
<div class="container">
	<div class="jumbotron">
		<h1>Bienvenido a KBackend</h1>
		<p>El backend es el módulo de administración y configuración de una aplicación,
			a traves de él se gestionan las diferentes partes de la misma, desde el control
			de los usuarios de sistemas y sus privilegios, hasta llevar auditorias sobre
			las acciones que estos realizan</p>
		<a class="btn btn-primary btn-lg" href="https://github.com/Ashrey/KBackend" target="_blank"><i class="fa fa-github-alt"></i> Ver en GitHub</a>
		</p>
	</div>

	<div class="row">
		<div class="col-4">
			<h4>Responsivo</h4>
			Basado en Bootstrap de Twitter, provee un diseño adaptado a todo tipo de pantalla.
		</div>
		<div class="col-4">
			<h4>Personalizable</h4>
			Puedes personalizar el backend con temas, apariencias y variables de configuración
		</div>
		<div class="col-4">
			<h4>Código Abierto</h4>
			El DBKM está licenciado bajo la New BSD, lo cual te provee de toda una libertad a nivel de programación
		</div>
	</div>

	<div class="row">
		<div class="col-4">
			<h4>Arquitectura MVC</h4>
			Realizado bajo el patrón de diseño MVC y construido con la solidez de KumbiaPHP
		</div>
		<div class="col-4">
			<h4>Características</h4>
			<ul>
				<li>Rápido escalado</li>
				<li>Diseño orientado al usuario final</li>
				<li>Rápido, sencillo y estable gracias al poder de KumbiaPHP</li>
				<li>Control de autidorías y sucesos del sistema</li>
			</ul>
		</div>
		<div class="col-4">
			<h4>Funcionalidades</h4>
			<ul>
				<li>Manipulación de Usuarios</li>
				<li>Gestión de Roles (Perfiles de Usuario) </li>
				<li>Gestión de Recursos (Módulos de la Aplicación)</li>
				<li>Permisos de los usuarios a los diferentes Modulos de la Aplicación</li>
				<li>Revisión de las acciones realizadas por los usuarios en la Aplicación</li>
				<li>Configuración general de la Aplicación. </li>
			</ul>
		</div>
	</div>              
</div>
';
    $blocks['content']  = (isset($blocks['content']) ? (strpos($blocks['content'], '{{block.1b3231655cebb7a1f783eddf27d254ca}}') === FALSE ? $blocks['content'] : str_replace('{{block.1b3231655cebb7a1f783eddf27d254ca}}', $buffer1, $blocks['content'])) : $buffer1);
    echo Haanga::Load('default.phtml', $vars155830f55f3d98, TRUE, $blocks);
    if ($return == TRUE) {
        return ob_get_clean();
    }
}