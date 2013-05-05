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
Load::models('admin/roles');

class RolesController extends AdminController {

    /**
     * Luego de ejecutar las acciones, se verifica si la petición es ajax
     * para no mostrar ni vista ni template.
     */
    protected function after_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

    public function index($pag = 1) {
        try {
            $roles = new Roles();
            $this->roles = $roles->paginate("page: $pag", 'per_page: ' . Config::get('backend.app.per_page'));
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        $this->titulo = 'Crear Rol (Perfil)';
        try {
            if (Input::hasPost('rol')) {
                $rol = new Roles(Input::post('rol'));
                if ($rol->save()) {
                    Flash::valid('El Rol Ha Sido Agregado Exitosamente...!!!');
                    if (!Input::isAjax()) {
                        return Router::redirect();
                    }
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function editar($id) {
        $this->titulo = 'Editar Rol (Perfil)';
        try {

            $id = (int) $id;

            View::select('crear');

            $rol = new Roles();

            $this->rol = $rol->find_first($id);

            if ($this->rol) {//verificamos la existencia del rol
                if (Input::hasPost('rol')) {
                    if ($rol->update(Input::post('rol'))) {
                        Flash::valid('El Rol Ha Sido Actualizado Exitosamente...!!!');
                        if (!Input::isAjax()) {
                            return Router::redirect();
                        }
                    } else {
                        Flash::warning('No se Pudieron Guardar los Datos...!!!');
                    }
                }
            } else {
                Flash::warning("No existe ningun rol con id '{$id}'");
                if (!Input::isAjax()) {
                    return Router::redirect();
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function eliminar($id) {
        try {
            $rol = new Roles();
            if (!$rol->find_first((int) $id)) { //si no existe
                Flash::warning("No existe ningun rol con id '{$id}'");
            } else if ($rol->delete()) {
                Flash::valid("El rol <b>{$rol->rol}</b> fue eliminado");
            } else {
                Flash::warning("No se Pudo Eliminar el Rol <b>{$rol->rol}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

}
