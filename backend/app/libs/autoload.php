<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app
function autoload_kbackend($name) {
    if (strncmp($name, 'KBackend', 7) == 0 ) {
        $split = explode('\\', $name);
        $class = $split[2];
        if ($split[1] == 'Model') {
            if (!include APP_PATH . "models/$class.php") {
                throw new KumbiaException("Clase $class no encontrada");
            }
        } elseif ($split[1] == 'Libs') {
            if (!include APP_PATH . "libs/$class.php"){
                throw new KumbiaException("Clase $class no encontrada");
            }
        }
    }elseif ($name == 'Logger'){
		/**
		 * Esto es necesario para no dar problemas con el logger
		 */
		 include APP_PATH . "libs/Logger.php";
    }
}

spl_autoload_register('autoload_kbackend');