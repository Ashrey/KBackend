<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Action extends \KBackend\Libs\ARecord {
	protected $source = 'action';
    /**
     * DANGER!!!!!
     * protege de un ciclo infinito 
     * DON'T REMOVE
     */
    protected $logger = false;

    protected function initialize() {
        //relaciones
        $this->belongs_to('admin/usuarios');
    }

    protected function before_save() {
        $this->ip = ip2long($_SERVER['REMOTE_ADDR']);
    }

    function ip() {
        return long2ip($this->ip);
    }

	/**
	 * Return pagination action by user id
	 */
    public function byUser($id, $pag = 1) {
        $where = "user_id = '$id'";
        return $this->paginate("page: $pag", "conditions: $where", "order: id desc", 'per_page: ' . \Config::get('backend.app.per_page')
        );
    }

}

