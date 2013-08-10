<?php

namespace KBackend\Libs;

/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class SQLFilter {

    /**
     * Modelo usado en el filtrado
     * @var type 
     */
    protected $model = null;
    
    /**
     * Columna por la que se procede a ordenar
     * @var String 
     */
    protected $order = null;
    
    /**
     * Condiciones tipo WHERE
     * @var Array 
     */
    protected $condition = array();

    /**
     * bandera que indica si es descendente el orden
     * @var bool 
     */
    protected $desc = false;
    
    /**
     * Pagina actual
     * @var int 
     */
    protected $page = 1;
    
    /**
     * Cantidad de resultados a mostrar por página
     * @var int 
     */
    protected $per_page = 10;

    /**
     *  Devuelve una instancia de la clase
     * @param String $model modelo usado
     * @return \self
     */
    static function getFilter($model) {
        if (\Session::has('filter', $model) && \Session::get('filter', $model)) {
            return \Session::get('filter', $model);
        } else {
            return new self($model);
        }
    }

    private function __construct($model) {
        $this->model = $model;
    }

    function getArray() {
        $array = array();
        //order
        if (!is_null($this->order)) {
            $desc = $this->desc ? 'DESC' : '';
            $array[] = "order: {$this->order} $desc";
        }
        //condition
        if (!empty($this->condition)) {
            $array[] = 'conditions: ' . implode(' AND ', $this->condition);
        }
        array_unshift($array, "page: $this->page", "per_page: $this->per_page");
        return $array;
    }

    /**
     * Procesa los pedidos del usuario
     */
    function request() {
        if (Input::hasPost('filter')) {
            $this->filter();
        }
    }

    /**
     * Procesa los filtros
     */
    protected function filter() {
        if (Input::post('clear')) {/* Elimina todos los filtros */
            $this->condition = array();
            Flash::info('Filtros Borrados');
        } elseif (Input::post('add')) {/* Agrega un filtro */
            $nuevo = Input::post('filter');
            $nuevo['val'] = empty($nuevo['val']) && $nuevo['val'] !== '0' ?
                    'NULL' : '"' . addslashes($nuevo['val']) . '"';
            $this->condition[uniqid()] = $nuevo;
        } elseif (Input::post('remove')) {/* Remueve un filtro */
            $key = Input::post('remove');
            unset($this->condition[$key]);
        }
    }

    function setOrder($order) {
        if ($order == $this->order) {
            $this->desc = !$this->desc;
        } else {
            $this->order = $order;
        }
    }
    
    /**
     * Establece la opciones de paginado
     * @param int $page página a mostrar
     * @param type $per_page resultados a mostrar por página
     */
    function pagination($page, $per_page){
        $this->page = (int)$page;
        $this->per_page = (int)$per_page;
    }

    /**
     * Al destruir, almacena
     */
    function __destruct() {
        \Session::set('filter', $this, $this->model);
    }

}
