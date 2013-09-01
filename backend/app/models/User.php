<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class User extends \KBackend\Libs\ARecord {

    protected $source = '_user';

    protected function initialize() {
        $this->validates_presence_of('login', 'message: Debe escribir un <b>Login</b> para el Usuario');
        $this->validates_format_of('login', '/^[a-zA-z0-9]+$/','message: Su login solo puede contener número y/o letras');
        $this->validates_presence_of('password', 'message: Debe escribir una <b>Contraseña</b>');
        $this->validates_presence_of('clave2', 'message: Debe volver a escribir la <b>Contraseña</b>');
        $this->validates_presence_of('email', 'message: Debe escribir un <b>correo electronico</b>');
        $this->validates_email_in('email', 'message: Debe escribir un <b>correo electronico</b> válido');
        $this->validates_uniqueness_of('login', 'message: El <b>Usuario</b> ya está registrado');
        $this->validates_uniqueness_of('email', 'message: El <b>Email</b> ya está registrado');
    }

    protected function before_save() {
        if (\Input::hasPost('user')){
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
     * Devuelve los usuarios de la bd Paginados.
     * 
     * @param  integer $pagina numero de pagina a mostrar
     * @return array          resultado de la consulta
     */
    public function paginar($cond) {
        $cond['join'] =  ' JOIN _role r ON r.id = role_id';
        $cond['columns'] = ' _user.id, _user.login, _user.email, r.role rol';
        return $this->paginate($cond);
    }

    public function numAcciones($pagina = 1) {
        $cols = "_user.*,COUNT(_action.id) as total";
        $join = "LEFT JOIN _action ON _user.id = _action.user_id";
        $group = '_user.' . join(',_user.', $this->fields);
        $sql = "SELECT $cols FROM $this->source $join GROUP BY $group";
        return $this->paginate_by_sql($sql, "page: $pagina", 'per_page: ' . \Config::get('backend.app.per_page'));
    }

    /**
     * Realiza un cambio de clave de usuario.
     * 
     * @param  array $datos datos del formulario
     * @return boolean devuelve verdadero si se realizó el update
     */
    public function cambiarClave(array $datos) {
        $this->clave = $datos['nueva_clave'];
        $this->clave2 = $datos['nueva_clave2'];
        return $this->update();
    }

    /**
     * Realiza el proceso de registro de un usuario desde el frontend.
     * @return boolean true si la operación fué exitosa.
     */
    public function register() {
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
				return false;
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

    function auth($arg) {
        $clave = $arg['password'];
        $user = $arg['login'];
        return $this->find_first("password = '$clave' AND login = '$user' AND enable='1'");
    }

}
