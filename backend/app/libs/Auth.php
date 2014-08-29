<?php
namespace KBackend\Libs;
use \Session;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Auth implements \KumbiaAuthInterface
{
	
	/**
	 * Almacena el valor para el singlenton
	 */
	static $_auth = null;

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
		if(!is_object($obj)){
			throw new \Exception('Expecting an object. A '. gettype($obj). ' given');
		}
		$this->obj = $obj;

		/*Carga los datos de la sesión*/
		$this->_store = \Session::get('store', $this->_key);
    }

	
	public function login($arg = array()){
        if(empty($arg['user'])){
			throw new \KumbiaException('user not get');
		}
        $arg['password'] = AuthACL::hash($arg['password']);
		/*instancia al objeto*/
		$result =$this->obj->auth($arg);
        if ($result) {
            $this->_store =  get_object_vars($result);
            Session::set('store', $this->_store, $this->_key);
            Session::set('login', FALSE, $this->_key);
            $is_login = TRUE;
        }else{
        	$is_login = FALSE;
        }
        Session::set('login', $is_login, $this->_key);
        return $is_login;
    }

    /**
     * Obtiene un valor de la identidad actual
     * 
     * @param string $var 
     * @return mixed
     */
    public function get($var)
    {
		return isset($this->_store[$var]) ? $this->_store[$var] : null; 
    }
    
}
