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
class FormBuilder {
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
	 * Template for generate label-input group
	 */
	protected $tpl = '<div class="form-group">{{label}}<div class="col-6">{{input}}</div></div>';
	
	
	function __construct($model){
		$this->model = $model;
		if(method_exists($model, 'getForm')){
			$this->options =  $model->getForm();
			$this->fields = array_keys($this->options);
		}else{
			$this->options =  array();
			$this->fields =  $this->allFields();
		}
	}

	protected function allFields(){
		$model = $this->model;
		$md = $model::metadata();
		$metadata = array_diff($md->getFieldsList(), array($md->getPK()));
		return $metadata;

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
	static function getType($key){
		$type = 'text';
		if(in_array($key, array('tinyint',  'smallint',  'mediumint', 'integer',  'int', 
			'bigint',  'float',  'double',  'precision',  'real', 'decimal',  'numeric',  'year',  'day',  'int unsigned'))){
			$type = 'number';
		}elseif($key == 'date'){
            $type = 'date';
        }elseif($key ==	'time'){
			$type= 'time';
        }elseif(in_array($key, array('datetime',  'timestamp'))){
            $type = 'datetime';
        }elseif(in_array($key, array('enum',  'set'))){ 
            $type = 'select';
        }elseif(in_array($key, array('tinytext', 'text',  'mediumtext',  'longtext', 
            'blob',  'mediumblob',  'longblob'))){
            $type = 'textarea';
		}
		return $type;
	}
	
	/**
	 * Genera los posibles atributos
	 */
	function getAttrs($field){
		$model = $this->model;
		$md = $model::metadata()->getFields();
		$type = $this->getMeta($md[$field]['Type']);
		return array(
			'type' => self::getType($type),
			//'required' => in_array($field, $this->model->not_null),
			'alias' => isset($this->options[$field]) ? $this->options[$field] : $this->model->get_alias($field),
		);
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
			$attr = $this->getAttrs($field);
			$type = $attr['type'];//form type
			$forAttr = $model_name . '_' . $field;//HTML for atributte
			$name = "$model_name.$field"; //HTML name atributte
			/*HTML generator*/
			$add = str_replace('{{label}}',Form::label($attr['alias'], $forAttr, 'class="control col-5"'), $this->tpl);
			$value = isset($this->model->$field)?$this->model->$field:null;
			$add = str_replace('{{input}}',call_user_func_array(array('Form', $type), array($name, 'class="control"', $value)), $add);
			$html .= $add;
		}
		//add button
		$html .= '<div class="text-center"><div class="btn-group">'. implode('', $this->_action). '</div></div>';
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

}
