<?php

class ScaffoldController extends AdminController {

    /**
     * Decide el scaffold a usar
     * @var String 
     */
    public $scaffold = 'bootstrap';

    /**
     * Nombre del modelo a mostrar
     * @var String
     */
    public $model;
    
    public $paginator = 'digg';

    /**
     * Array de columnas a  mostrar
     * @var Array
     */
    protected $show_cols = array();

    /**
     * Establece si se usan o no filtros
     * @var boolean 
     */
    protected $_use_filter = false;

    /**
     * Establece el titulo
     * @var String
     */
    protected $_title = '';

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
     * Acciones disponibles
     * @var Array 
     */
    protected $_action = array();

    /**
     * Mostrar la barra de acciones
     * @var boolean 
     */
    protected $_show_bar = true;

    /**
     * Añade la funcionalidad de iniciación
     * @param type $module
     * @param type $controller
     * @param type $action
     * @param type $parameters
     */
    function __construct($module, $controller, $action, $parameters) {
        parent::__construct($module, $controller, $action, $parameters);
        if (method_exists($this, 'init')) {
            call_user_func(array($this, 'init'));
        }
    }

    protected function init() {
        $this->useCRUD();
    }

    protected function after_filter() {
        if (Input::isAjax()) {
            View::select('ajax', null);
        }
    }

    protected function before_filter() {
        if ($this->scaffold == 'static')
            View::template(null);
        $this->title = empty($this->_title) ? ucwords($this->model) : $this->_title;
        $this->use_filter = $this->_use_filter;
    }

    public function index($page = 1) {
        $cols = $this->_getCols();
        $cond = Scaffold::request($this->model);
        $model = Load::model($this->model);
        $this->result = method_exists($model, $this->_index) ?
                call_user_func_array(array($model, $this->_index), array($cond, $page)) :
                $model->paginate($cond, "page: $page", "columns: $cols", 'per_page: ' . Config::get('backend.app.per_page'));
        /* asigna columnas a mostrar */
        $col = current($this->result->items);
        $this->cols = $col ? array_keys(get_object_vars($col)) : array();
        /* Acciones a mostrar */
        $this->action = $this->_action;
        /* Mostrar la barra de acciones */
        $this->show_bar = $this->_show_bar;
    }

    /**
     * Crea un Registro
     */
    public function crear() {
        try {
            if (Input::hasPost($this->model)) {
                $obj = Load::model($this->model);
                //En caso que falle la operación de guardar
                if (!$obj->save(Input::post($this->model))) {
                    Flash::error('Falló Operación');
                    //se hacen persistente los datos en el formulario
                    $this->{$this->model} = $obj;
                    return;
                } else {
                    Flash::success('Agregegado correctamente');
                    Router::redirect();
                }
            }
            // Solo es necesario para el autoForm
            $this->{$this->model} = Load::model($this->model);
        } catch (KumbiaException $e) {
            $this->{$this->model} = Load::model($this->model);
            Flash::error($e);
        }
    }

    /**
     * Edita un Registro
     */
    public function editar($id) {
        View::select('crear');

        //se verifica si se ha enviado via POST los datos
        if (Input::hasPost($this->model)) {
            $obj = Load::model($this->model);
            if (!$obj->update(Input::post($this->model))) {
                Flash::error('Falló Operación');
                //se hacen persistente los datos en el formulario
                $this->{$this->model} = Input::post($this->model);
            } else {
                return Router::toAction('');
            }
        }

        //Aplicando la autocarga de objeto, para comenzar la edición
        $this->{$this->model} = Load::model($this->model)->find((int) $id);
    }

    /**
     * Borra un Registro
     */
    public function borrar($id) {
        if (!Load::model($this->model)->delete((int) $id)) {
            Flash::error('Falló Operación');
        } else {
            Flash::success('Borrado correctamente');
        }
        //enrutando al index
        Router::redirect();
    }

    /**
     * Ver un Registro
     */
    public function ver($id) {
        $model = Load::model($this->model);
        $this->result = method_exists($model, 'view') ?
                call_user_func_array(array($model, $this->_index), array($id)) :
                $model->find_first((int) $id);
        /* asigna columnas a mostrar */
        $this->cols = array_keys(get_object_vars($this->result));
    }

    public function update($field) {
        Scaffold::update(Load::model($this->model), $field);
        die();
    }

    /**
      -     * Retorna las columnas a consultar
      -     * @return string
      - */
    protected function _getCols() {
        return !empty($this->show_cols) || is_array($this->show_cols) ?
                implode(',', $this->show_cols) : '*';
    }

    /**
     * Permite añadir una acción
     * @param string $action identificador de la accion
     * @param type $html HTML para la acción 
     */
    protected function action($action, $html) {
        $this->_action[$action] = $html;
    }

    /**
     * Asigna acciones básicas para el CRUD 
     */
    protected function useCRUD() {
        $this->action('ver', Html::linkAction('ver/%id%', '<span class="btn"><i class="icon-eye-open"></i></span>'));
        $this->action('editar', Html::linkAction('editar/%id%', '<span class="btn"><i class="icon-edit"></i></span>'));
        $this->action('borrar', Html::linkAction('borrar/%id%', '<span class="btn"><i class="icon-trash"></i></span>', 'class="js-confirm" data-msg="¿Desea Eliminar?"'));
    }

}
