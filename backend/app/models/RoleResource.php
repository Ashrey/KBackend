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
     * Add new record
     * @param  int $rol     id of role
     * @param  int  $recurso id of resource
     * @return boolean         
     */
    public function add($rol, $resourse) {
        if ($this->isCreated($rol,$resourse ))
            return TRUE;
        return $this->create(array(
            'role_id' => $rol,
            'resource_id' => $resourse
        ));
    }
    
    /**
     * Elimina un privilegio
     * 
     * @param  int $rol     id del rol
     * @param  int  $recurso id del recuro
     * @return boolean        
     */
    public function deny($rol, $resource) {
        return $this->delete_all("role_id = '$rol' AND resource_id = '$resource'");
    }

    /**
     * Edit access for user id
     *  @param int $role id 
     * @param  array $priv allows
     * @param  string $all all alow for page
     * @return boolean  
     */
    public function edit($rol, $priv, $all) {
        $this->begin();
        foreach ($all as $e) {
            /*El privilegio ha sido asignado*/
            if (in_array($e, $priv)) {
                if(!$this->add($rol, $e)){
                    $this->rollback();
                    return false;
                }
            }else if(!$this->deny($rol, $e)){
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
    public function isCreated($rol, $resource) {
        return $this->exists("role_id = '$rol' AND resource_id = '$resource'");
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

