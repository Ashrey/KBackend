<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */
use \Flash;
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
            $this->result = new Grid(new Paginator($model, $param));
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
        try {
            $obj = new $this->_model();
            $this->form = new FormBuilder($obj);
            if(\Input::hasPost($this->model)){
                if ($this->form->isValid() && $obj->save()) {
                    \Flash::valid('Agregegado correctamente');
                    if (!\Input::isAjax()) {
                        \Redirect::toAction('');
                    }    
                } else {
                   \Flash::error('Falló Operación');
                    //se hacen persistente los datos en el formulario
                    $this->{$this->model} = $obj; 
                }
            }
            
            
        } catch (\Exception $e) {
            Flash::error($e);
        }
    }

    /**
     * Edit record 
     */
    public function edit($id) {
        $model = $this->_model;
        $obj = $model::get($id);
        /**
         * Date set in request
         */
        if (\Input::hasPost($this->model)) {
            $data = \Input::post($this->model); 
            if (is_object($obj)) {
                if (!$obj->save($data)) {
                    //se hacen persistente los datos en el formulario
                    $this->{$this->_model} = $data;
                } else {
                    \Flash::valid('Edición hecha');
                }
            } else {
                \Flash::error('No existe este registro');
            }
        }
        $this->form = new FormBuilder($model::get((int) $id));
        $this->{$this->model} = $obj;
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
}
