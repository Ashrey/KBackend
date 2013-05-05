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
class RolesRecursos extends ARecord {

//    public $debug = true;

    protected function initialize() {
        $this->belongs_to('roles');
    }

    /**
     * Guarda un nuevo registro.
     * 
     * @param  int $rol     id del rol
     * @param  int  $recurso id del recuro
     * @return boolean         
     */
    public function guardar($rol, $recurso) {
        if ($this->existe($rol, $recurso))
            return TRUE;
        
        return $this->create(array(
            'roles_id' => $rol,
            'recursos_id' => $recurso
        ));
    }
    
    /**
     * Elimina un privilegio
     * 
     * @param  int $rol     id del rol
     * @param  int  $recurso id del recuro
     * @return boolean        
     */
    public function eliminar($rol, $recurso) {
        return $this->delete_all("roles_id = '$rol' AND recursos_id = '$recurso'");
    }

    /**
     * Modifica los privilegios en una pagina dada.
     *  @param int $role id del rol a conceder privilegios 
     * @param  array $priv privilegios a conceder
     * @param  string $all todos los privilegios de la página 
     * @return boolean  
     */
    public function editarPrivilegios($rol, $priv, $all) {
        $this->begin();
        foreach ($all as $e) {
            /*El privilegio ha sido asignado*/
            if (in_array($e, $priv)) {
                if(!$this->guardar($rol, $e)){
                    $this->rollback();
                    return false;
                }
            }else if(!$this->eliminar($rol, $e)){
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return TRUE;
    }

    /**
     * Verifica la existencia de un privilegio.
     * 
     * @param  int $rol     id del rol
     * @param  int $recurso id del recurso
     * @return boolean
     */
    public function existe($rol, $recurso) {
        return $this->exists("roles_id = '$rol' AND recursos_id = '$recurso'");
    }
    
    /**
     * Devuelve un array con los privilegios asignados a un rol
     * @param int $id id del rol
     */
    public function privilegios($id){
        $c = array();
        $a = $this->find_all_by_roles_id((int)$id);
        foreach($a as $b)
            $c[] = $b->recursos_id;
        return $c;
    }

}

