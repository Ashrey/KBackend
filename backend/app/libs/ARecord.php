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

    const _SELF_     = 0;
    const ONE_TO_ONE = 1;


    protected $logger = true;

    protected static $database = 'backend';

    protected static $relationship = array();




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
        $this->init();
        $this->relation(self::_SELF_, get_called_class());
    }


    protected function init(){

    }

    public static function query($sql, $values = NULL){
        $search  = array();
        $replace = array();
        foreach (static::$relationship as $type) {
            foreach ($type as $table => $val) {
                $var       = explode('\\', $table);
                $search[]  = end($var);
                $replace[] = $table::getTable();
            }
        }
        $sql = str_replace($search, $replace, $sql);
        return parent::query($sql, $values);
    }

    protected function relation($type, $table){
        self::$relationship[$type][$table] = array();
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
