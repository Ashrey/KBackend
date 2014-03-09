<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */
abstract class ScaffoldController extends \KBackend\Libs\AuthController {

    /**
     * Decide el scaffold a usar
     * @var String 
     */
    public $scaffold = 'backend';

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
     * Nombre del método a la hora de indexar los registros
     * @var String 
     */
    protected $_index = 'index';

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
    
    public function index() {
        try {
            $_model = new $this->_model();
            /*captura los filtros*/
            $filter = FilterSQL::get();
            $filter->per_page =  \Config::get('backend.app.per_page');
            $paginator = new Paginator($_model,  $filter->getArray());
            /*llama a la funcion de resultados*/
            $this->result = new \Grid($paginator);
        } catch (KumbiaException $e) {
            \View::excepcion($e);
        }
    }

    /**
     * Crea un Registro
     */
    public function create() {
        try {
            /**
             * Date set in request
             */
            if (\Input::hasPost($this->model)) {
                $obj = new $this->_model();
                if (!$obj->save(\Input::post($this->model))) {
                    \Flash::error('Falló Operación');
                    //se hacen persistente los datos en el formulario
                    $this->{$this->model} = $obj;
                } else {
                    \Flash::valid('Agregegado correctamente');
                    if (!\Input::isAjax()) {
                        \Redirect::toAction('');
                    }
                }
            }
            // Solo es necesario para el autoForm
            $this->form = new \FormBuilder(new $this->_model());
        } catch (KumbiaException $e) {
            Flash::error($e);
        }
    }

    /**
     * Edit record 
     */
    public function edit($id) {
        /**
         * Date set in request
         */
        if (\Input::hasPost($this->model)) {
            $data = \Input::post($this->model);
            $m = new $this->_model();
            $obj = $m->find_first($id);
            if (is_object($obj)) {
                if (!$obj->update($data)) {
                    //se hacen persistente los datos en el formulario
                    $this->{$this->_model} = $data;
                } else {
                    \Flash::valid('Edición hecha');
                    if (!\Input::isAjax()) {
                        \Redirect::toAction('');
                    }
                }
            } else {
                \Flash::error('No existe este registro');
            }
        }
        //Aplicando la autocarga de objeto, para comenzar la edición
        $obj = new $this->_model();
        $this->form = new \FormBuilder($obj->find((int) $id));
    }

    /**
     * Borra un Registro
     */
    public function delete($id) {
        try {
			if (call_user_func(array($this->_model, '_delete'), (int) $id)) {
				\Flash::valid('Borrado correctamente');
			} else {
				\Flash::error('Falló Operación');
			}
			//enrutando al index
			\Redirect::to();
		} catch (Exception $e) {
            Flash::error($e);
        }
    }

    /**
     * Ver un Registro
     */
    public function view($id) {
        $_model = new $this->_model();
        $this->result = method_exists($_model, 'view') ?
                call_user_func_array(array($_model, $this->_index), array($id)) :
                $_model->find_first((int) $id);
        /* asigna columnas a mostrar */
        $this->cols = array_keys(get_object_vars($this->result));
    }
}
