<?php

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
 * @package    Form 
 * @copyright  Copyright (c) 2005-2013 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class FormBuilder{
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


	function __construct($model){
		$this->model  = $model;
		$this->fields = static::allFields($model);
		$this->options = static::getOption($model);
	}

	/**
	 * Get the list of field based on metadata. Remove PK
	 * @param string name of model
	 * @return array
	 */
	protected static function allFields($model){
		$md = $model::metadata();
		$metadata = method_exists($model, '_formFields') ? $model::_formFields():
			array_diff($md->getFieldsList(), array($md->getPK()));
		return $metadata;
	}

	/**
	 * Get option of form
	 * @param string name of model
	 * @return array
	 */
	protected function getOption($model){
		$option = method_exists($model, '_formOption')? $model::_formOption():
			array();
		$rules = method_exists($model, '_rules')? $model::_rules():
			array();
		return array_merge_recursive($option, $rules);
	}

	/**
	 * Permite añadir una acción
	 * @param string $action identificador de la accion
	 * @param type $html HTML para la acción 
	 */
	public function action($action, $html) {
		$this->_action[$action] = $html;
	}
	
	static function getMeta($key){
		return trim(preg_replace('/(\(.*\))/', '', $key));	
	}

	/**
	 * Return type of field
	 * @param $key type of col
	 * @return string
	 */
	protected function getType($field){
		if(static::haveType( $field, $this->options)){
			return $this->options[$field]['type'];
		}
		$model = $this->model;
		$md = $model::metadata()->getFields();
		$key = $this->getMeta($md[$field]['Type']);
		return static::isEmail($field, $this->options) ? 'email': static::defaultType($key);
	}
	
	/**
	 * Genera los posibles atributos
	 */
	function getAttrs($field){
		return array(
			'required' => $this->isRequired($field, $this->options),
			'class' => 'control'
		);
	}



	/**
	 * Return label value
	 * @param string $field field name
	 * @return string
	 */
	function getLabel($field){
		return isset($this->options[$field]['alias']) ?
		 $this->options[$field]['alias'] :
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
		$model_name = $this->getNameForm();
		//form init
		$action = ltrim(Router::get('route'), '/');
        $html .= Form::open($action, 'post', 'class="horizontal"');
		foreach($this->fields as $field){
			$attr = $this->attrStr($this->getAttrs($field));
			$type = $this->getType($field);
			$id   = "{$model_name}_{$field}";//HTML for atributte
			$name = "$model_name.$field"; //HTML name atributte
			$data = static::getData($field, $this->options);
			/*HTML generator*/
			$value = isset($this->model->$field)?$this->model->$field:null;
			$add   = call_user_func_array(array('Field', $type), array($name, $attr, $value, $data));
			$html .= Haanga::Safe_Load('_shared/field.phtml', array(
					'label' => $this->getLabel($field),
					'id'   => $id,
					'input' => $add
				), true);
		}
		//add button
		$html .=  Haanga::Safe_Load('_shared/submit.phtml',array(), true);
		return "$html</form>";
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
	 * @param array $options
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
	 * @param array $options
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
	 * @param array $options
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
	 * @param array $options
	 * @return bool
	 */
	protected static function haveType($field, Array $option){
		return isset($option[$field]) 
			&& array_key_exists('type', $option[$field]);
	}



	/**
	 * Use for default type
	 * @param string @key 
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
}
