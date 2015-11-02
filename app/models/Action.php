<?php
namespace KBackend\Model;
use \Config;
use KBackend\Libs\Paginator;
use KBackend\Libs\Helper\Grid;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Action extends \KBackend\Libs\ARecord {

    protected function _beforeSave() {
        $this->ip = ip2long($_SERVER['REMOTE_ADDR']);
    }

    function ip() {
        return long2ip($this->ip);
    }

	/**
	 * Return pagination action by user id
	 * @param integer $id
	 */
    public static function byUser($id) { 
        $param = array(
                'where' => 'user_id = :id',
                'fields' => 'id, date_at, ip, action',
                'order' =>  'date_at DESC'
        );
        $paginator = new Paginator('\KBackend\Model\Action', $param , array(':id' => $id));
        return  new Grid($paginator);
    }
}

