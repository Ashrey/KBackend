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
 * @category   Kumbia
 * @package    Auth
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

/**
 * Clase Base para la gestion de autenticación
 * 
 * @category   Kumbia
 * @package    Auth
 */
abstract class Auth2 {

    /**
     * Mensaje de Error
     *
     * @var String
     */
    protected $_error = null;

    /**
     * Verificar que no se inicie sesion desde browser distinto con la misma IP
     *
     * @var boolean
     */
    protected $_checkSession = TRUE;

    /**
     * Data de la session
     * @var Array 
     */
    protected $_data = array();

    /**
     * Adaptador por defecto
     *
     * @var string
     */
    protected static $_defaultAdapter = 'model';

    function __construct() {
        $this->_data = Session::has('data', 'KUMBIA_AUTH') ? Session::get('data', 'KUMBIA_AUTH') : array();
    }

    function __destruct() {
        Session::set('data', $this->_data, 'KUMBIA_AUTH');
    }

    /**
     * Realiza el proceso de identificacion.
     * @return bool
     */
    public function identify() {
        if ($this->isValid()) {
            return TRUE;
        } else {
            return call_user_func_array(array($this, '_check'), func_get_args());
        }
    }
    
        /**
     * Devuelve algun dato de la autenticacion
     * @param String $key
     * @return String
     */
    function get($key) {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
    

    /**
     * Realiza el proceso de autenticacion segun para cada adapter
     * 
     * @param $username
     * @param $password
     * @return bool
     */
    abstract protected function _check();

    /**
     * logout
     *
     * @param void
     * @return void
     */
    public function logout() {
        Session::set($this->_key, FALSE);
        session_destroy();
    }

    /**
     * Verifica que exista una identidad válida para la session actual
     * 
     * @return bool
     */
    public function isValid() {
        session_regenerate_id();
        if ($this->_checkSession) {
            $this->_checkSession();
        }
        return isset($this->_data['_valid']) && $this->_data['_valid'] === TRUE;
    }

    /**
     * Verificar que no se inicie sesion desde browser distinto con la misma IP
     * 
     */
    private function _checkSession() {
        Session::set('USERAGENT', $_SERVER['HTTP_USER_AGENT']);
        Session::set('REMOTEADDR', $_SERVER['REMOTE_ADDR']);

        if ($_SERVER['REMOTE_ADDR'] !== Session::get('REMOTEADDR') ||
                $_SERVER['HTTP_USER_AGENT'] !== Session::get('USERAGENT')) {
            session_destroy();
        }
    }

    /**
     * Indica que no se inicie sesion desde browser distinto con la misma IP
     * 
     * @param bool $check
     */
    public function setCheckSession($check) {
        $this->_checkSession = $check;
    }

    /**
     * Obtiene el mensaje de error
     * 
     * @return string
     */
    public function getError() {
        return $this->_error;
    }

    /**
     * Indica el mensaje de error
     * 
     * @param string $_error
     */
    public function setError($error) {
        $this->_error = $error;
    }

    /**
     * Obtiene el adaptador para Auth
     *
     * @param string $adapter (model, openid, oauth)
     */
    public static function factory($adapter = NULL) {
        if (!$adapter) {
            $adapter = self::$_defaultAdapter;
        }
        if (!include_once "auth2/adapters/{$adapter}_auth.php") {
            throw KumbiaException("El adaptador {$adapter} no existe");
        } else {
            $class = $adapter . 'auth';
            return new $class;
        }
    }

    /**
     * Cambia el adaptador por defecto
     *
     * @param string $adapter nombre del adaptador por defecto
     */
    public static function setDefault($adapter) {
        self::$_defaultAdapter = $adapter;
    }

}
