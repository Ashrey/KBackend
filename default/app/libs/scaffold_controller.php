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
    protected $_title  = '';
   
    protected function after_filter() {
        if (Input::isAjax()) {
            View::select('ajax', null);
        }
    }

    protected function before_filter() {
        if ($this->scaffold == 'static')
            View::template(null);
        $this->title = empty($this->_title)?$this->model : ucwords($this->_title);
        $this->use_filter = $this->_use_filter;
    }

    public function index($page = 1) {
        $cols = $this->_getCols();
        $this->cols = $this->show_cols;
        $cond = Scaffold::request($this->model);
        $model = Load::model($this->model);

        $this->results = method_exists($model, 'index') ?
                $model->index($cond, $page) : $model->paginate($cond, "page: $page", "columns: $cols", 'per_page: ' . Config::get('backend.app.per_page'));
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
                $model->view($id) :
                $model->find_first((int) $id);

    }

    public function update($field) {
        Scaffold::update(Load::model($this->model), $field);
        die();
    }

    /**
     * Retorna las columnas a consultar
     * @return string
     */
    protected function _getCols() {
        return !empty($this->show_cols) || is_array($this->show_cols) ?
                implode(',', $this->show_cols) : '*';
    }
}
