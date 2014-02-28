<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class AccessController extends  \KBackend\Libs\AuthController{

    public function index(){
        $this->roles = \KBackend\Model\Role::_find();
    }

    public function allow($rol, $page=1) {

        try {
                $this->rol =  \KBackend\Model\Role::_find((int)$rol);
                $_model = new \KBackend\Model\Resource();
                 /*captura los filtros*/
                 $filter = \KBackend\Libs\FilterSQL::get();
                 $filter->per_page =  \Config::get('backend.app.per_page');
                $this->result  = new \KBackend\Libs\Paginator($_model,  $filter->getArray());
                 /*llama a la funcion de resultados*/
                $this->privilegios = \KBackend\Model\RoleResource::_access((int)$rol);
            
        } catch (KumbiaException $e) {
            die;
            View::excepcion($e);
        }
    }

    public function assign() {
        try {
            if (Input::hasPost('priv') && Input::hasPost('todo') && Input::hasPost('rol') ) {
                $obj = new \KBackend\Model\RoleResource();
                $priv = Input::post('priv');
                $todo  = Input::post('todo');
                $rol = Input::post('rol');
                if ($obj->edit($rol, $priv ,$todo)) {
                    Flash::valid('Los privilegios fueron editados');
                } else {
                    Flash::warning('No se pudo editar los privilegios');
                }
                return Router::toAction("allow/$rol");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction();
    }

}

