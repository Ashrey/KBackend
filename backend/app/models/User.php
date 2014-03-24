<?php
namespace KBackend\Model;
use \KBackend\Libs\Paginator;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class User extends \KBackend\Libs\ARecord {

    public static function _rules() {
        return array(
            'login' => array(
                'required' => array('error' =>'Debe escribir un <strong>Login</strong>'),
                'alphanum',
                'length' =>  array('max' =>20, 'min' =>4),
            ),
            'email' => array(
                'required',
                'email',
            ),   
        );
    }

    public static function _formOption(){
        return array(
            'enable' => array(
                'type'    => 'check',
                
            ),
            'role_id' => array(
                'type' => 'select',
                'data' => array('\KBackend\Model\Role', 'all')
            )
        );
    }

    public static function _formFields(){
        return array('login', 'password', 'email', 'role_id', 'enable');
    }

    protected function before_save() {
        if (\Input::hasPost('user')){
			if(!isset($data['pasword']))return;
			$data = \Input::post('user');
			if($data['password'] === $data['clave2']){
				$this->password = \KBackend\Libs\AuthACL::hash($data['password']);
			}else{
				 \Flash::error('Las <strong>Claves</strong> no coinciden');
				return 'cancel';
			}
        }
    }

    /**
     * Devuelve el SQL para paginación
     * @return string sql
     */
    public static function index() {
        return  array(
            'fields' => '_user.id, _user.login, _user.email, r.role rol',
            'join'   => 'JOIN _role r ON r.id = role_id',
        );
    }

    /**
     * Get the row for user with id = $id
     * @param int $id id of user
     * @return Object
     */
    static function view($id) {
        $param = array(
          'fields' => '_user.id, login, email, role',
          'join' =>  'JOIN _role ON _role.id = role_id',
          'where' => '_user.id = ? '
        );
        return self::first($param, array($id));
    }


    /**
     * Realiza el proceso de registro de un usuario desde el frontend.
     * @return boolean true si la operación fué exitosa.
     */
    public function register() {
		$data = \Input::post('user');
		if(!($data['captcha'] === \Session::get('captcha'))){
			\Flash::info($data['captcha']);
			var_dump(\Session::get('captcha'));
			throw new \Exception('La letra introducida no es válida');
		}
        $clave = $this->password;
        $this->begin(); //iniciamos una transaccion
        $this->enable = '0';//por defecto las cuentas están desactivadas
        $this->role_id = '3';//el minimo de permisos
        if ($this->save()) {
            $hash = $this->hash();
            $correo = new Email();
            if ($correo->enviarRegistro($this, $clave, $hash)) {
                $this->commit();   
            } else {
				$this->rollback();
				throw new \Exception($correo->getError());
            }
        } else {
            $this->rollback();
            throw new \Exception('Existen datos que no son válidos');
        }
        return true;
    }
    
    /**
     * Envia un link de recuperación
     */
    public function forget(){
        $this->begin(); //iniciamos una transaccion
        $this->created_at = date("Y-m-d G:i:s");
        if ($this->save()) {
            $hash = $this->hash();
            $correo = new Email();
            if ($correo->sendPass($this, $hash)) {
                $this->commit();   
            } else {
				$this->rollback();
				throw new \Exception($correo->getError());
            }
        } else {
            $this->rollback();
            throw new \Exception('Existen datos que no son válidos');
        }
        return true;
	}
	
	 /**
     * Permite generar una contraseña nueva al usuario y enviarla a su correo 
     */
    public function newpass($id, $hash){
		if ($this->find_first((int) $id)) { //verificamos la existencia del user
			if(!($this->hash() === $hash)){
				throw new \Exception('Hash de validación no válido');
			}
			$this->begin(); //iniciamos una transaccion
			$this->created_at = date("Y-m-d G:i:s");
			$pass = substr( str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789$@&'), 0, 8);
			$this->password =  \KBackend\Libs\AuthACL::hash($pass);
			if ($this->save()) {
				$correo = new Email();
				if ($correo->sendNewPass($this, $pass)) {
					$this->commit();   
				} else {
					$this->rollback();
					throw new \Exception($correo->getError());
				}
			} else {
				$this->rollback();
				throw new \Exception('Existen datos que no son válidos');
			}
		}
        return true;
    }

    /**
     * Activa un usaurio via correo
     * @param int $id_
     * @param string $hash
     * @return boolean
     */
    public function active($id, $hash) {
        if ($this->find_first((int) $id)) { //verificamos la existencia del user
            if ($this->hash() === $hash && ($this->enable === '0' or  $this->enable === '1')) {
                $this->enable = 1;
                return $this->save();
            }
        }
        return FALSE;
    }

    /**
     * Devuelve el hash de identificacion de usuario registrado
     * @return String
     */
    function hash() {
        return sha1($this->login . $this->id . $this->password. $this->created_at);
    }

    /**
     * Make auth
     * @param $arg Array
     * @return bool Valid auth
     */
    public static function auth($arg) {
        $where = array('where' =>
            'password = :password AND login = :user AND enable=1'
        );
        return self::first($where, $arg);
    }

    /**
     * Paginate user with their number of action
     * @param integer $page number of page
     */
    public static function actions($page = 1) {
        $arg = array(
            'fields' =>  '_user.*,COUNT(_action.id) as total',
            'join'   =>  'LEFT JOIN _action ON _user.id = _action.user_id',
            'group'  => '_user.id'
        );
        return self::paginate($arg,  $page, \Config::get('backend.app.per_page'));
    }


}
