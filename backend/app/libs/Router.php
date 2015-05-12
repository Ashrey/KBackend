<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */

class Router extends \KumbiaRouter {
	/**
	 * Carga y devuelve una instancia del controllador
	 */
	static function getController($param) {
		$cname = ucfirst($param['controller']);
		$name  = "\\KBackend\\Controller\\{$cname}Controller";
		return new $name($param);
	}
}
