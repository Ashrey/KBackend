<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KumbiaAuth;
require_once CORE_PATH . 'kumbia/controller.php';
/*carga las configuraciones del backend*/
\KBackend\Libs\Config::read('backend');
class AuthController extends \Controller
{

    /**
     * variable que indica si las acciones del controller son protegidas
     * 
     * Por defecto todas las acciones son protegidas
     * para indicar que solo algunas acciones van a ser protegidas debe
     * crearse un array con los nombres de dichas acciones, ejemplo:
     * 
     * <code>
     * protected $_protected_actions = array(
     *                          'ultimos_envios',
     *                          'editar',
     *                          'eliminar',
     *                          'activar',
     *                      );
     * </code>
     * 
     * @var array
     */ 
    protected $_protectedActions = array();

    /**
     * @var Object Objeto encargado de hacer el auth
     */
    protected $_authACL = null;

    /**
     * Check if user have permission for the resource
     */
    protected $_checkPermission = true;

    /**
     * Función que hace las veces de contructor de la clase.
     * 
     */ 
    protected function initialize()
    {
        $class = Config::get('backend.security.auth');
        $obj = new $class();
        KumbiaAuth::inject(new Auth($obj));
        $this->_authACL = AuthACL::getInstance();
        if (empty($this->_protectedActions) || in_array($this->action_name , $this->_protectedActions)){  
            return $this->checkAuth();          
        }
    }

    /**
     * Función que hace todos las validaciones necesarias para controladores
     * y acciones protegidas.
     * 
     * Verifica que el usuario esté logueado, si no es así le muestra el form de 
     * logueo.
     * 
     * si está logueado verifica que tenga los permisos necesarios para acceder
     * a la acción correspondiente.
     * 
     * @return boolean devuelve TRUE si tiene acceso a la acción.
     * 
     */ 
    protected function checkAuth(){
        if (KumbiaAuth::isLogin()) {
            return !$this->_checkPermission || $this->_isAllow();
        } elseif (\Input::hasPost('login')) {
            $this->_valid();
            \Redirect::toAction(\Router::get('action'));
        } else {
            \View::select(null, 'logueo');
            return FALSE;
        }

    }

    /**
     * Verifica si el usuario conectado tiene acceso a la acción actual
     * 
     * @return boolean devuelve TRUE si tiene acceso a la acción.
     */
    protected function _isAllow()
    {
        if (!$this->_authACL->check()) {
            \Flash::error('No posees privilegios para acceder a ' . \Router::get('route'));
            \View::select(null, 'forbidden');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Realiza la autenticacón con los datos enviados por formulario
     * 
     * Si se realiza el logueo correctamente, se verifica que tenga permisos
     * para entrar al recurso actual.
     * 
     * @return boolean devuelve TRUE si se pudo loguear y tiene acceso a la acción.
     * 
     */ 
    protected function _valid()
    {
        KumbiaAuth::login(\Input::post('login'));
        if (KumbiaAuth::isLogin()) {
            Event::fired('LoginSuccess');
            return $this->_isAllow();
        } else {
            Event::fired('LoginFail');
            \Flash::warning('Datos de acceso  no válidos');
            \View::select(null,'logueo');
            return FALSE;
        }
    }

    /**
     * Acción para cerrar sesión en la app
     * 
     * Cualquier controlador que herede de esta clase
     * tiene acceso a esta acción.
     * 
     */ 
    public function logout()
    {
        KumbiaAuth::logout();
        return \Redirect::to('/');
    }

    /**
     * Método que se ejecuta luego de ejecutada la acción y filtros 
     * del controlador.
     * 
     */ 
    protected function finalize()
    {

    }

}
