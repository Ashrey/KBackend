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
    
    /**
     * Add new access for a resource
     * @param  int $rol     id of role
     * @return boolean         
     */
    public static function add($rol, $resource) {
        if (static::isCreated($rol,$resource))
            return TRUE;
        $new = new self();
        return $new->create(array(
            'role_id' => $rol,
            'resource_id' => $resource
        ));
    }
    
    /**
     * Deny access for a resource
     * 
     * @param  int $rol     id del rol
     * @return boolean        
     */
    public static function  remove($rol, $resource) {
        /*If not created not deleted*/
        if (!static::isCreated($rol,$resource))
            return TRUE;
        return static::deleteAll('role_id = :rol AND resource_id = :resource',
            array(':rol'=> $rol, ':resource' => $resource));
    }

    /**
     * Edit access for user id
     * @param int $rol id 
     * @param  array $priv allows
     * @param  array $all all allow for page
     * @return boolean  
     */
    public static function edit($rol, $priv, $all) {
        static::begin();
        foreach ($all as $e) {
            /*El privilegio ha sido asignado*/
            if (in_array($e, $priv)){
                if(!static::add($rol, $e)){
                    static::rollback();
                    return false;
                }
            }elseif(!static::remove($rol, $e)){
                static::rollback();
                return false;
            }
        }
        static::commit();
        return TRUE;
    }

    /**
     * Verifica la existencia de un privilegio.
     * 
     * @param  int $rol     id del rol
     * @return boolean
     */
    public static function isCreated($rol, $resource) {
        return static::count('role_id = :rol AND resource_id = :resource',
            array(':rol'=>$rol, ':resource' => $resource));
    }
    
    /**
     * Return allow access for role $id
     * @param int $id id of  rol
     */
    public static function access($id){
        $c = array();
        $a = static::allBy('resource_id', $id);
        foreach($a as $b)
            $c[] = $b->role_id;
        return $c;
    }

    public static function getTable()  {
        return 'kb_role_resource';
    }

}

