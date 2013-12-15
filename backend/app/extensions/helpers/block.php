<?php

/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Permite almacenar bloques de codigos en las vistas
 * 
 * @category   Kumbia
 * @package    Block 
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class Block {
	/**
	 * Almacena los bloques 
	 * @var array
	 */
	static protected $block = array();

	static protected $sblock = null;
	
	static protected $bcurrent = null;

	static public $_extends = false;

	/**
	 * Comienza un bloque
	 * @param  String $key Nombre del bloque
	 * @return void
	 */
	static function start($key){
		self::$bcurrent = $key;
		ob_start();
	}
	
	/**
	 * Finaliza un bloque
	 * @return [type]             [description]
	 */
	static function end(){
		if(is_null(self::$bcurrent))
			throw new KumbiaException('Se ha intentado cerrar un bloque no abierto');
		/*Estoy en la extensión*/
		if(self::$_extends){
			self::$block[self::$bcurrent] = ob_get_clean();
		}else{ /*estoy en la base*/
			if(isset(self::$block[self::$bcurrent])){ ;
				$block = self::$bcurrent;
				echo str_replace("{&[#(@$block@)#]&}", ob_get_clean(),self::$block[self::$bcurrent]);
			}else{
				echo ob_get_clean();
			} 
		}
		//establece de nuevo a null
		self::$bcurrent = null;
	}
	
	static function super(){
		$block = self::$bcurrent;
		return "{&[#(@$block@)#]&}";
	}

	/**
	 * Establece el valor de un bloque
	 * @param string $key   Nombre del Bloque
	 * @param string $value Valor a establecer
	 */
	static function set($key, $value){
		self::$block[$key] = $value;
	}

	/**
	 * Comienza extension 
	 */
	static function child(){
		self::$_extends = true;
	}

	/**
	 * Extender el archivo
	 */
	static function extend($file){
		self::$_extends = false;
		extract(get_object_vars(View::get('controller')), EXTR_OVERWRITE);
	    $__file = APP_PATH . "views/_shared/$file.phtml";
        // carga la vista
        if (!include $__file)
            throw new KumbiaException('Vista "' . $__file. '" no encontrada', 'no_view');

	}

}
