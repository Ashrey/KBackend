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
 * Clase de Autenticacón por objeto genérico
 * 
 * @category   Kumbia
 * @package    Auth
 * @subpackage Adapters
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class ObjectAuth extends Auth2
{
	
	/**
	 * Nombre del objeto a llamar
	 */
	protected $obj = null;
	/**
	 * Argumento a pasar al callback
	 */
	protected $arg = null;
	
	public function setOption($obj, $arg = array()){
		$this->obj = $obj;
		$this->arg = $arg;
	
	}
    /**
     * Check
     * 
     * @param $username
     * @param $password
     * @return bool
     */
	
	protected function _check($username, $password){
        // TODO $_SERVER['HTTP_HOST'] puede ser una variable por si quieren ofrecer autenticacion desde cualquier host indicado
        if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === FALSE) {
            self::log('INTENTO HACK IP ' . $_SERVER['HTTP_REFERER']);
            $this->setError('Intento de Hack!');
            return FALSE;
        }
        
        if(empty($this->obj)){
			throw new KumbiaException('No se a establecido el objeto');
		}
        
        //$username = addslashes($username);
        $username = filter_var($username, FILTER_SANITIZE_MAGIC_QUOTES);
		
		
		
		/*instancia al objeto*/
		$obj = new $this->obj();
		$arg = array_merge($this->arg, array($this->_login=>$username, $this->_pass =>$password)); 
		$result = $obj->auth($arg);
        if ($result) {
            $data =  get_object_vars($result);
            foreach ($data as $field => $value) {
                Session::set($field, $value, parent::$_sessionNamespace);
            }
            Session::set($this->_key, TRUE);
            return TRUE;
        }
        $this->setError('Error Login!');
        Session::set($this->_key, FALSE);
        return FALSE;
    }
    
}
