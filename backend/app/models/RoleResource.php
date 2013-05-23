<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class RoleResource extends \KBackend\Libs\ARecord {
	protected $source = 'role_resource';
	
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
     * Return allow access for role $id
     * @param int $id id of  rol
     */
    public function access($id){
        $c = array();
        $a = $this->find_all_by_role_id((int)$id);
        foreach($a as $b)
            $c[] = $b->resource_id;
        return $c;
    }

}

