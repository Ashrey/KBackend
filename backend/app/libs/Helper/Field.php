<?php
namespace KBackend\Libs\Helper;
use \Input;
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */
use \View; 
class Field
{
    /**
     * Utilizado para generar los id de los radio button,
     * lleva un conteo interno
     *
     * @var array
     */
    protected static $_radios = array();

    /**
     * Obtiene el valor de un componente tomado
     * del mismo valor del nombre del campo y formulario
     * que corresponda a un atributo del mismo nombre
     * que sea un string, objeto o array.
     *
     * @param string $field
     * @param mixed $value valor de campo
     * @param boolean $filter filtrar caracteres especiales html
     * @param boolean $check si esta marcado el checkbox
     * @return Array devuelve un array de longitud 3 con la forma array(id, name, value)
     */
    public static function getField($field, $value = null, $is_check, $filter = true, $check = null)
    {
        // Obtiene considerando el patrón de formato form.field
        $formField = explode('.', $field, 2);
        // Formato modelo.campo
        if(isset($formField[1])) {
            // Id de campo
            $id = "{$formField[0]}_{$formField[1]}";
            // Nombre de campo
            $name = "{$formField[0]}[{$formField[1]}]";
        } else {
            // Asignacion de Id y Nombre de campo
            $id = $name = $field;
        }

        // Verifica en $_POST
        if(Input::hasPost($field)) {
            $value = $is_check ?
                Input::post($field) == $value: Input::post($field);
        } elseif($value === null  || ($check === null && $is_check)) {
            // Autocarga de datos
            $form = View::getVar($formField[0]);
            if(is_array($form) && isset($form[$formField[1]])) {
                $tmp_val = $form[$formField[1]];
            } elseif(is_object($form) && isset($form->$formField[1])) {
                $tmp_val = $form->{$formField[1]};
            }else{
                $tmp_val = $form;
            }
            $value = $is_check ? $tmp_val == $value : $tmp_val;
        } else if($is_check) {            
            $value = $check ? TRUE : FALSE;
        }
        // Filtrar caracteres especiales
        if (!$is_check && $value !== null && $filter) {
            $value = htmlspecialchars($value, ENT_COMPAT, APP_CHARSET);
        }
        // Devuelve los datos
        return array($id, $name, $value);
    }

    /**
     * Obtiene el valor de un componente tomado
     * del mismo valor del nombre del campo y formulario
     * que corresponda a un atributo del mismo nombre
     * que sea un string, objeto o array.
     *
     * @param string $field
     * @param mixed $value valor de campo
     * @param boolean $filter filtrar caracteres especiales html
     * @return Array devuelve un array de longitud 3 con la forma array(id, name, value)
     */
    public static function getFieldData($field, $value = null, $filter = true)
    {
        return self::getField($field, $value, FALSE, $filter);
    }
    
    /**
     * Obtiene el valor de un componente check tomado
     * del mismo valor del nombre del campo y formulario
     * que corresponda a un atributo del mismo nombre
     * que sea un string, objeto o array.
     *
     * @param string $field
     * @param string $checkValue
     * @param boolean $checked
     * @return array Devuelve un array de longitud 3 con la forma array(id, name, checked);
     */
    public static function getFieldDataCheck($field, $checkValue, $checked = null)
    {
        return self::getField($field, $checkValue, TRUE, FALSE, $checked);
    }

    /**
     * Crea un campo input
     *
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $type
     * @param string $field
     * @param string $value
     * @return string
     */
    public static function input($type, $field,$attrs = NULL, $value=NULL)
    {
       
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
         // Obtiene name, id y value (solo para autoload) para el campo y los carga en el scope
        list($id, $name, $value) = self::getFieldData($field, $value);
        return "<input id=\"$id\" name=\"$name\" type=\"$type\" value=\"$value\" $attrs/>";
    }

    /**
     * Crea un campo text
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function text($field, $attrs = NULL, $value = NULL)
    {
        return self::input('text', $field, $attrs, $value);
    }

    /**
     * Crea un campo select
     *
     * @param string $field Nombre de campo
     * @param array $data Array de valores para la lista desplegable
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string|array $value Array para select multiple (opcional)
     * @return string
     */
    public static function select($field, $attrs = NULL, $value = NULL, $data)
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        // Obtiene name, id y value (solo para autoload) para el campo y los carga en el scope
        list($id, $name, $value) = self::getFieldData($field, $value);

        $options = '';
        foreach ($data as $item) {
            $pk = $item::getPK();
            $k = htmlspecialchars($item->$pk, ENT_COMPAT, APP_CHARSET);
            $options .= "<option value=\"$k\"";
            // Si es array $value para select multiple se seleccionan todos
            if (is_array($value)) {
                if (in_array($k, $value)) {
                    $options .= ' selected="selected"';
                }
            } else {
                if ($k == $value) {
                    $options .= ' selected="selected"';
                }
            }
            $options .= '>' . htmlspecialchars($item, ENT_COMPAT, APP_CHARSET) . '</option>';
        }

        return "<select id=\"$id\" name=\"$name\" $attrs>$options</select>";
    }

    /**
     * Crea un campo checkbox
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param boolean $checked Indica si se marca el campo (opcional)
     * @return string
     */
    public static function check($field, $attrs = NULL,  $checked = NULL)
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
        
        // Obtiene name y id para el campo y los carga en el scope
        list($id, $name, $checked) = self::getFieldDataCheck($field, 1, $checked);

        if ($checked) {
            $checked = 'checked="checked"';
        }

        return "<input id=\"$id\" name=\"$name\" type=\"checkbox\" value=\"1\" $attrs $checked/>";
    }

    /**
     * Crea un campo radio button
     *
     * @param string $field Nombre de campo
     * @param string $radioValue Valor en el radio
     * @param string|array $attrs Atributos de campo (opcional)
     * @param boolean $checked Indica si se marca el campo (opcional)
     * @return string
     */
    public static function radio($field, $radioValue, $attrs = NULL, $checked = NULL)
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        // Obtiene name y id para el campo y los carga en el scope
        list($id, $name, $checked) = self::getFieldDataCheck($field, $radioValue, $checked);

        if ($checked) {
            $checked = 'checked="checked"';
        }

        // contador de campos radio
        if (isset(self::$_radios[$field])) {
            self::$_radios[$field]++;
        } else {
            self::$_radios[$field] = 0;
        }
        $id .= self::$_radios[$field];

        return "<input id=\"$id\" name=\"$name\" type=\"radio\" value=\"$radioValue\" $attrs $checked/>";
    }


    /**
     * Crea un campo hidden
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value
     * @return string
     */
    public static function hidden($field, $attrs = NULL, $value = NULL)
    {
        return self::input('hidden', $field, $attrs, $value);
    }

    /**
     * Crea un campo password
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value
     */
    public static function pass($field, $attrs = NULL, $value = NULL)
    {
       return self::input('password',$field, $attrs, $value);
    }

    /**
     * Crea un campo file
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @return string
     */
    public static function file($field, $attrs = NULL)
    {
        // aviso al programador
        if (!self::$_multipart) {
            Flash::error('Para poder subir ficheros, debe abrir el form con Form::openMultipart()');
        }
        
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }
 
        // Obtiene name y id, y los carga en el scope
        list($id, $name, ) = self::getFieldData($field, FALSE);
        return "<input id=\"$id\" name=\"$name\" type=\"file\" $attrs/>";
    }

    /**
     * Crea un campo textarea
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function textarea($field, $attrs = NULL, $value = NULL)
    {
        if (is_array($attrs)) {
            $attrs = Tag::getAttrs($attrs);
        }

        // Obtiene name, id y value (solo para autoload) para el campo y los carga en el scope
        list($id, $name, $value) = self::getFieldData($field, $value);

        return "<textarea id=\"$id\" name=\"$name\" $attrs>$value</textarea>";
    }

    /**
     * Crea un campo fecha nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function date($field, $attrs = NULL, $value = NULL)
    {
        return self::input('date',$field, $attrs, $value);
    }
    

    /**
     * Crea un campo tiempo nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function time($field, $attrs = NULL, $value = NULL)
    {
       return self::input('time',$field, $attrs, $value);
    }

    /**
     * Crea un campo fecha/tiempo nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function datetime($field, $attrs = NULL, $value = NULL)
    {
        return self::input('datetime-local',$field, $attrs, $value);
    }

    /**
     * Crea un campo numerico nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function number($field, $attrs = NULL, $value = NULL)
    {
        return self::input('number',$field, $attrs, $value);
    }


    /**
     * Crea un campo url nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function url($field, $attrs = NULL, $value = NULL)
    {
        return self::input('url',$field, $attrs, $value);
    }

    /**
     * Crea un campo email nativo (HTML5)
     *
     * @param string $field Nombre de campo
     * @param string|array $attrs Atributos de campo (opcional)
     * @param string $value (opcional)
     * @return string
     */
    public static function email($field, $attrs = NULL, $value = NULL)
    {
        return self::input('email',$field, $attrs, $value);
    }
}
