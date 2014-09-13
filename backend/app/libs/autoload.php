<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app
define('KBACKEND_PATH', dirname(dirname(__FILE__)));
function autoload_kbackend($name) {
    if (strncmp($name, 'KBackend', 7) == 0 ) {
        $split = explode('\\', $name);
        array_shift($split);
        $type = array_shift($split);
        $class = implode(DIRECTORY_SEPARATOR, $split);
        if ($type == 'Model') {
            if (!include  KBACKEND_PATH."/models/$class.php") {
                throw new KumbiaException("Clase $class no encontrada");
            }
        } elseif ($type == 'Libs') {
            if (!include KBACKEND_PATH."/libs/$class.php"){
                throw new KumbiaException("Clase $class no encontrada");
            }
        }elseif($type == 'Controller') {
            if (!include KBACKEND_PATH."/controllers/$class.php"){
                throw new KumbiaException("Clase $class no encontrada");
            }
        }
    }
}

spl_autoload_register('autoload_kbackend');
$__dir = dirname(dirname(KBACKEND_PATH)).'/vendor';
require $__dir .'/Haanga/lib/Haanga/Loader.php';
require $__dir. '/ActiveRecord/lib/Kumbia/ActiveRecord/Autoloader.php';
require $__dir. '/autoload.php';
\Kumbia\ActiveRecord\Autoloader::register();
