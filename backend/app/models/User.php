<?php
namespace KBackend\Model;
use \KBackend\Libs\Paginator;
use \Validate;
use \Flash;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class User extends \KBackend\Libs\ARecord {

    protected function init(){
        $this->oneToOne('KBackend\Model\Role');
        $this->oneToMany('KBackend\Model\Action');
    }

    public static function _rules() {
        return array(
            'login' => array(
                'required' => array('error' =>'Debe escribir un <strong>Login</strong>'),
                'alphanum',
                'length' =>  array('max' =>20, 'min' =>4),
                '@unique'  => array('error' => 'usuario ya registrado')
            ),
            'email' => array(
                'required',
                'email',
                '@unique' => array('error' => 'email ya registrado'),
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

    protected function _beforeSave() {
        /*Not is a hash*/
        if(isset($this->password) && $this->password[0] != '$')
            $this->password = \KBackend\Libs\AuthACL::hash($this->password);
    }

    protected function _beforeCreate(){
        $this->created_at = date('Y-m-d H:i:s');
    } 

    /**
     * Devuelve el SQL para paginación
     * @return string sql
     */
    public static function index() {
        return  array(
            'join'   => 'JOIN Role r ON r.id = role_id',
            'fields' => 'User.id, User.login, User.email, r.role rol',
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
        $this->begin(); //iniciamos una transaccion
        $this->enable = 0;//por defecto las cuentas están desactivadas
        $this->role_id = 0;//el minimo de permisos
        $val = array(
            'password' => array(
                'required' => array('error' =>'Debe escribir un <strong>Login</strong>'),
                'length' =>  array('min' =>4),
            ),
            'password2' => array(
                'required',
                'equal' => array(
                    'to' => $this->password,
                    'error' => 'Las contraseñas no coinciden'
                 )
            )  
        );
        $fail = Validate::fail($this, $val);
        if ($fail || !$this->save()){
            if($fail) Validate::errorToFlash($fail);
            $this->rollback();
            throw new \Exception('Existen datos que no son válidos');
        }
        $hash = $this->hash();
        $correo = new Email();
        $correo->register($this, $hash);
        $this->commit(); 
    }
    
    /**
     * Envia un link de recuperación
     */
    public static function forget($by){
        $user = self::first(
                array('where' => 'email = :value OR login=:value'),
                array(':value' => $by)
            );
        if(!$user)
            throw new \Exception('Usuario o email no válido');
        $user->begin(); //iniciamos una transaccion
        $user->created_at = date("Y-m-d G:i:s");
        if ($user->save()) {
            $hash = $user->hash();
            $email = new Email();
            $email->forget($user, $hash);
            $user->commit();   
        } else {
            static::rollback();
            throw new \Exception('Existen datos que no son válidos');
        }
        return true;
    }
    
     /**
     * Permite generar una contraseña nueva al usuario y enviarla a su correo 
     */
    public function changePassword($hash){
        $this->begin(); //iniciamos una transaccion
        $this->created_at = date("Y-m-d G:i:s");
        $pass = substr( str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789$@&'), 0, 8);
        $this->password =  \KBackend\Libs\AuthACL::hash($pass);
        if ($this->save()) {
            $correo = new Email();
            if ($correo->forget($this, $pass)) {
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

    /**
     * Activa un usaurio via correo
     * @param int $id_
     * @param string $hash
     * @return boolean
     */
    public function active($id, $hash) {
        if ($this->hash() === $hash && ($this->enable === '0')) {
            $this->enable = 1;
            return $this->save();
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
            'fields' =>  'User.*,COUNT(Action.id) as total',
            'join'   =>  'LEFT JOIN Action ON User.id = Action.user_id',
            'group'  => 'User.id'
        );
        return self::paginate($arg,  $page, \Config::get('backend.app.per_page'));
    }


}
