<?php
namespace KBackend\Libs\Helper;
use \Form, \Haanga;
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */ 
class Field{

    /**
     * Model for form
     * @var object
     */
    protected $model = null;

    protected $options = array();

    protected $name;

    function __construct($model, $form, $name, Array $options){
        $this->form    = $form;
        $this->model   = $model;
        $this->options = $options; 
        $this->name    = $name;
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


    function __toString(){
        $model_name = $this->getNameForm();
        $field = $this->name;
        $value = isset($this->model->$field) ? $this->model->$field: NULL;
        list($id, $name, $value) = Form::getFieldData("$model_name.$field", $value);
        return  Haanga::Load('_shared/form/field.phtml', array(
                'label'    => $this->getLabel($field),
                'id'       => $id,
                'input'    => $this->input($field, $id, $name, $value),
                'error'    => $this->form->hasError($field),
                'required' => $this->isRequired($field),
        ), true);
    }

    /**
     * Return label value
     * @return string
     */
    function getLabel(){
        return isset($this->options['label']) ?
            $this->options['label'] :
            ucwords(str_replace(array('_id', '_', ), ' ', $this->name));
    }

    function input($field, $id, $name, $value){
        $type = $this->getType($field);
        return  Haanga::Load("_shared/form/$type.phtml", array(
                'id'       => $id,
                'name'     => $name,
                'value'    => $value,
                'data'     => $this->getData($field),
                'error'    => $this->form->hasError($field),
                'required' => $this->isRequired($field),
        ), true);
    }

    /**
     * Return type of field
     * @return string
     */
    protected function getType(){
        if($this->haveType()){
            return $this->options['type'];
        }
        $type = $this->type();
        $key  = static::cleanType($type);
        return $this->isEmail() ? 'email': static::defaultType($key);
    }

        /**
     * Return if is required field
     * @return bool
     */
    protected function isRequired(){
        return $this->has('required');
    }

    /**
     * Return true if $field has propiety $key
     * @param string $key 
     * @return bool
     */
    public function has($key){
        return (
                in_array($key, $this->options) ||
                 array_key_exists($key, $this->options  ));
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
     * Return if is email field
     * @return bool
     */
    public function isEmail(){
        return $this->has('email');
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
        $type = $this->type($field);
        if(isset($option[$field]['select']['list'])){
            $select = $option[$field]['select'];
            $list = $select['list'];
            if(is_callable($select['list'])){
                $param = isset($select['params']) ? $select['params']: array();
                $list = call_user_func_array($select['list'], $param);
            }
        }elseif(strncmp('enum', $type, 4) == 0){
            $tmp = explode('\',\'', substr($type, 6, -2));
            $list = array_combine($tmp, $tmp);
        }
        return $this->preProcessData($list, $field, $value);
    }

    /**
     * Preproccess a data for render
     */
    public function preProcessData(Array $list,$field, $value){
        $option = isset($this->options[$field]['select']) ? $this->options[$field]['select']:array();
        $result = array();
        /*Implement empty value*/
        if(!empty($option['empty'])){
            $result[] = (object) array(
                'value'    => '',
                'text'     => $option['empty'],
                'selected' => ''
            );
        }
        $text =  empty($option['show']) ? NULL: $option['show'];
        foreach ($list as $key => $v) {
            $obj = new \StdClass();
            $obj->value    = Form::selectValue($v, $key, 'id');
            $obj->text     = Form::selectShow($v, $text);
            $obj->selected = Form::selectedValue($value, $obj->value);
            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Return if is have type field
     * @return bool
     */
    protected function haveType(){
        return $this->has('type');
    }

        /**
     * Return type like database set
     * @param  string $field 
     * @return string
     */
    public function type(){
        $model = $this->model;
        $md = $model::metadata()->getFields();
        return empty($md[$this->name]['Type']) ? '' : $md[$this->name]['Type'];
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
           'datetime' => array('datetime',  'timestamp'),
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

   
}
