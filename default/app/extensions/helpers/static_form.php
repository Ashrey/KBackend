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
 * @copyright  Copyright (c) 2005-2012 KumbiaPHP Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

/**
 * Helper para crear Formularios de un modelo automáticamente
 *
 * @category   KumbiaPHP
 * @package    Helpers
 */
class StaticForm
{

    /**
     * Genera un form de un modelo (objeto) automáticamente
     *
     * @var object 
     */
    public static function create($model){
       
        $model_name = Util::smallcase(get_class($model));
        echo "<?php \n /* create by Scaffold */  ?>\n<?php ";
        ?>echo Form::open(NULL, 'post', 'class="well"') ?> <?php
        $pk = $model->primary_key[0];
        $field = $model_name. '.'. $pk;
        /*Primary Key*/
        echo "\n<?php echo Form::hidden('$field');?>\n";
        /*Resto de los campos*/
        $fields = array_diff($model->fields, $model->_at, $model->_in, $model->primary_key);
        foreach ($fields as $field){
            $tipo = trim(preg_replace('/(\(.*\))/', '', $model->_data_type[$field])); //TODO: recoger tamaño y otros valores
            $alias = $model->get_alias($field);
            $formId = $model_name . '_' . $field;
            $formKumbia = $model_name . '.' . $field;
            $formName = $model_name . '[' . $field . ']';
            $text =  in_array($field, $model->not_null) ? "$alias *" :$alias;
            switch ($tipo) {
                case 'tinyint': case 'smallint': case 'mediumint':
                case 'integer': case 'int': case 'bigint':
                case 'float': case 'double': case 'precision':
                case 'real': case 'decimal': case 'numeric':
                case 'year': case 'day': case 'int unsigned': // Números
                    if (strripos($field, '_id', -3)) {
                        $control = "<?php  echo Form::dbSelect('$formKumbia', NULL, NULL, 'Seleccione', NULL);?>";
                    } else {
                        $control = "<?php echo Form::number('$formKumbia');?>" ;
                    }
                     break;
                case 'date': // Usar el js de datetime
                    $control = "<?php echo Form::date('$formKumbia');?>" ;
                    break;
                case 'datetime': case 'timestamp':
                    $control = "<?php echo Form::date('$formKumbia');?>" ;
                    break;
                case 'enum': case 'set': case 'bool':
                     // Intentar usar select y lo mismo para los field_id
                    $control = "<?php echo Form::text('$formKumbia');?>";
                    break;
                case 'text': case 'mediumtext': case 'longtext':
                case 'blob': case 'mediumblob': case 'longblob': // Usar textarea
                    $control = "<?php echo Form::textarea('$formKumbia');?>";
                    break;
                default: //text,tinytext,varchar, char,etc se comprobara su tamaño
                     $control = "<?php echo Form::text('$formKumbia');?>";
            }
            self::control($text, $formId, $control);
        }
        echo '<div class="form-actions">',"\n",
                "\t",'<div class="btn-group ">',"\n",
               "\t\t", '<button type="submit"class="btn btn-primary">Enviar</button>',"\n",
                 "\t\t",'<?php', ' echo Html::linkAction(\'\', \'Cancelar\', \'class="btn btn-danger js-confirm" data-msg="Desea cancelar?"\') ?>',
                 "\n\t", '</div>',"\n",
                 "\n",
                '</div>';
        echo "\n<?php";
        ?> echo Form::close();?>
        <?php
    }
    
    static function control($text, $field, $control) {
        echo 
        '<div class="control-group">',"\n",
        "\t<?php echo Form::label('$text',  '$field', 'class=\"control-label\"');?>\n",
        "\t<div class=\"controls\">\n",
        "\t\t", $control, "\n",
        "\t</div>\n</div>\n";
    }
}
