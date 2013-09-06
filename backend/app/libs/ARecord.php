<?php
namespace KBackend\Libs;
/**
 * ActiveRecord
 *
 * Esta clase es la clase padre de todos los modelos
 * de la aplicacion
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */

class ARecord extends \KumbiaActiveRecord implements \ArrayAccess {


    protected $logger = true;

    public function offsetSet($indice, $valor) {
        if (!is_null($indice)) {
            $this->{$indice} = $valor;
        }
    }

    public function offsetExists($indice) {
        return isset($this->{$indice});
    }

    public function offsetUnset($indice) {
        //no se pueden quitar atributos.
    }

    public function offsetGet($indice) {
        return $this->offsetExists($indice) ? $this->{$indice} : NULL;
    }
    
    public static function __callStatic($name, $args){
		$model = get_called_class();
		$obj = new $model();
		$name = substr($name, 1);
		return call_user_func_array(array($obj, $name), $args);
    }
    
    public function get_alias($key=null){
		 if ($key && array_key_exists($key, $this->alias)) {
            return $this->alias[$key];
        } else {
            return ucfirst($key);
        }
        return $this->alias;
	}
	
	public function paginate($arg=null){
        $arg = is_array($arg)? $arg : func_get_args();
        array_unshift($arg, $this);
        return  \KBackend\Libs\Paginate::paginate($arg);
    }

}
