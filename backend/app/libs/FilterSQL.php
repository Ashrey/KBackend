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
	protected $_valid = array('page', 'order', 'per_page',
        'desc', 'col', 'val', 'op');

    /**
     * Value of filter
     * @var mixed
     */
    protected $value = 1;

	/**
	 * param for filter
	 * @var array
	 */
	protected $_condition = array();

	private function __construct() {
        foreach($_GET as $key => $val){
			if(in_array($key, $this->_valid)){
				$this->_arg[$key] =  filter_input(INPUT_GET, $key, FILTER_SANITIZE_STRING);
			}
		}
		if(!isset($this->_arg['page']))$this->_arg['page']=1;
    }

    /**
     * Singleton
     */
    static public function get(){
        static $obj;
        return is_object($obj) ? $obj:new self();
    }
	
    function getArray() {
        $param = $this->_arg;
        $col = isset($param['col'])?$param['col']:1;
        $op  = isset($param['op'])?$param['op']:'=';
        $this->value = isset($param['val'])?$param['val']:1;
        $param['where'] = "$col $op :filter";
        return $param;
    }

    function getValues(){
        return array('filter' => $this->value);
    }

    /**
     * Return URL for filter
     * @param  Array $arg array of options
     * @return String
     */
    function getURL(Array $arg){
        if(isset($arg['order']) && isset($this->_arg['order'])
            && $arg['order'] == $this->_arg['order']){
            $arg['order'] .= ' desc';
        }
		$arg = array_merge($this->_arg, $arg);
		asort($arg);
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
