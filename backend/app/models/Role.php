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
	
    public static function _rules() {
        return array(
            'role' => array(
                'required',
            )
        );
     }

    /**
     * Devuelve los recursos a los que un rol tiene acceso 
     * y además están activos
     * 
     * @return array 
     */
    public static function  getResource($id){
        $join = "INNER JOIN _role_resource as rr ON rr.role_id = _role.id ";
        $join .= "INNER JOIN _resource as r ON rr.resource_id = r.id ";
        $param = array(
            'join'   => $join,
            'fields' => 'url',
            'where'  => "_role.id = ? AND enable = 1"
        );
        return self::all($param, array($id));
    }

    public function __toString(){
        return $this->role;
    }

}

