<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class UserController extends \KBackend\Libs\ScaffoldController{
	
	protected $_model = '\KBackend\Model\User';
	
	protected $_index = 'paginar';
	
	protected $_title = 'User';

    /**
     * Cambio de los datos personales de usuario.
     * 
     */
    public function perfil() {
        try {
            $usr = new Usuarios();
            $this->usuario1 = $usr->find_first(Auth::get('id'));
            if (Input::hasPost('usuario1')) {
                if ($usr->update(Input::post('usuario1'))) {
                    Flash::valid('Datos Actualizados Correctamente');
                    $this->usuario1 = $usr;
                }
            } else if (Input::hasPost('usuario2')) {
                if ($usr->cambiarClave(Input::post('usuario2'))) {
                    Flash::valid('Clave Actualizada Correctamente');
                    $this->usuario1 = $usr;
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
    
       /**
     * Cambio de los datos personales de usuario.
     * 
     */
    public function ver($id) {
        try {
            $usr = new Usuarios();
            $this->result = $usr->find((int)$id);

        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }


    /**
     * Edita los datos de un usuario desde el backend.
     * @param  int $id id del usuario a editar
     */
    public function editar($id) {
        try {

           $usr = new Usuarios(Input::post('usuario'));
            $this->usuario = $usr->find_first($id);
            if ($this->usuario) {// verificamos la existencia del usuario
                if (Input::hasPost('usuario')) {
                    if ($usr->save(Input::post('usuario')) ){
                        Flash::valid('El Usuario Ha Sido Actualizado Exitosamente...!!!');
                        if (!Input::isAjax()) {
                            return Router::redirect();
                        }
                    } else {
                        Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    }
                }
            } else {
                Flash::warning('El usuario no existe');
                if (!Input::isAjax()) {
                    return Router::redirect();
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    /**
     * Activa un usuario desde el backend
     * @param  int $id id del usuario a activar
     */
    public function activar($id) {
        try {
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else if ($usuario->activar()) {
                Flash::valid("La Cuenta {$usuario->login} ({$usuario->nombres}) fue activada");
            } else {
                Flash::warning('No se Pudo Activar la cuenta');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }

    /**
     * Desactiva un usuario desde el backend
     * @param  int $id id del usuario a desactivar
     */
    public function desactivar($id) {
        try {
            $id = (int) $id;
            $usuario = new Usuarios();
            if (!$usuario->find_first($id)) { //si no existe el usuario
                Flash::warning("No existe ningun usuario con id '{$id}'");
            } else if ($usuario->desactivar()) {
                Flash::valid("La Cuenta {$usuario->login} ({$usuario->nombres}) fue desactivada");
            } else {
                Flash::warning('No se pudo desactivar la cuentaS');
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction('');
    }
    
    public function logout(){
		\KBackend\Libs\MyAuth::finish();
		Router::redirect();
		die();
	}

}
