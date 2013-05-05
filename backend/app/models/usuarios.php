<?php

/**
 * Backend - KumbiaPHP Backend
 * PHP version 5
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Modelos
 * @license http://www.gnu.org/licenses/agpl.txt GNU AFFERO GENERAL PUBLIC LICENSE version 3.
 * @author Manuel José Aguirre Garcia <programador.manuel@gmail.com>
 */
 Load::model('admin/roles');
class Usuarios extends ActiveRecord
{
    const ROL_DEFECTO = 1;

    protected function initialize()
    {
        $min_clave = Config::get('backend.app.minimo_clave');
        $this->belongs_to('roles');
        $this->has_many('admin/auditorias');
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
            $this->clave = MyAuth::hash($this->clave);
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
        //Flash::error($cond);
        return $this->paginate("page: $pagina",
                'join: JOIN roles r ON r.id = roles_id',
                'columns: usuarios.*, r.rol rol',
                'per_page: '.Config::get('backend.app.per_page'),
                "conditions: $cond"
               );
    }

    public function numAcciones($pagina = 1)
    {
        $cols = "usuarios.*,COUNT(auditorias.id) as num_acciones";
        //$join = "INNER JOIN roles ON roles.id = usuarios.roles_id ";
        $join = "LEFT JOIN auditorias ON usuarios.id = auditorias.usuarios_id";
        $group = 'usuarios.' . join(',usuarios.', $this->fields);
        $sql = "SELECT $cols FROM $this->source $join GROUP BY $group";
        return $this->paginate_by_sql($sql, "page: $pagina", 'per_page: '.Config::get('backend.app.per_page'));
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
        $this->activo = '0';
        return $this->save();
    }
    
     /**
     * Activa a un usuario
     */
    function activar() {
        $this->activo = '1';
        return $this->save();
    }

}
