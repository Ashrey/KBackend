<?php
namespace KBackend\Libs;
use \Router;
use \Haanga;
use \ArrayIterator;
use \Kumbia\ActiveRecord\QueryGenerator;
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
class Paginator extends \Kumbia\ActiveRecord\Paginator
{
    
    /**
     * Constructor
     * 
     * @param mixed $model nombre de clase o modelo
     * @param Array $param Param for query
     * @param array $values values
     */
    public function __construct($model, Array $param = array(), Array $values = array())
    {
        $filter = FilterSQL::get();
        $filter->per_page =  Config::get('backend.app.per_page');
        $param  = array_merge($param, $filter->getArray());
        $values = array_merge($values, $filter->getValues());
        $page = $param['page'];
        $per_page = $param['per_page'];
        unset($param['limit'], $param['offset']);
        $sql = QueryGenerator::select($model::getSource(), $model::getDriver(), $param);
        parent::__construct($model, $sql, $page, $per_page, $values);   
	}

    function getHeader(){
        if(!$this->count()) return array();
        $model = $this->_model;
        $values = $this->_values;
        $head = $model::query($this->_sql, $values)->fetch(\PDO::FETCH_ORI_FIRST);
        return array_keys($head);
    }
	

    /*function for render paginator*/
    protected static function generateURL(){
        $filter = FilterSQL::get();
        $controller = Router::get('controller');
        $action     = Router::get('action');
        return "$controller/$action". $filter->getURL(array('page' => '-_page_-'));
    }


    /**
     * @param double $half
     * @param integer $show
     */
    function start($half, $show){
        if ($this->page <= $half) {
            $start = 1;
        }elseif (($this->count  - $this->page) < $half) {
            $start = $this->count - $show + 1;
            if ($start < 1)
                $start = 1;
        } else {
            $start = $this->page - $half;
        }
        return $start;
    }

    
    function render(){
        $show  = 10;
        $half  = floor($show / 2);
        $start = $this->start($half, $show);
        if($start == 1){
            $start = 2;
            $show -= 1;
        }

        return Haanga::Load('_shared/pagination.phtml', array(
            'url' => self::generateURL(),
            'show' => $show,
            'pag' => $this,
            'start' => $start,
            'range' => range($start, min($this->totalPages, ($start + $show))),
        ), true);
    }

    function __toString(){
        return $this->render();
    }
}
