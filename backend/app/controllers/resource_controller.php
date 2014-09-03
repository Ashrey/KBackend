<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Model\Resource;
use \KBackend\Model\RoleResource;
class ResourceController extends \KBackend\Libs\ScaffoldController{
	
	protected $_model = '\KBackend\Model\Resource';
	
	protected $_title = 'Resource';

    public function index(){
        parent::index();
        $this->result->setAction('resource/buttons');
    }

    public function access($id){
        try {
            if (Input::hasPost('access')) {
                $access = Input::post('access');
                $this->flash(RoleResource::edit($id, $access),
                    'Los privilegios fueron editados',
                    'No se pudo editar los privilegios');
            }
        } catch (\Exception $e) {
            View::excepcion($e);
        }
        $this->resource    = Resource::get($id);
        $this->privilegios = RoleResource::access($id);
        $this->result      = \KBackend\Model\Role::all();
    }
}
