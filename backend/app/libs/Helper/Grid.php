<?php
namespace KBackend\Libs\Helper;
use KBackend\Libs\Paginator;

/**
 * KBackend
 * PHP version 5
 * @package Helper 
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Grid {
	
	/**
	 * Objeto Paginator
	 * @var Paginator
	 */
	protected $pag = null;

	/**
	 * Callback para las columnas
	 * @var Array
	 */
	 protected $_cb = array();

	/**
	 * Cabecera
	 *  @var array
	 */
	protected $_header = array();

	/**
	 * @param \KBackend\Libs\Paginator $pag
	 */
	function __construct($pag=null){
		if(!is_null($pag)){
			$this->setPaginator($pag);
		}

	}
	/**
	 * Envia los datos del paginador
	 * @param Paginator $pag
	 */
	function setPaginator($pag){
		$this->pag = $pag;
	}
	
	
	/**
	 * Permite añadir un callback
	 * @param string $col nombre de la columna
	 * @param type $cb
	 */
	public function callback($col, $cb) {
		$this->_cb[$col] = $cb;
	}

	/**
	 * Envia el valor de la columna pasado por el callback
	 * @param  String $name Nombre de la columna
	 * @param  mixed $val Valor actual
	 * @return String valor con el callback
	 */
	public function getCol($name, $val){
		if(isset($this->_cb[$name])){
			return call_user_func($this->_cb[$name], $val);
		}
		return empty($val)?'—':$val;
	}
	/**
	 * Set header
	 * @return void
	 */
	public function header($arg){
		$this->_header = $arg;
	}

	function render(){
		$filter = \KBackend\Libs\FilterSQL::get();
		$header = empty($this->_header)?$this->pag->getHeader():$this->_header;
		return \Haanga::Load('_shared/grid/grid.phtml', array(
			'href' => \Router::get('action'),
			'grid' => $this,
			'result' => $this->pag,
			'f'=>$filter,
			'action'=>'_shared/grid/default.phtml',//;$this->_action,
			'header'=>$header
		), true);
	}

	function __toString(){
		return $this->render();
	}
}