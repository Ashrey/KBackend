<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app
define('KBACKEND_PATH', dirname(dirname(__FILE__)));
function autoload_kbackend($name) {
    if (strncmp($name, 'KBackend', 7) == 0 ) {
        $split = explode('\\', $name);
        $class = $split[2];
        if ($split[1] == 'Model') {
            if (!include  KBACKEND_PATH."/models/$class.php") {
                throw new KumbiaException("Clase $class no encontrada");
            }
        } elseif ($split[1] == 'Libs') {
            if (!include KBACKEND_PATH."/libs/$class.php"){
                throw new KumbiaException("Clase $class no encontrada");
            }
        }
    }elseif ($name == 'Logger'){
		/**
		 * Esto es necesario para no dar problemas con el logger
		 */
		 include  KBACKEND_PATH."/libs/Logger.php";
    }
}

spl_autoload_register('autoload_kbackend');
$__dir = dirname(dirname(KBACKEND_PATH)).'/vendor';
require $__dir .'/Haanga/lib/Haanga/Loader.php';
require $__dir. '/ActiveRecord/lib/Kumbia/ActiveRecord/Autoloader.php';
\Kumbia\ActiveRecord\Autoloader::register();
