<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \Flash, \Input;
use \KBackend\Libs\Helper\Grid;
use \KBackend\Libs\Helper\FormBuilder;
abstract class ScaffoldController extends \KBackend\Libs\AuthController {

	/**
	 * Name of scaffold
	 * @var String
	 */
	public $scaffold = 'backend';

	/**
	 * Base template for scaffold
	 * @var String
	 */
	public $basetpl = 'default.phtml';

	/**
	 * Nombre del _modelo a mostrar
	 * @var String
	 */
	protected $_model;

	/**
	 * Establece el titulo
	 * @var String
	 */
	public $title = '';

	/**
	 * Nombre del método a la hora de mostrar un registro específico
	 * @var String
	 */
	protected $_view = 'view';

	/**
	 * Añade la funcionalidad de iniciación
	 * @param type $module
	 */
	function __construct($arg) {
		parent::__construct($arg);
		/**
		 * Set the name of model
		 */
		$last = explode('\\', $this->_model);
		$this->model = strtolower(end($last));
	}

	protected function after_filter() {
		if (\Input::isAjax()) {
			\View::select('ajax', null);
		}
	}

	protected function before_filter() {
		/*Set the model name if not title*/
		$this->title = empty($this->title) ? ucwords($this->model) : $this->title;
	}

	public function index() {
		try {
			$this->result = $this->createGrid();
		} catch (\Exception $e) {
			\View::excepcion($e);
		}
	}

	/**
	 * Crea un Registro
	 */
	public function create() {
		$obj = new $this->_model();
		if ($this->handleForm($obj) && !Input::isAjax()) {
			\Redirect::toAction('');
		}
	}

	/**
	 * Edit record
	 */
	public function edit($id) {
		$model = $this->_model;
		$obj = $model::get($id);
		if (!is_object($obj)) {
			throw new \Exception('Objecto dont exist', 1);
		}
		$this->handleForm($obj, TRUE);
	}

	/**
	 * Borra un Registro
	 */
	public function delete($id) {
		$model = $this->_model;
		try {
			$this->view($id);
			if (\Input::is('POST')) {
				if ($model::delete($id)) {
					\Flash::valid('Borrado correctamente');
					\Redirect::to();
				} else {
					\Flash::error('Falló Operación');
				}
			}
		} catch (\Exception $e) {
			Flash::error($e->getMessage());
		}
	}

	/**
	 * show a record
	 */
	public function view($id) {
		$this->result = $this->getRecord($id);
	}

	/**
	 * Get a object for view
	 * @param  int $id primary key
	 * @return object
	 */
	protected function getRecord($id) {
		$_model = $this->_model;
		return $_model::get((int) $id);
	}

	/**
	 * Return a form for model
	 * @param  object $obj model
	 * @return FormBuilder  form
	 */
	protected function getForm($obj) {
		return new FormBuilder($obj);
	}

	/**
	 * return a form for edit action
	 * @param object $obj
	 * @return FormBuilder
	 */
	protected function getFormEdit($obj) {
		return $this->getForm($obj);
	}

	/**
	 * Create a Grid
	 * @return Grid
	 */
	protected function createGrid() {
		return new Grid($this->createPaginator());
	}

	/**
	 * Create a Paginator
	 * @return Paginator
	 */
	protected function createPaginator() {
		return new Paginator($this->_model);
	}

	/**
	 * Handle post request
	 * @param object $obj
	 * @param bool $edit use edit form?
	 * @return bool
	 */
	protected function handleForm($obj, $edit = FALSE) {
		$this->form = $edit ? $this->getFormEdit($obj) : $this->getForm($obj);
		$this->{$this->model} = $obj;

		if (Input::is('POST') && $this->form->isValid()) {
			$return = $obj->save();
			$this->flash($return, 'Operación exitosa', 'Falló la operación');
			return $return;
		}
		return FALSE;
	}

	/**
	 * Set a flash message
	 * @param  bool $cond     evaluted condition
	 * @param  string $success sucess message
	 * @param  string $fail    error message
	 */
	protected function flash($cond, $success, $fail) {
		if ($cond) {
			Flash::valid($success);
		} else {
			Flash::error($fail);
		}
	}
}
