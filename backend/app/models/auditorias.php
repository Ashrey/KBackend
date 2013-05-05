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
class Auditorias extends ARecord {

    //protege de un ciclo infinito NO ELIMINAR
    public $logger = false;

    protected function initialize() {
        //relaciones
        $this->belongs_to('admin/usuarios');
    }

    protected function before_save() {
        $this->ip = ip2long($_SERVER['REMOTE_ADDR']);
    }

    function ip() {
        return long2ip($this->ip);
    }

    /**
     * Obtiene las acciones realizadas por un usuario especifico
     * 
     * @param  Usuarios $usuario usuario que se auditará
     * @param  Filtro   $filtro  filtro de auditorias
     * @param  integer  $pagina  pagina a mostrar
     * @return array        registros en la bd
     */
    public function porUsuario($id, $pagina = 1) {
        $where = "usuarios_id = '$id'";
        return $this->paginate("page: $pagina", "conditions: $where", "order: id desc", 'per_page: ' . Config::get('backend.app.per_page')
        );
    }

}

