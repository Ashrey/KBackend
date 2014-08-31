<?php
namespace KBackend\Libs\Helper;
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */
use \Router, \Form, \Haanga, \View, \Validate, \Input, \Iterator;
class FormBuilder implements Iterator {
	/**
	 * Acciones disponibles
	 * @var Array 
	 */
	protected $_action = array();

	/**
	 * Almacena todo los campos
	 * @var Array
	 */
	protected $fields = array();

	/**
	 * Almacena las opciones del campo
	 * @var array()
	 */
	protected $options = array();
	
	/**
	 * Objeto del modelo
	 * @var Object
	 */
	protected $model = null;

	/**
	 * Lista de validacioens
	 * @var array
	 */
	protected $rules = array();

	/**
	 * Flag is form was validate
	 * @var boolean
	 */
	protected $validated = false;

	/**
	 * Field with error
	 * @var array
	 */
	protected $has_error = array();

	function rewind() {
        reset($this->fields);
    }

    function current() {
        return current($this->fields);
    }

    function key() {
        return current($this->fields);
    }

    function next() {
        next($this->fields);
    }

    function valid() {
        return current($this->fields);
    }

	function __construct($model, $rules=array()){
		$rules = static::load($rules);
		$this->model    = $model;
		$fields  = static::getFields($model, $rules);
		$this->getOption($rules);
		foreach ($fields as $name) {
			$options = isset($this->options[$name]) ? $this->options[$name]:array();
			$this->fields[$name] = new Field($model, $this, $name, $options);
		}
	}

	/**
	 * Get this model
	 * @return object model
	 */
	function getModel(){
		return $this->model;
	}

	/**
	 * Get the list of field based on metadata or option
	 * @param object $model l
	 * @param array $rules options
	 * @return array
	 */
	protected static function getFields($model, array &$rules){
		$md = $model::metadata();
		if(isset($rules['_fields'])){
			$fields = $rules['_fields'];
			unset($rules['_fields']);
		}else{
			$fields = array_diff($md->getFieldsList(), array($md->getPK()));
		}
		return $fields;
	}

	/**
	 * Get option of form
	 * @param array $option options
	 */
	protected function getOption(Array $option){
		$model = $this->model;
		$op = method_exists($model, '_formOption') ?
			$model::_formOption():
			array();
		$this->options  = array_merge_recursive($op, $option);
		$this->getRules($option);
	}

	/**
	 * Set a rules validation
	 * @todo optimize
	 * @param  Array  $option optio
	 */
	protected function getRules(Array $option){
		$model = $this->model;
		$rules = method_exists($model, '_rules') ?
			$model::_rules():
			array();
		$arr = array('type', 'label', 'list');
		foreach ($option as $key => $value) {
			foreach ($arr as $val) {
				if(isset($option[$key][$val]))
					unset($option[$key][$val]);
			}
		}
		$this->rules = array_merge_recursive($rules, $option);
	}

	/**
	 * Permite añadir una acción
	 * @param string $action identificador de la accion
	 * @param string $html HTML para la acción 
	 */
	public function action($action, $html) {
		$this->_action[$action] = $html;
	}
	



	function hasError($field){
		return empty($this->has_error[$field])? NULL: $this->has_error[$field];
	}



	/**
	 * Return name of the form
	 * @return string name of the form
	 */
	function getNameForm(){
		$name = strtolower(get_class($this->model));
		$ex = explode('\\', $name);
		return end($ex);
	}

	
	/**
     * Permite usar los botones predeterminados
    */
    public function useDefaultBtn() {
        $this->action('submit', '<button type="submit" class="btn btn-primary"><i class=" fa fa-check"></i> Ok</button> ');
        $this->action('calcel', \Html::linkAction('', '<i class="fa fa-times"></i> Cancelar', 'class="btn btn-danger js-confirm" data-msg="¿Está seguro?"'));
    }

	/**
	 * Genera el formulario
	 * @return String  HTML del formulario
	 */
	function __toString(){
		try {
			return  Haanga::Load('_shared/form/form.phtml', array(
		        'action' => PUBLIC_PATH . ltrim(Router::get('route'), '/'),
		        'fields' => $this
		    ), true);
		} catch (\Exception $e) {
			return $e->getmessage();
		}
		
	}


	function isValid($rules=array()){
		$name = $this->getNameForm();
		if(Input::hasPost($name))
			$this->model->dump(Input::post($name));
		$this->validated = true;
		$error = Validate::fail($this->model, array_merge($this->rules, $rules));
		$this->has_error =  $error === FALSE ? array():$error;
		return empty($this->has_error);
	}


	
	/**
	 * Load a array of a file
	 * @param  string|array $rules filename or array of rules
	 * @param  array  $merge other rules
	 * @return array        final array
	 */
	function load($rules, Array $merge = array()){
        if(is_string($rules)){
            $rules = include APP_PATH . "/extensions/form/$rules.php";
        }
        if(!is_array($rules)){
            throw new \RuntimeException('Se esperaba un array');
        }
        $rules += $merge;
        return $rules;
    }

}
