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
     * Establece si se usan o no filtros
     * @var boolean 
     */
    public $use_filter = false;

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
     * Mostrar la barra de acciones
     * @var boolean 
     */
    public $show_bar = true;

    /**
     * Añade la funcionalidad de iniciación
     * @param type $module
     * @param type $controller
     * @param type $action
     * @param type $parameters
     */
    function __construct($module, $controller, $action, $parameters) {
        parent::__construct($module, $controller, $action, $parameters);
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
    
    public function index(){
        try {
            $model = $this->_model;
            $param = method_exists($model, 'index') ? $model::index():array();
            $this->result = $this->createGrid($model, $param);
        }catch(\RangeException $e){
            /*nothing*/
        }catch (\Exception $e) {
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
        $this->handleForm($obj);
    }

    /**
     * Borra un Registro
     */
    public function delete($id) {
        $model = $this->_model;
        try {
            $this->view($id);
            if(\Input::is('POST')){
    			if ($model::delete($id)) {
    				\Flash::valid('Borrado correctamente');
                     \Redirect::to();
    			} else {
    				\Flash::error('Falló Operación');
    			}
            }
		} catch (\Exception $e) {
            Flash::error($e);
        }
    }

    /**
     * Ver un Registro
     */
    public function view($id) {
        $_model = $this->_model;
        $this->result = method_exists($_model, 'view') ?
                call_user_func_array(array($_model, 'view'), array((int)$id)) :
                $_model::get((int) $id);
    }

    /**
     * Return a form for model
     * @param  object $obj model
     * @return FormBuilder  form
     */
    protected function getForm($obj){
        return new FormBuilder($obj);
    }

    /**
     * Create a grid
     * @param object $model 
     * @param mixed Array $param 
     * @param mixed Array  $values 
     * @return Grid
     */
    protected function createGrid($model, Array $param=array(), Array  $values=array()){
        return new Grid(new Paginator($model, $param, $values));
    }
    

    /**
     * Handle post request
     * @param object $obj 
     * @return bool
     */
    protected function handleForm($obj){
        $this->form = $this->getForm($obj);
        $this->{$this->model} = $obj; 
        if(Input::is('POST') && $this->form->isValid()){
            if($obj->save()) {
                Flash::valid('Operación exitosa');
                return TRUE; 
            } else {
               \Flash::error('Falló la operación');
            }
        }
        return FALSE;
    }

    /**
     * Set a flash message
     * @param  bool $cond     evaluted condition
     * @param  string $success sucess message
     * @param  string $fail    error message
     */
    protected function flash($cond, $success, $fail){
        if ($cond) {
            Flash::valid($success);
        } else {
            Flash::warning($fail);
        }
    }
}
