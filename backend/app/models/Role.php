<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Role extends \KBackend\Libs\ARecord {
	protected $source = '_role';
	
    protected function initialize() {
        //validaciones
        $this->validates_presence_of('rol','message: Debe escribir el <b>Nombre del Rol</b>');
        $this->validates_uniqueness_of('rol','message: Este Rol <b>ya existe</b> en el sistema');
    }

    /**
     * Devuelve los recursos a los que un rol tiene acceso 
     * y además están activos
     * 
     * @return array 
     */
    public function getRecursos(){
        $columnas = "r.*";
        $join = "INNER JOIN _role_resource as rr ON rr.role_id = _role.id ";
        $join .= "INNER JOIN _resource as r ON rr.resource_id = r.id ";
        $where = "_role.id = '$this->id'";
        return $this->find($where, "columns: $columnas" , "join: $join");
    }

}

