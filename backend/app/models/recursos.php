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
class Recursos extends ARecord {

//    public $debug = true;

    protected function initialize() {
        //validaciones
        $this->validates_presence_of('controlador', 'message: Debe escribir un <b>Controlador</b>');
        $this->validates_presence_of('descripcion', 'message: Debe escribir una <b>Descripción</b>');
        $this->validates_uniqueness_of('recurso', 'message: Este Recurso <b>ya existe</b> en el sistema');
    }

    /**
     * Obtiene los recursos a los que un rol tiene acceso.
     * 
     * @param  int $id_rol 
     * @return array         
     */
    public function obtener_recursos_por_rol($id_rol) {
        $cols = 'recursos.recurso';
        $joins = 'INNER JOIN roles_recursos as r ON r.recursos_id = recursos.id';
        $where = "r.roles_id = '$id_rol'";
        return $this->find("columns: $cols", "join: $joins", "$where");
    }

    /**
     * Obtiene los recursos que no se han agregado al al bd.
     * 
     * @param  integer $pagina 
     * @return array          
     */
    public function obtener_recursos_nuevos($pagina = 1) {
        $recursos = LectorRecursos::obtenerRecursos();
        foreach ($recursos as $index => $re) {
            if ($this->exists('recurso = \'' . $re['recurso'] . '\'')) {
                unset($recursos[$index]);
            }
        }
        $recursos = LectorRecursos::paginar($recursos, $pagina, Config::get('backend.app.per_page'));
        $this->recursos_nuevos = $recursos->items;
        array_unshift($this->recursos_nuevos, null);
        return $recursos;
    }

    /**
     * Guarda los recursos que aun no estan en bd y fueron seleccionados
     * por el usuario.
     * 
     * @return boolean 
     */
    public function guardar_nuevos() {
        $chequeados = Input::post('check');
        $descripciones = Input::post('descripcion');
        $activos = Input::post('activo');
        $this->begin();
        if (!$chequeados) {
            return FALSE;
        }
        foreach ($chequeados as $valor) {
            if (empty($descripciones[$valor])) {
                Flash::error('Hay recursos seleccionados que no tienen descripción');
                $this->rollback();
                return FALSE;
            }
            $data = $this->recursos_nuevos[$valor];
            $data['descripcion'] = $descripciones[$valor];
            $data['activo'] = $activos[$valor];
            if (!$this->create($data)) {
                $this->rollback();
                return FALSE;
            }
        }
        $this->commit();
        return TRUE;
    }

    /**
     * Obtiene las acciones existentes por cada controlador.
     * 
     * @return array 
     */
    public function accionesPorControlador() {
        $res = $this->find("modulo = '$this->modulo' AND controlador = '$this->controlador' AND accion != ''", 'columns: id,accion');
        return $res;
    }

    /**
     * Desactiva a un recurso
     */
    function desactivar() {
        $this->activo = '0';
        return $this->save();
    }

    /**
     * Activa a un recurso
     */
    function activar() {
        $this->activo = '1';
        return $this->save();
    }

}
