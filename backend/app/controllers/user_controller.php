<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Libs\Paginator;
class UserController extends \KBackend\Libs\ScaffoldController{
	
	protected $_model = '\KBackend\Model\User';

    protected function createPaginator(){
        return new Paginator($this->_model,  array(
            'join'   => 'JOIN Role r ON r.id = role_id',
            'fields' => 'User.id, User.login, User.email, r.role rol',
        ));
    }
}
