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
require_once __DIR__ . '/ActiveRecord/Paginator.php';
class Paginator extends \ActiveRecord\Paginator
{
    
    /**
     * Constructor
     * 
     * @param string $model nombre de clase de modelo
     * @param string $sql consulta select sql
     * @param int $page numero de pagina
     * @param int $per_page cantidad de items por pagina
     * @param array $values valores
     */
    public function __construct(\ActiveRecord\ActiveRecord $model, $args)
    {
        extract($args);
        $table = $model::getSource();
        $sql = method_exists($model, 'index') ? $model::index(): "SELECT * FROM $table";
        parent::__construct($model, $sql, $page, $per_page);
        $sql .= " ". FilterSQL::getSQL($args);
	}

    function getHeader(){
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
