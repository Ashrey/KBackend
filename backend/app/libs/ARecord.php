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

    protected static $database = 'backend';



    public function __construct(Array $data = array())
    {
        parent::__construct($data);
        $validate =  function(){
            $rules = $this->_rules();
            $val = new Validate($this, $rules);
            if($val->exec()){
                return TRUE;
            }
            $val->flash();
            return FALSE;
        };
        $validate->bindTo($this);
        Event::bind('ORMUpdate', $validate, $this);
        Event::bind('ORMCreate', $validate, $this);
    }

    public function create(Array $data = array()){
        if(!Event::fired('ORMCreate', $this)){
            return FALSE;
        }
        return parent::create($data);
    }

    public function update(Array $data = array()){
        if(!Event::fired('ORMUpdate', $this)){
            return FALSE;
        }
        return parent::update($data);
    }


    /**
     * Obtiene nombre de tabla
     * 
     * @return string
     */
    public static function getTable()
    {
        $class = explode('\\', get_called_class());
        $name = strtolower(end($class));
        return "kb_$name";
    }



    public function unique($field){
        $sm = self::saveMethod();
        return ($sm === 'update') || static::count("$field = ?",$this->$field) == 0;
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

    public static function _rules(){
        return array();
    }
}
