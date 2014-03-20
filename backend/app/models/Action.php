<?php
namespace KBackend\Model;
use \Config;
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
	 */
    public static function byUser($id, $pag = 1) {
        $arg = array(
            'where' => 'user_id = :id',
            'order' => 'id desc',
        );
        $param =  array(':id' => $id);
        return  self::paginate( $arg, $pag, \Config::get('backend.app.per_page'), $param);
    }



}

