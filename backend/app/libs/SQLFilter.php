<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class SQLFilter {
	
	/**
	 * Argumentos del PAGINATE
	 */
	protected $_arg = array();
	
	protected $_valid = array('page', 'order'); 
	
	/**
	 * Singleton
	 */
	static public function get(){
		static $obj;
		return is_object($obj) ? $obj:new self();
	}
	
	private function __construct() {
        foreach($_GET as $key => $val){
			if(in_array($key, $this->_valid)){
				$this->_arg[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_ENCODED);
			}
		}
    }
	

    function getArray() {
        return $this->_arg;
    }
    
    function getURL($arg){
		$arg = array_merge($this->_arg, $arg);
		asort($arg);
		return '?'.http_build_query($arg);
	}

    /**
     * Procesa los pedidos del usuario
     */
    function request() {
        if (Input::hasPost('filter')) {
            $this->filter();
        }
    }

    /**
     * Procesa los filtros
     */
    protected function filter() {
        if (Input::post('clear')) {/* Elimina todos los filtros */
            $this->condition = array();
            Flash::info('Filtros Borrados');
        } elseif (Input::post('add')) {/* Agrega un filtro */
            $nuevo = Input::post('filter');
            $nuevo['val'] = empty($nuevo['val']) && $nuevo['val'] !== '0' ?
                    'NULL' : '"' . addslashes($nuevo['val']) . '"';
            $this->condition[uniqid()] = $nuevo;
        } elseif (Input::post('remove')) {/* Remueve un filtro */
            $key = Input::post('remove');
            unset($this->condition[$key]);
        }
    }

    
	public function __get($name) {
		return $this->_arg[$name];
	}
	
	public function __set($name, $value) {
		$this->_arg[$name] = $value;
	}
}
