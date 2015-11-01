<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class FilterSQL {
	/**
	 * Argumentos del PAGINATE
	 * @var array
	 */
	protected $_arg = array();
	
    /**
     * Valid param on URL
     * @var array
     */
	protected $_valid = array('page', 'order', 'per_page', 'desc', 'col', 'val');

    /**
     * Value of filter
     * @var mixed
     */
    protected $value = 1;

	private function __construct() {
        foreach($this->_valid as $key){
            $val = filter_input(INPUT_GET, $key, FILTER_VALIDATE_REGEXP,
                array("options"=>array("regexp"=>"/^\w{1,}$/")));
			if($val){
				$this->_arg[$key] = $val;
			}
		}
    }

    /**
     * Singleton
     */
    static public function get(){
        static $obj;
        return is_object($obj) ? $obj:new self();
    }

    /**
     * Return the SQL array
     * @var 
     * @return array
     */
    function getSQLArray($where='') {
        $param = $this->_arg;
        $col = isset($param['col'])?$param['col']:1;
        $this->value = isset($param['val'])?$param['val']:1;
        $op = '=';
        if(!is_numeric($this->value)){
            $this->value = "%{$this->value}%";
            $op  = 'LIKE';
        }
        $param['page']  = isset($param['page'])? $param['page']:1;
        $param['where'] = "$col $op :filter" . (empty($where)?'': " AND ($where)");
        if(isset($param['order']) && isset($param['desc'])){
            $desc = $param['desc']?' DESC':'';
            $param['order'] = "{$param['order']} $desc";
        }
        return $param;
    }

    /**
     * Get array of SQL Values
     * @return array
     */
    function getValues(){
        return array('filter' => $this->value);
    }

    /**
     * Get the arguments
     * @return array
     */
    function getArgs() {
        return $this->_arg;
    }

    /**
     * Return URL for filter
     * @param  Array $arg array of options
     * @return String
     */
    function getURL(Array $arg){
        if(isset($arg['order']) && isset($this->_arg['order'])
            && $arg['order'] == $this->_arg['order']){
            $arg['desc'] = !isset($this->_arg['desc']) || !$this->_arg['desc'];
        }
		$arg = array_merge($this->_arg, $arg);
		$action=implode('/', \Router::get('parameters'));
		return "$action?".http_build_query($arg);
	}

	public function __get($name) {
		return $this->_arg[$name];
	}
	
	public function __set($name, $value) {
		$this->_arg[$name] = $value;
	}
}
