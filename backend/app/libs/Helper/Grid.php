<?php
namespace KBackend\Libs\Helper;
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
	 * Acciones disponibles
	 * @var Array 
	 */
	protected $_action = array();

	/**
	 * Cabecera
	 *  @var array
	 */
	protected $_header = array();

	function __construct($pag=null){
		if(!is_null($pag)){
			$this->setPaginator($pag);
		}

	}
	/**
	 * Envia los datos del paginador
	 * @param Obj $pag
	 */
	function setPaginator($pag){
		$this->pag = $pag;
	}
	
	/**
	 * Permite añadir una acción
	 * @param string $action identificador de la accion
	 * @param type $html HTML para la acción 
	 */
	public function action($action, $html) {
		$this->_action[$action] = $html;
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

	/**
     * Asigna acciones básicas para el CRUD 
    */
    public function useCRUD() {
        $this->action('ver', \Html::linkAction('view/%id%', '<i class="fa fa-eye"></i>',  'class="btn btn-info"'));
        $this->action('editar', \Html::linkAction('edit/%id%', '<i class="fa fa-edit"></i>', 'class="btn btn-warning"'));
        $this->action('borrar', \Html::linkAction('delete/%id%', '<i class="fa fa-trash-o"></i>', 'class="btn btn-danger" data-msg="¿Desea Eliminar?"'));
    }

	function render(){
		$filter = \KBackend\Libs\FilterSQL::get();
		$header = empty($this->_header)?$this->pag->getHeader():$this->_header;
		return \Haanga::Load('_shared/grid.phtml', array(
			'href' => \Router::get('action'),
			'grid' => $this,
			'result' => $this->pag,
			'f'=>$filter,
			'action'=>$this->_action,
			'header'=>$header
		), true);
	}

	function __toString(){
		return $this->render();
	}
}