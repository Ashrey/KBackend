<?php
namespace KBackend\Libs;
use \Router;
use \Haanga;
use \ArrayIterator;
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category   Kumbia
 * @package    ActiveRecord
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
 
/**
 * Implementación de paginador
 * 
 */
class Paginator implements \IteratorAggregate, \ArrayAccess
{
	/**
	 * Items de pagina
	 * 
	 * @var Array
	 */
	protected $items;
	
	/**
	 * Numero de página siguiente
	 * 
	 * @var int
	 */
	public $next;
	
	/**
	 * Número de página anterior
	 * 
	 * @var int
	 */
    public $prev;
    
    /**
     * Número de página actual
     * 
     * @var int
     */
    public $current;
    
    /**
     * Número de páginas totales
     * 
     * @var int
     */
    public $total;
    
    /**
     * Cantidad de items totales
     * 
     * @var int
     */
    public $count;
    
    /**
     * Cantidad de items por página
     * 
     * @var int
     * 
     * TODO: colocar en camelcase
     */
    public $per_page;
    
    
    
    /**
     * Constructor
     * 
     * @param string $model nombre de clase de modelo
     * @param string $sql consulta select sql
     * @param int $page numero de pagina
     * @param int $per_page cantidad de items por pagina
     * @param array $values valores
     */
    public function __construct($model, $args, $values = null)
    {
        extract($args);
        $sql = method_exists($model, 'getSQL') ? $model->getSql(): "SELECT * FROM {$model->source}";
        //var_dump($args);die;
        //Si la página o por página es menor de 1 (0 o negativo)
        if ($page < 1 || $per_page < 1) {
            throw new \KumbiaException("La página $page no existe en el páginador");
        }
        $start = $per_page * ($page - 1);
        // Valores para consulta
        if($values !== null && !is_array($values)) $values = array_slice(func_get_args(), 4);
        $sql .= " ". FilterSQL::getSQL($args);
        //var_dump($sql);
        //Cuento las apariciones atraves de una tabla derivada
        $n = $model->find_by_sql("SELECT COUNT(*) AS count FROM ($sql) AS t", $values)->count;
        
        //si el inicio es superior o igual al conteo de elementos,
        //entonces la página no existe, exceptuando cuando es la página 1
        if ($page > 1 && $start >= $n) throw new \KumbiaException("La página $page no existe en el páginador");
        
        $this->items = $model->find_all_by_sql($model->limit($sql, "offset: $start", "limit: $per_page"));
        $this->_rowCount = count($this->items);
        
        //Se efectuan los calculos para las páginas
        $this->next = ($start + $per_page) < $n ? ($page + 1) : null;
        $this->prev = ($page > 1) ? ($page - 1) : null;
        $this->current = $page;
        $this->total = ceil($n / $per_page);
        $this->count = $n;
        $this->per_page = $per_page;
	}
	

    public function getIterator() {
        return new ArrayIterator($this->items);
    }
    
    public function offsetExists ($i){
	 return isset($this->items[$i]);
	
	}
	public function offsetGet ($i){
		 return $this->items[$i];
	}
	public function offsetSet ($offset , $value ){}
	public function offsetUnset ( $offset ){}

    /*function for render paginator*/
    protected static function generateURL(){
        $filter = FilterSQL::get();
        $controller = Router::get('controller');
        $action     = Router::get('action');
        return "$controller/$action". $filter->getURL(array('page' => '-_page_-'));
    }


    function start($half){
        if ($this->current <= $half) {
            $start = 1;
        }elseif (($this->total - $this->current) < $half) {
            $start = $this->total - $show + 1;
            if ($start < 1)
                $start = 1;
        } else {
            $start = $this->current - $half;
        }
        return $start;
    }

    
    function render(){
        $show  = 10;
        $half  = floor($show / 2);
        $start = $this->start($half);
        if($start == 1){
            $start = 2;
            $show -= 1;
        }

        return Haanga::Load('_shared/pagination.phtml', array(
            'url' => self::generateURL(),
            'show' => $show,
            'pag' => $this,
            'start' => $start,
            'range' => range($start, min($this->total, ($start + $show))),
        ), true);
    }

    function __toString(){
        return $this->render();
    }
}
