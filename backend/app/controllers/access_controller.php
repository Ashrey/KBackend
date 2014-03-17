<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Model\Role;
use \KBackend\Model\RoleResource;
class AccessController extends  \KBackend\Libs\AuthController{

    public function index(){
        $this->roles = Role::all();
    }

    public function allow($rol) {
        try {
            $this->rol =  Role::get($rol);
            $_model = new \KBackend\Model\Resource();
            /*captura los filtros*/
            $filter = \KBackend\Libs\FilterSQL::get();
            $filter->per_page =  Config::get('backend.app.per_page');
            $this->result  = new \KBackend\Libs\Paginator($_model,  $filter->getArray());
            $this->url = "access/assign/$rol";
             /*llama a la funcion de resultados*/
            $this->privilegios = RoleResource::access($rol);
        } catch (\Exception $e) {
            View::excepcion($e);
        }
    }

    public function assign($rol) {
        try {
            if (Input::hasPost('todo')) {
                $priv = Input::post('priv');
                $todo  = Input::post('todo');
                if (RoleResource::edit($rol, (array)$priv , $todo)) {
                    Flash::valid('Los privilegios fueron editados');
                } else {
                    Flash::warning('No se pudo editar los privilegios');
                }
            }
        } catch (\Exception $e) {
            View::excepcion($e);
        }
        return Redirect::toAction("allow/$rol");
    }
}

