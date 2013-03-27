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
 * @package Controller
 * @license http://www.gnu.org/licenses/agpl.txt GNU AFFERO GENERAL PUBLIC LICENSE version 3.
 * @author Manuel José Aguirre Garcia <programador.manuel@gmail.com>
 */
class PrivilegiosController extends AdminController {

    public function index(){
        $this->roles = Load::model('admin/roles')->find();
    }
    

    public function asignar($rol, $page=1) {
        try {
                $this->rol = Load::model('admin/roles')->find((int)$rol);
                $this->results = Load::model('admin/recursos')->paginate("page: $page", 'order: recurso');
                $this->privilegios = Load::model('admin/roles_recursos')->privilegios((int)$rol);
            
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

