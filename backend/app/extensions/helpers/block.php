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
	
	static protected $bcurrent = null;

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
	 * @return void 
	 */
	static function end(){
		if(is_null(self::$bcurrent))
			throw new KumbiaException('Se ha intentado cerrar un bloque no abierto');
		self::$block[self::$bcurrent] = ob_get_clean();
		self::$bcurrent = null;
	}
	
	/**
	 * Devuelve el contenido de un bloque, en caso de no existir
	 * devuelve lo estableciod en default
	 * @param  string $key     Nombre del bloque
	 * @param  string $default Texto por defecto
	 * @return String          Contenido del bloque
	 */
	static function get($key, $default = ''){
		echo isset(self::$block[$key]) ? self::$block[$key] :  $default;
	}
	
	/**
	 * Establece el valor de un bloque
	 * @param string $key   Nombre del Bloque
	 * @param string $value Valor a establecer
	 */
	static function set($key, $value){
		self::$block[$key] = $value;
	}

}
