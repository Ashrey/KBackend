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
 * Flash Es la clase standard para enviar advertencias,
 * informacion y errores a la pantalla
 * 
 * @category   Kumbia
 * @package    Flash 
 * @copyright  Copyright (c) 2005-2009 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */
class ModelForm {

    /**
     * Genera un form de un modelo (objeto) automáticamente
     *
     * @var object 
     */
    public static function create($model, $action = NULL) {

        /**
         * Data extra para los campos
         */
        $skip = isset($data['skip']) ? $data['skip'] : array();
        $switch = isset($data['switch']) ? $data['switch'] : array();
        $label = isset($data['label']) ? $data['label'] : array();



        $model_name = Util::smallcase(get_class($model));
        $saltos = array_keys($switch);
        if (!$action)
            $action = ltrim(Router::get('route'), '/');
        echo Form::open($action, 'post', 'class="form-horizontal well"'), PHP_EOL;
        $pk = $model->primary_key[0];
        echo Form::hidden("{$model_name}.{$pk}", $model->$pk);
        echo '<fieldset>';
        $fields = array_diff($model->fields, $model->_at, $model->_in, $model->primary_key);
        foreach ($fields as $field) {
            /* Se salta algunos campos */
            if (in_array($field, $skip))
                continue;
            /* Permite un cambio de fieldset */
            if (in_array($field, $saltos)) {
                echo '</fieldset>';
                echo '<fieldset><legend>', $switch[$field], '</legend>';
            }
            $tipo = trim(preg_replace('/(\(.*\))/', '', $model->_data_type[$field])); //TODO: recoger tamaño y otros valores
            $alias = isset($label[$field]) ? $label[$field] : $model->get_alias($field);
            $formId = $model_name . '_' . $field;
            $name = "$model_name.$field";
            echo '<div class="control-group">';
            /* Coloco la Etiqueta */
            $css_class = in_array($field, $model->not_null) ? 
                    'class="required control-label"' : 'class="control-label"';
            echo Form::label($alias, $formId, $css_class), PHP_EOL;
            echo '<div class="controls">';
            switch ($tipo) {
                case 'tinyint': case 'smallint': case 'mediumint':case 'integer': case 'int':
                case 'bigint': case 'float': case 'double': case 'precision': case 'real':
                case 'decimal': case 'numeric': case 'year': case 'day': case 'int unsigned': // Números
                    if (strripos($field, '_id', -3)) {
                        echo Form::dbSelect($name, NULL, NULL, 'Seleccione', NULL, $model->$field);
                        break;
                    } else {
                        echo Form::text($name);
                        break;
                    }
                case 'date':
                    echo Form::date($name);
                    break;
                case 'time':
                    echo Form::time($name);
                    break;
                case 'datetime': case 'timestamp': // Usar el js de datetime
                    echo Form::datetime($name);
                    break;
                case 'enum': case 'set':
                    // Intentar usar select y lo mismo para los field_id
                    $data = explode(',', str_replace(array('\')', '\'', 'enum('), '', $model->_data_type[$field]));
                    $data = array_combine($data, array_map('ucfirst', $data));
                    $data = array_merge(array('' => 'Seleccione...'), $data);
                    echo Form::select($name, $data);
                    break;
                case'tinytext': case 'text': case 'mediumtext': case 'longtext':
                case 'blob': case 'mediumblob': case 'longblob': // Usar textarea
                    echo Form::textarea($name);
                    break;
                default: //text,tinytext,varchar, char,etc se comprobara su tamaño
                    echo Form::text($name);
                //break;
            }
            echo '</div></div>' . PHP_EOL;
        }
        View::partial('backend/submit');
        echo '</form>', PHP_EOL;
    }
}
