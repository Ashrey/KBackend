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
            $this->result  = new \KBackend\Libs\Paginator($_model);
            $this->url = "access/assign/$rol";
             /*llama a la funcion de resultados*/
            $this->privilegios = RoleResource::access($rol);
        } catch (\Exception $e) {
            View::excepcion($e);
        }
    }


}

