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
use \Validate;
class ARecord extends \Kumbia\ActiveRecord\ActiveRecord implements \ArrayAccess {


    protected $logger = true;

    /**
     * Obtiene nombre de tabla
     * 
     * @return string
     */
    public static function getTable()
    {
        $class = explode('\\', get_called_class());
        $name = strtolower(end($class));
        return "_$name";
    }

    protected function _beforeSave(){
        $rules = $this->_rules();
        $val = new Validate($this, $rules);
        if($val->exec()){
            return true;
        }else{
            $error = $val->getMessages();
            foreach ($error as $value)
                \Flash::error($value);
            return false;
        }
    }

    public function unique($field){
        return static::count("$field = ?",$this->$field);
    }
    


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
    
    public function get_alias($key=null){
        return ucfirst(str_replace('_', ' ',$key));
	}
}
