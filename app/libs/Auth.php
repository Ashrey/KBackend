<?php
namespace KBackend\Libs;
use \Session;
use \KumbiaAuth;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */

class Auth implements \KumbiaAuthInterface {

	/**
	 * @var Array almacena valores del auth
	 */
	protected $_store = array();

	/**
	 * Nombre del objeto a llamar
	 */
	protected $obj = null;

	/**
	 * Clave de sesion
	 *
	 * @var string
	 */
	protected $_key = 'jt2D14KIdRs7LA==';

	function __construct($obj) {
		if (!is_object($obj)) {
			throw new \Exception('Expecting an object. A '.gettype($obj).' given');
		}
		$this->obj = $obj;

		/*Carga los datos de la sesión*/
		$this->_store = \Session::get('store', $this->_key);
	}

	public function login(Array $arg = array()) {
		$arg['password'] = AuthACL::hash($arg['password']);
		/*instancia al objeto*/
		$result =  $this->obj->auth($arg);
		if ($result) {
			$this->_store = get_object_vars($result);
			Session::set('store', $this->_store, $this->_key);
			$result->last_login = date('c');
			$result->save();
		}
		return is_object($result);
	}

	/**
	 * Obtiene un valor de la identidad actual
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function get($var) {
		return isset($this->_store[$var])?$this->_store[$var]:null;
	}

}
