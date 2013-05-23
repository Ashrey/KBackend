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
                $this->results = \KBackend\Model\Resource::_paginate("page: $page",
                        'per_page: '.Config::get('backend.app.per_page'));
                $this->privilegios = \KBackend\Model\RoleResource::_access((int)$rol);
            
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function asignar_privilegios() {
        try {
            if (Input::hasPost('priv') && Input::hasPost('todo') && Input::hasPost('rol') ) {
                $obj = Load::model('admin/roles_recursos');
                $priv = Input::post('priv');
                $todo  = Input::post('todo');
                $rol = Input::post('rol');
                if ($obj->editarPrivilegios($rol, $priv ,$todo)) {
                    Flash::valid('Los privilegios fueron editados');
                } else {
                    Flash::warning('No se pudo editar los privilegios');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::toAction("asignar/$rol");
    }

}

