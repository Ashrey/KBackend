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
	protected $source = 'role';
	
    protected function initialize() {
        //relaciones
        $this->has_and_belongs_to_many('recursos', 'model: admin/recursos', 'fk: recursos_id', 'through: admin/roles_recursos', 'key: roles_id');
        $this->has_and_belongs_to_many('usuarios', 'model: admin/usuarios', 'fk: usuarios_id', 'through: admin/roles_usuarios', 'key: roles_id');
        
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
        $join = "INNER JOIN role_resource as rr ON rr.role_id = role.id ";
        $join .= "INNER JOIN resource as r ON rr.resource_id = r.id ";
        $where = "role.id = '$this->id'";
        return $this->find($where, "columns: $columnas" , "join: $join");
    }

}

