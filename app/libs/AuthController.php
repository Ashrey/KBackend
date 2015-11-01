<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KumbiaAuth;use \Redirect;

class AuthController extends Controller {

	/**
	 * By default all action are protected
	 * If this array is not empty, only action in array will be protected
	 *
	 * <code>
	 * protected $_protectedActions = array(
	 *                          'edit',
	 *                          'delete'
	 *                      );
	 * </code>
	 *
	 * @var array
	 */

	protected $_protectedActions = array();

	/**
	 * @var Object auth object
	 */
	protected $_authACL = null;

	/**
	 * Check if user have permission for the resource
	 */
	protected $_checkPermission = true;

	/**
	 * Initialization class
	 *
	 */

	protected function initialize() {
		$class = Config::get('backend.security.auth');
		$obj   = new $class();
		KumbiaAuth::init(new Auth($obj));
		$this->_authACL = AuthACL::getInstance();
		if (empty($this->_protectedActions) || in_array($this->action_name, $this->_protectedActions)) {

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
	 * @return bool True if the credentials are valid
	 *
	 */

	protected function checkAuth() {
		if (KumbiaAuth::isLogin()) {
			return !$this->_checkPermission || $this->_isAllow();
		} elseif (\Input::hasPost('login')) {
			return $this->_valid();
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
	protected function _isAllow() {
		if (!$this->_authACL->check()) {
			\Flash::error('No posees privilegios para acceder a '.\Router::get('route'));
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
	 *
	 */

	protected function _valid() {
		KumbiaAuth::login(\Input::post('login'));
		if (KumbiaAuth::isLogin()) {
			Event::fired('LoginSuccess');
			\Redirect::to('');
		} else {
			Event::fired('LoginFail');
			\Flash::warning('Datos de acceso  no válidos');
			\View::select(null, 'logueo');
		}
	}

	/**
	 * Acción para cerrar sesión en la app
	 *
	 * Cualquier controlador que herede de esta clase
	 * tiene acceso a esta acción.
	 *
	 */

	public function logout() {
		KumbiaAuth::logout();
		return \Redirect::to('/');
	}

	/**
	 * Método que se ejecuta luego de ejecutada la acción y filtros
	 * del controlador.
	 *
	 */

	protected function finalize() {

	}

}
