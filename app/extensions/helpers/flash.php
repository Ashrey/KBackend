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
class Flash {

    /**
     * get the array buffer
     * @return array
     */
    public static function getBuffer(){
        return Session::has('content', 'Flash') ?
            Session::get('content', 'Flash'): array();
    }

    /**
     * Delete a buffer
     */
    public static function cleanBuffer(){
        Session::delete('content', 'Flash');
    }

    /**
     * Visualiza un mensaje flash
     *
     * @param string $name	Para tipo de mensaje y para CSS class='$name'.
     * @param string $text 	Mensaje a mostrar
     */
    public static function show($name, $text) {
        $temp = static::getBuffer();
        if(!isset($temp[$name])) {
            $temp[$name] = array($text);
        }else{
            $temp[$name][] = $text;
        }
        Session::set('content',$temp, 'Flash');
    }



    /**
     * Get content buffer
     * @return string
     */
    public static function content(){
        $b = static::getBuffer();
        $buffer = '';
        foreach($b as $name => $value){
            $buffer .= "<div class=\"$name alert flash\" data-alert=\"alert\" data-dismiss1=\"alert\">
                <a class=\"close\" data-dismiss=\"alert\"  href=\"#\">×</a><ul>";
            foreach($value as $text){
                $buffer .= "<li>$text</li>";
            }
            $buffer.='</ul></div>';
        }
        static::cleanBuffer();
        return $buffer;
    }

    /**
     * Visualiza un mensaje de error
     *
     * @param string $text
     */
    public static function error($text) {
        return self::show('alert-danger', $text);
    }

    /**
     * Visualiza un mensaje de advertencia en pantalla
     *
     * @param string $text
     */
    public static function warning($text) {
        return self::show('alert-warning', $text);
    }

    /**
     * Visualiza informacion en pantalla
     *
     * @param string $text
     */
    public static function info($text) {
        return self::show('alert-info', $text);
    }

    /**
     * Visualiza informacion de suceso correcto en pantalla
     *
     * @param string $text
     */
    public static function valid($text) {
        return self::show('alert-success', $text);
    }

    /**
     * Visualiza informacion en pantalla
     *
     * @param string $text
     *
     * @deprecated  ahora Flah::info()
     */
    public static function notice($text) {
        return self::show('alert-info info', $text);
    }

    /**
     * Visualiza informacion de Suceso en pantalla
     *
     * @param string $text
     *
     * @deprecated  ahora Flash::valid()
     */
    public static function success($text) {
        return self::show('alert-success', $text);
    }

}
