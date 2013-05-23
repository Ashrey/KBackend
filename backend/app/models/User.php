<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class User extends \KBackend\Libs\ARecord
{
	protected $source = 'user';
    const ROL_DEFECTO = 1;

    protected function initialize()
    {
        $min_clave = \Config::get('backend.app.minimo_clave');
        $this->belongs_to('roles');
        $this->has_many('auditorias');
        $this->validates_presence_of('login', 'message: Debe escribir un <b>Login</b> para el Usuario');
        $this->validates_presence_of('clave', 'message: Debe escribir una <b>Contraseña</b>');
        $this->validates_presence_of('clave2', 'message: Debe volver a escribir la <b>Contraseña</b>');
        $this->validates_presence_of('email', 'message: Debe escribir un <b>correo electronico</b>');
        $this->validates_email_in('email', 'message: Debe escribir un <b>correo electronico</b> válido');
        $this->validates_uniqueness_of('login', 'message: El <b>Login</b> ya está siendo utilizado');
        //Esto no estaba y se podía volver a crear usuario con email repertodp
        $this->validates_uniqueness_of('email', 'message: El <b>Email</b> ya está siendo utilizado');

    }

    protected function before_save()
    {
        if (isset($this->clave2) and $this->clave !== $this->clave2) {
            Flash::error('Las <b>CLaves</b> no Coinciden...!!!');
            return 'cancel';
        } else {
            $this->clave = \KBackend\Libs\MyAuth::hash($this->clave);
        }
    }

    /**
     * Devuelve los usuarios de la bd Paginados.
     * 
     * @param  integer $pagina numero de pagina a mostrar
     * @return array          resultado de la consulta
     */
    public function paginar($cond,$pagina = 1)
    {
        return $this->paginate("page: $pagina",
                'join: JOIN role r ON r.id = role_id',
                'columns: user.id, user.login, user.email, r.role rol',
                'per_page: '.\Config::get('backend.app.per_page'),
                "conditions: $cond"
               );
    }

    public function numAcciones($pagina = 1)
    {
        $cols = "user.*,COUNT(action.id) as total";
        $join = "LEFT JOIN action ON user.id = action.user_id";
        $group = 'user.' . join(',user.', $this->fields);
        $sql = "SELECT $cols FROM $this->source $join GROUP BY $group";
        return $this->paginate_by_sql($sql, "page: $pagina", 'per_page: '.\Config::get('backend.app.per_page'));
    }

    /**
     * Realiza un cambio de clave de usuario.
     * 
     * @param  array $datos datos del formulario
     * @return boolean devuelve verdadero si se realizó el update
     */
    public function cambiarClave(array $datos)
    {
        $this->clave = $datos['nueva_clave'];
        $this->clave2 = $datos['nueva_clave2'];
        return $this->update();
    }



    /**
     * Realiza el proceso de registro de un usuario desde el frontend.
     * @return boolean true si la operación fué exitosa.
     */
    public function registrar()
    {
        $clave = $this->clave;
        //por defecto las cuentas están desactivadas
        //Revisar esto en la base de datos
        $this->activo = '0'; 
        $this->begin(); //iniciamos una transaccion
        $this->roles_id = self::ROL_DEFECTO;
        if ($this->save() ) {
            $hash = $this->hash();
            $correo = Load::model('admin/correos');
            if ($correo->enviarRegistro($this, $clave, $hash)) {
                $this->commit();
                return TRUE;
            } else {
                Flash::error($correo->getError());
                $this->rollback();
                return FALSE;
            }
        } else {
            $this->rollback();
            return FALSE;
        }
    }

    /**
     * Si el estado es negativo es que ha sido bloqueado y no se puede 
     * activar vía correo
     *
     * @param int $id_usuario
     * @param string $hash
     * @return boolean
     */
    public function activarCuenta($id_usuario, $hash)
    {
        if ($this->find_first((int) $id_usuario)) { //verificamos la existencia del user
            if ( $this->hash() === $hash && $this->activo > -1 ){
                $this->activo = 1;
                if ($this->save()) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    /**
     * Devuelve el hash de identificacion de usuario registrado
     * @return String
     */
    function hash(){
        return sha1($this->login . $this->id . $this->clave);
    }
    /**
     * Desactiva a un usuario
     */
    function desactivar() {
        $this->active = '0';
        return $this->save();
    }
    
     /**
     * Activa a un usuario
     */
    function activar() {
        $this->active = '1';
        return $this->save();
    }
    
    
    function auth($arg){
		$clave = $arg['clave'];
		$user = $arg['login'];
		return $this->find_first("password = '$clave' AND login = '$user' AND enable='1'");
	} 

}
