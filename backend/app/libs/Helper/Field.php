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

    /**
     * Options for field
     */
    protected $options = array();

    /**
     * Name of col on the model
     * @var string
     */
    protected $col;

    /**
     * Form object
     * @var FormBuilder
     */
    protected $form;

    /**
     * label of widget
     * @var string
     */
    protected $label;

    /**
     * type of widget
     * @var string
     */
    protected $type;

    /**
     * Data for select type
     * @var array
     */
    protected $data;

    /**
     * Id of widget
     * @var string
     */
    protected $id;

    /**
     * Name of widget
     * @var string
     */
    protected $name;

    /**
     * Value of widget
     * @var string
     */
    protected $value;

    function __construct($model, FormBuilder $form, $col, Array $options){
        $this->form    = $form;
        $this->model   = $model;
        $this->col    = $col;
        $this->options = $options; 
        $this->label();
        $this->type();
        $model_name = $this->form->getNameForm();
        $field = $this->col;
        $value = isset($this->model->$field) ? $this->model->$field: NULL;
        list($this->id, $this->name, $this->value) = Form::getFieldData("$model_name.$field", $value);
        $this->data();
    }

    /**
     * Return all options
     * @return array
     */
    function getOptions(){
        return $this->options;
    }

    /**
     * Return label value
     * @return string
     */
    function label(){
        if(isset($this->options['label'])){
            $this->label = $this->options['label'];
            unset($this->options['label']);
        }else{
            $this->label = ucwords(str_replace(array('_id', '_', ), ' ', $this->col));
        }
    }


    /**
     * Return type of field
     * @return null
     */
    protected function type(){
        if($this->haveType()){
            $this->type = $this->options['type'];
            unset($this->options['type']);
            return ;
        }
        $type = $this->modelType();
        $key  = static::cleanType($type);
        $this->type = $this->isEmail() ? 'email': static::defaultType($key);
    }



    function __toString(){
        return  Haanga::Load('_shared/form/field.phtml', array(
                'label'    => $this->label,
                'id'       => $this->id,
                'input'    => $this->input(),
                'error'    => $this->form->hasError($this->col),
                'required' => $this->isRequired(),
        ), true);
    }


    /**
     * render widget
     * @return string
     */
    function input(){
        return  Haanga::Load("_shared/form/{$this->type}.phtml", array(
                'id'       => $this->id,
                'name'     => $this->name,
                'value'    => $this->value,
                'data'     => $this->data,
                'error'    => $this->form->hasError($this->col),
                'required' => $this->isRequired(),
        ), true);
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

    /**
     * Return if is email field
     * @return bool
     */
    public function isEmail(){
        return $this->has('email');
    }

    /**
     * Return  data for select
     * @return array
     */
    public function data(){
        if($this->type != 'select')return array();
        $list = array();
        $option = $this->options;
        $type = $this->modelType();
        if(isset($option['select']['list'])){
            $select = $option['select'];
            $list = $select['list'];
            if(is_callable($select['list'])){
                $param = isset($select['params']) ? $select['params']: array();
                $list = call_user_func_array($select['list'], $param);
            }
        }elseif(strncmp('enum', $type, 4) == 0){
            $tmp = explode('\',\'', substr($type, 6, -2));
            $list = array_combine($tmp, $tmp);
        }
        return $this->preProcessData($list);
    }

    /**
     * Preproccess a data for render
     */
    public function preProcessData(Array $list){
        $result = array();
        /*Implement empty value*/
        if(!empty($this->options['select']['empty'])){
            $result[''] = (object) array(
                'text'     => $this->options['select']['empty'],
                'selected' => ''
            );
        }
        $text =  empty($this->options['select']['show']) ? NULL: $this->options['select']['show'];
        foreach ($list as $key => $v) {
            $obj = new \StdClass();
            $value         = Form::selectValue($v, $key, 'id');
            $obj->text     = Form::selectShow($v, $text);
            $obj->selected = Form::selectedValue($value, $this->value);
            $result[$value] = $obj;
        }
        $this->options['select']['list'] = $result;
        $this->data = $result;
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
     * @return string
     */
    public function modelType(){
        $model = $this->model;
        $md = $model::metadata()->getFields();
        return empty($md[$this->col]['Type']) ? '' : $md[$this->col]['Type'];
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
