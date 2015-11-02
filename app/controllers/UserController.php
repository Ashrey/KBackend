<?php
namespace KBackend\Controller;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Libs\Paginator;
use \KBackend\Libs\Helper\FormBuilder;
use \KBackend\Libs\Helper\Grid;
use \KBackend\Model\User;
use \KBackend\Model\Action;
class UserController extends \KBackend\Libs\ScaffoldController {

	protected $_model = '\KBackend\Model\User';

	protected function createPaginator() {
		return new Paginator($this->_model, array(
			'join' => 'JOIN Role r ON r.id = role_id',
			'fields' => 'User.id, User.login, User.email, r.role rol',
		));
	}

	protected function getFormEdit($obj) {
		return new FormBuilder($obj, 'user_edit.php');
	}

	protected function getForm($obj) {
		return new FormBuilder($obj, 'user.php');
	}

	public function index() {
		parent::index();
		$this->result->setAction('user/buttons');
	}

	protected function getRecord($id) {
		$_model = $this->_model;
		return $_model::view((int) $id);
	}

	public function action($id) {
		$id = (int) $id;
		$this->user = User::get($id);
		$this->result = Action::byUser($id);
	}
}
