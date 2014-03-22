<?php

namespace KBackend\Libs;

/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class AuthACL {

    /**
     * Namespace de las cookies y el hash de clave que se va a encriptar
     * Recordar que si se cambian, se deben actualizar las claves en la bd.
     */
    protected static $_hash = '$2a$05$AcoE7zCEG276ztq4bGUADu';

    /**
     * Objeto Acl2
     *
     * @var SimpleAcl
     */
    protected $_acl = null;

    /**
     * Objeto Auth
     * @var iAuth
     */
    protected $_auth = null;
    
    /**
     * Variable para mantener una unica instancia
     * @var AuthACL 
     */
    protected static $_obj = null; 


    protected function __construct(iAuth $auth, $acl) {
        $this->_auth = $auth;
        $this->_acl = \Acl2::factory($acl);
        $this->_assignAccess();
    }
    

    /**
     * Devuelve un objeto de Autenticación y Permisos
     */
    static public function getInstance() {
        $tmp = self::$_obj; /*da error si uso el self en el if*/
        if(!($tmp instanceof self)){
            $class = Config::get('backend.security.auth');
            $acl   = Config::get('backend.security.acl');
            if(!class_exists($class)){
                throw new \Exception("Class $class configured for Auth do not exists");   
            }
            $obj = new $class();
            self::$_obj =  new self(Auth::getInstance($obj), $acl);
        }
        return self::$_obj;
    }

    /**
     * Realiza el proceso de autenticación de un usuario en el sistema.
     * @param  string  $user      
     * @param  string  $pass      
     * @return boolean             
     */
    public function login($user, $pass) {
        $pass = self::hash($pass);
        $this->_auth->login(array('user'=>$user,'password' => $pass));
        $this->_assignAccess();  
    }


    /**
     * Crea una encriptacion de la clave para el usuario.
     * 
     * Usada para la verificación al loguear y cuando se crea un user en la bd.
     * 
     * @param  string $pass 
     * @return string       
     */
    public static function hash($pass) {
        return crypt($pass, self::$_hash);
    }



    /**
     * Establece los recursos a los que un rol tiene acceso
     */
    protected function _assignAccess() {
        $id = $this->_auth->get('role_id');
        $res = \KBackend\Model\Role::getResource($id);
        //establecemos los recursos permitidos para el rol
        $urls = array();
        while (($e = $res->fetch())) {
            $urls[] = empty($e->url)?null: $e->url;
        }
         //damos permiso al rol de acceder al arreglo de recursos
        $this->_acl->allow($id, $urls);
        $this->_acl->user($this->_auth->get('id'), array($id));
    }

    /**
     * Verifica si el usuario conectado tiene permisos de acceso al recurso actual
     *
     * Por defecto trabaja con el id del usuario en sesión.
     * Ademas hace uso del Router para obtener el recurso actual.
     *
     * @return boolean resultado del chequeo
     */
    public function check() {
        $id = $this->_auth->get('id');
        $controller = \Router::get('controller');
        $action = \Router::get('action');
        $module = \Router::get('module') ? \Router::get('module') . '/': '';
        //posibles permisos
        $direct = "{$module}$controller/$action";
        $control = "{$module}$controller/*";  //por si tiene acceso a todas las acciones
        $all = "*/*";  //por si tiene acceso a todos los controladores
        //si se cumple algunas de las codiciones, el user tiene permiso.
        return $this->_acl->check($direct, $id) ||
                $this->_acl->check($control, $id) ||
                $this->_acl->check($all, $id);
    }

    /**
     * isLogin
     */
    public static function isLogin(){
        $obj = self::getInstance();
        return $obj->_auth->isLogin();
    }

    /**
     * Logout
     */
    public function logout(){
        $obj = self::getInstance();
        return $obj->_auth->logout();
    }

    public static function get($var){
        $obj = self::getInstance();
        return $obj->_auth->get($var);
    }
}