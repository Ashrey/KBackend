<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app
define('KBACKEND_PATH', dirname(__DIR__));
function autoload_kbackend($name) {
	if (strncmp($name, 'KBackend\\', 8) == 0) {
		$split = explode('\\', $name);
		array_shift($split);
		$type  = array_shift($split);
		$class = implode(DIRECTORY_SEPARATOR, $split);
		$keys  = ['Model' => 'models', 'Libs' => 'libs', 'Controller' => 'controllers'];
		$dir   = $keys[$type];
		if (!include KBACKEND_PATH."/$dir/$class.php") {
			throw new KumbiaException("Clase $class no encontrada");
		}
	}
}
spl_autoload_register('autoload_kbackend');
include (CORE_PATH.'kumbia/kumbia_autoload.php');
spl_autoload_register(array('KumbiaAutoload', 'autoload'));
//add autoload
require dirname(KBACKEND_PATH).'/vendor/autoload.php';
