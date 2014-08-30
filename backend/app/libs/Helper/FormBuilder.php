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

	/**
	 * CSS class for each field
	 * @var string
	 */
	protected $fieldClass = 'control';

	function rewind() {
        reset($this->fields);
    }

    function current() {
        return $this->field(current($this->fields));
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
		$this->fields   = static::getFields($model, $rules);
		$this->getOption($rules);
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
			$fields = method_exists($model, '_formFields') ? $model::_formFields():
				array_diff($md->getFieldsList(), array($md->getPK()));
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

	/**
	 * set the CSS class for field
	 * @param string $fieldClass CSS class
	 */
	public function setClass($fieldClass){
		$this->fieldClass = $fieldClass;


	}
	
	/**
	 * return a 'clean' type of database
	 * @param string $key 
	 * @return string
	 */
	static function cleanType($key){
		return trim(preg_replace('/(\(.*\))/', '', $key));	
	}

	/**
	 * Return type of field
	 * @param $key type of col
	 * @return string
	 */
	protected function getType($field){
		if(static::haveType($field, $this->options)){
			return $this->options[$field]['type'];
		}
		$model = $this->model;
		$md = $model::metadata()->getFields();
		$type = empty($md[$field]['Type']) ? '' : $md[$field]['Type'];
		$key = static::cleanType($type);
		return $this->isEmail($field) ? 'email': static::defaultType($key);
	}
	

	function hasError($field){
		return empty($this->has_error[$field])? NULL: $this->has_error[$field];
	}



	/**
	 * Return label value
	 * @param string $field field name
	 * @return string
	 */
	function getLabel($field){
		return isset($this->options[$field]['label']) ?
			$this->options[$field]['label'] :
		  	ucwords(str_replace(array('_id', '_', ), ' ', $field));
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

	function field($field){
		$model_name = $this->getNameForm();
		$value = isset($this->model->$field) ? $this->model->$field: NULL;
		list($id, $name, $value) = Form::getFieldData("$model_name.$field", $value);
		return  Haanga::Load('_shared/form/field.phtml', array(
				'label'    => $this->getLabel($field),
				'id'       => $id,
				'input'    => $this->input($field, $id, $name, $value),
				'error'    => $this->hasError($field),
				'required' => $this->isRequired($field),
		), true);
	}

	function input($field, $id, $name, $value){
		$type = $this->getType($field);
		return  Haanga::Load("_shared/form/$type.phtml", array(
				'id'       => $id,
				'name'     => $name,
				'value'    => $value,
				'data'     => $this->getData($field),
				'error'    => $this->hasError($field),
				'required' => $this->isRequired($field),
		), true);
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

	public static function fieldValue($field, $result) {
        /* permite llamar a las claves foraneas */
        if (isset($field[3]) && strripos($field, '_id', -3)) {
            $method = substr($field, 0, -3);
            $t = $result->$method; 
            $c = is_object($t) ? $t->non_primary[0]:null;
            $value = is_null($c)? '' :h($t->$c);
        } else {
            $value = $result->$field;
        }
        return $value;
    }

	/**
	 * Return if is required field
	 * @param string $field name
	 * @return bool
	 */
	protected function isRequired($field){
		return $this->has($field, 'required');
	}

	public function has($field, $key){
		return isset($this->options[$field]) 
			&& (
				in_array($key, $this->options[$field]) ||
				 array_key_exists($key, $this->options[$field]
			));
	}

	/**
	 * Return if is email field
	 * @param string $field name
	 * @return bool
	 */
	public function isEmail($field){
		return $this->has($field, 'email');
	}

	/**
	 * Return  data for select
	 * @param string $field name
	 * @param array $option
	 * @return array
	 */
	public function getData($field, $value=NULL){
		$list = array();
		$option = $this->options;
		if(isset($option[$field]['select']['list'])){
			$select = $option[$field]['select'];
			$list = $select['list'];
			if(is_callable($select['list'])){
				$param = isset($select['params']) ? $select['params']: array();
				$list = call_user_func_array($select['list'], $param);
			}
		}
		return static::preProcessData($list, $value);
	}

	/**
	 * Preproccess a data for render
	 */
	protected static function preProcessData(Array $list, $value){
		$result = array();
		foreach ($list as $key => $v) {
			$obj = new \StdClass();
			$obj->value    = Form::selectValue($v, $key, 'id');
            $obj->text     = Form::selectShow($v, NULL);
            $obj->selected = Form::selectedValue($value, $obj->value);
            $result[] = $obj;
		}
		return $result;
	}

	/**
	 * Return if is have type field
	 * @param string $field name
	 * @param array $option
	 * @return bool
	 */
	protected static function haveType($field, Array $option){
		return isset($option[$field]) 
			&& array_key_exists('type', $option[$field]);
	}



	/**
	 * Use for default type
	 * @param string $key 
	 * @return string
	 */
	protected static function defaultType($key){
		$ret = 'text';
		$types = array(
		   'number' => array(
		        'tinyint',  'smallint',  'mediumint', 'integer',  'int',
		        'bigint',  'float',  'double',  'precision',  'real',
		        'decimal',  'numeric',  'year',  'day',  'int unsigned'
		    ),
		   'date'     => array('date'),
		   'time'     => array('time'),
		   'datatime' => array('datetime',  'timestamp'),
		   'select'   => array('enum',  'set'),
		   'textarea' => array(
		        'tinytext', 'text',  'mediumtext',  'longtext',
		        'blob',  'mediumblob',  'longblob'
		   	)
		);
		foreach ($types as $type => $value) {
		 	if(in_array($key, $value)){
		 		$ret = $type;
		 		break;
		 	}
		}
		return $ret; 
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
            \RuntimeException('Se esperaba un array');
        }
        $rules += $merge;
        return $rules;
    }

}
