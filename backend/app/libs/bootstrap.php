<?php
// Bootstrap de la aplicacion para personalizarlo
// Para cargar cambia en public/index.php el require del bootstrap a app
function  autoload($name){
	if(strncmp($name, 'KBackend', 7) == 0){
		$split = explode('\\', $name);
		$class = $split[2];
		if ($split[1] == 'Model'){
			include APP_PATH . "models/$class.php";
        }elseif($split[1] == 'Libs'){
			include APP_PATH . "libs/$class.php";
		}
	}
} 
spl_autoload_register ('autoload');
require_once CORE_PATH . 'kumbia/bootstrap.php';

