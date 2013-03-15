<?php

class ScaffoldController extends AdminController {

    public $scaffold = 'bootstrap';
    public $model;
    
    protected $show_cols = array();
    
    protected function after_filter() {
        if (Input::isAjax()) {
            View::select('ajax', null);
        }
    }

    protected function before_filter() {
        if ($this->scaffold == 'static')
            View::template(null);
    }

    public function index($page = 1) {
        /* Se asegura de que siempre exista el filtro 
         * en el espacio de nombres */
        if (!Session::has('filter', $this->model)) {
            Session::set('filter', array(), $this->model);
        }

        /* Analiza las peticiones */
        if (Input::post('clear')) {/* Elimina todos los filtros */
            Session::set('filter', array(), $this->model);
        } elseif (Input::post('add')) {/* Agrega un filtro */
            $cond = Session::get('filter', $this->model);
            $nuevo = Input::post('filter');
            $nuevo['val'] = empty($nuevo['val']) && $nuevo['val'] !== '0' ?
                    'NULL' : '"' . addslashes($nuevo['val']) . '"';
            $cond[uniqid()] = $nuevo;
            Session::set('filter', $cond, $this->model);
        } elseif (Input::post('remove')) {/* Remueve un filtro */
            $cond = Session::get('filter', $this->model);
            $key = Input::post('remove');
            unset($cond[$key]);
            Session::set('filter', $cond, $this->model);
        }
        /* Hace Redireccion en caso de peticones POST */
       if ($_SERVER['REQUEST_METHOD'] == 'POST')
            Router::redirect();
        
        
        $cond = Session::get('filter', $this->model);
        /* Crea los filtros */
        $filter = array('1 = 1');
        foreach ($cond as $val) {
            $filter[] = implode(' ', $val);
        }
        $cond = implode(' AND ', $filter);
        $cols = !empty($this->show_cols) || is_array($this->show_cols)?
            implode(',', $this->show_cols) :'*';
        $this->cols = $this->show_cols;
        $this->results = Load::model($this->model)->paginate($cond,
                "page: $page", "columns: $cols");
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
                }else{
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
        }else{
            Flash::ok('Borrado correctamente');
        }
        //enrutando al index para listar los articulos
        Router::redirect();
    }

    /**
     * Ver un Registro
     */
    public function ver($id) {
        $this->result = Load::model($this->model)->find_first((int) $id);
    }
    
    public function update($field){
        Scaffold::update(Load::model($this->model), $field);
        die();
    }

}
