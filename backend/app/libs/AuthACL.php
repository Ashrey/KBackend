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
     * Objeto Auth2
     * @var Auth2 
     */
    protected $_auth = null;
    
    /**
     * Variable para mantener una unica instancia
     * @var AuthACL 
     */
    protected static $_obj = null; 


    protected function __construct() {
        $this->_auth = \Auth2::factory('object');
        $this->_acl = \Acl2::factory();
        $this->_assignAccess();
    }
    

    /**
     * Devuelve un objeto de Autenticación y Permisos
     */
    public function getAuth() {
        self::$_obj = self::$_obj ? self::$_obj:
                new self();
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
        $this->_auth->setOption('\KBackend\Model\User');
        $this->_auth->identify($user, $pass, 'auth');
        $this->_assignAccess();  
    }

    /**
     * Devuelve verdadero si el usuario está logueado
     * @return boolean
     */
    public function isLogin() {
        return $this->_auth->isValid();
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
         $rol = \KBackend\Model\Role::_find_first($this->_auth->get('role_id'));
        //establecemos los recursos permitidos para el rol
         $recursos = $rol->getRecursos();
        $urls = array();
        foreach ($recursos as $e) {
            $e->recurso = !empty($e->modulo) ? "$e->module/" : '';
            $e->recurso .= !empty($e->controller)? "$e->controller/":'*/';
            $e->recurso .=!empty($e->accion) ? "$e->action" : '*';
            $urls[] = $e->recurso;
        }
         //damos permiso al rol de acceder al arreglo de recursos
        $this->_acl->allow($rol->id, $urls);
        $this->_acl->user($this->_auth->get('id'), array($rol->id));
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
     * Actualiza el objeto de sesion
     */
    public static function get($key){
        $a = \Auth2::factory('object');
        return $a->get($key);
    }
    
    /**
     * Cierra la sesion de un usuario en la app.
     */
    public static function logout() {
        $a = \Auth2::factory('object');
        return $a->logout();
    }

}
