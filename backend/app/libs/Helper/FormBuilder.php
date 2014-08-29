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
	 * @return array
	 */
	protected function getOption(Array $option){
		$model = $this->model;
		$op = method_exists($model, '_formOption') ?
			$model::_formOption():
			array();
		$this->options  = array_merge_recursive($op, $option);

		$rules = method_exists($model, '_rules') ?
			$model::_rules():
			array();
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
		return static::isEmail($field, $this->options) ? 'email': static::defaultType($key);
	}
	
	/**
	 * Genera los posibles atributos
	 */
	function getAttrs($field){
		return array(
			'required' => $this->isRequired($field, $this->options),
			'class' => $this->fieldClass,
		);
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
		$html = '';
		//form init
		$action = ltrim(Router::get('route'), '/');
        $html .= Form::open($action, 'post', 'class="horizontal" novalidate');
		foreach($this->fields as $field){
			$html .= $this->field($field);
		}
		//add button
		$html .=  Haanga::Safe_Load('_shared/submit.phtml',array(), true);
		return "$html</form>";
	}

	function field($field){
		$model_name = $this->getNameForm();
		$attr = $this->attrStr($this->getAttrs($field));
		$type = $this->getType($field);
		$id   = "{$model_name}_{$field}";//HTML for atributte
		$name = "$model_name.$field"; //HTML name atributte
		$data = static::getData($field, $this->options);
		/*HTML generator*/
		$value = isset($this->model->$field)?$this->model->$field:null;
		$add   = call_user_func_array(array('KBackend\Libs\Helper\Field', $type), array($name, $attr, $value, $data));
		return  Haanga::Load('_shared/field.phtml', array(
				'label' => $this->getLabel($field),
				'id'   => $id,
				'input' => $add,
				'error' => $this->hasError($field),
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
     * Return string if HTML attr
     * @param array $attributes
     * @return string
     */
    protected static function attrStr(Array $attributes){
    	$output = '';
   		foreach ($attributes as $name => $value) {
    		if (is_bool($value)) {
       		 	if ($value) $output .= $name . ' ';
		    } else {
		        $output .= sprintf('%s="%s"', $name, $value);
		    }
		}
		return $output;
	}

	/**
	 * Return if is required field
	 * @param string $field name
	 * @param array $option
	 * @return bool
	 */
	protected static function isRequired($field, Array $option){
		return isset($option[$field]) 
			&& (
				in_array('required', $option[$field]) ||
				 array_key_exists('required', $option[$field]
			));
	}

	/**
	 * Return if is email field
	 * @param string $field name
	 * @param array $option
	 * @return bool
	 */
	protected static function isEmail($field, Array $option){
		return isset($option[$field]) 
			&& (
				in_array('email', $option[$field]) ||
				 array_key_exists('email', $option[$field]
			));
	}

	/**
	 * Return if is email field
	 * @param string $field name
	 * @param array $option
	 * @return array
	 */
	protected static function getData($field, Array $option){
		return isset($option[$field]) 
			&& (
				in_array('data', $option[$field]) ||
				 array_key_exists('data', $option[$field]
			)) ?
				call_user_func_array(
					$option[$field]['data'],
					isset($option[$field]['dataparam']) ?
						$option[$field]['dataparam']:
						array()
				):
				array();
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
	 * @param string @key
	 * @param string $key 
	 * @return string
	 */
	protected static function defaultType($key){
		if(in_array($key, array('tinyint',  'smallint',  'mediumint', 'integer',  'int', 
			'bigint',  'float',  'double',  'precision',  'real', 'decimal',  'numeric',  'year',  'day',  'int unsigned'))){
			return 'number';
		}elseif($key == 'date'){
            return 'date';
        }elseif($key ==	'time'){
			return 'time';
        }elseif(in_array($key, array('datetime',  'timestamp'))){
            return 'datetime';
        }elseif(in_array($key, array('enum',  'set'))){ 
            return 'select';
        }elseif(in_array($key, array('tinytext', 'text',  'mediumtext',  'longtext', 
            'blob',  'mediumblob',  'longblob'))){
            return 'textarea';
		}
		return 'text';
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
