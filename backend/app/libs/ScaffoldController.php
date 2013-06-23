<?php

namespace KBackend\Libs;

/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ScaffoldController extends \KBackend\Libs\AuthController {

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
     * Nombre del paginador a usar
     * @var String
     */
    protected $_paginator = 'backend';

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
        /**
         * Set the name of model
         */
        $last = explode('\\', $this->_model);
        $this->model = strtolower(end($last));
        if (method_exists($this, 'init')) {
            call_user_func(array($this, 'init'));
        }
    }

    protected function init() {
        $this->useCRUD();
    }

    protected function after_filter() {
        if (\Input::isAjax()) {
            \View::select('ajax', null);
        }
    }

    protected function before_filter() {
        $this->title = empty($this->_title) ? ucwords($this->_model) : $this->_title;
        $this->use_filter = $this->_use_filter;
    }

    public function cond($cmd = null, $value = null) {
        $filter = SQLFilter::getFilter($this->_model);
        if (is_null($cmd)) {
            $filter->request();
        } elseif ($cmd == 'order') {
            $filter->setOrder($value);
        }
        \Router::redirect();
    }

    public function index($page = 1) {
        try {
            $cols = $this->_getCols();
            $filter = SQLFilter::getFilter($this->_model);
            $_model = new $this->_model();
            $args = array_merge(array("page: $page", "columns: $cols", 'per_page: ' . \Config::get('backend.app.per_page')), $filter->getArray());

            $this->result = method_exists($_model, $this->_index) ?
                    call_user_func_array(array($_model, $this->_index), $args) :
                    call_user_func_array(array($_model, 'paginate'), $args);

            /* asigna columnas a mostrar */
            $col = current($this->result->items);
            $this->cols = $col ? array_keys(get_object_vars($col)) : array();
            /* Acciones a mostrar */
            $this->action = $this->_action;
            /* Mostrar la barra de acciones */
            $this->show_bar = $this->_show_bar;
            /* Envia el paginador */
            $this->paginator = $this->_paginator;
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
                    return;
                } else {
                    \Flash::success('Agregegado correctamente');
                    if (!\Input::isAjax()) {
                        \Router::toAction('');
                    }
                }
            }
            // Solo es necesario para el autoForm
            $this->{$this->_model} = new $this->_model();
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
            $obj = call_user_func(array($this->_model, '_find_first'), $id);
            if (is_object($obj)) {
                if (!$obj->update($data)) {
                    //se hacen persistente los datos en el formulario
                    $this->{$this->_model} = $data;
                } else {
                    \Flash::success('Edición hecha');
                    if (!\Input::isAjax()) {
                        \Router::toAction('');
                    }
                }
            } else {
                \Flash::error('No existe este registro');
            }
        }
        //Aplicando la autocarga de objeto, para comenzar la edición
        $this->{$this->model} = (new $this->_model())->find((int) $id);
    }

    /**
     * Borra un Registro
     */
    public function delete($id) {
        if (call_user_func(array($this->_model, '_delete'), (int) $id)) {
            \Flash::success('Borrado correctamente');
        } else {
            \Flash::error('Falló Operación');
        }
        //enrutando al index
        \Router::redirect();
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

    public function update($field) {
        Scaffold::update(Load::_model($this->_model), $field);
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
        $this->action('ver', \Html::linkAction('view/%id%', '<span class="btn"><i class="icon-eye-open"></i></span>'));
        $this->action('editar', \Html::linkAction('edit/%id%', '<span class="btn"><i class="icon-edit"></i></span>'));
        $this->action('borrar', \Html::linkAction('delete/%id%', '<span class="btn"><i class="icon-trash"></i></span>', 'class="js-confirm" data-msg="¿Desea Eliminar?"'));
    }

}
