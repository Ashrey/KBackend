<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ConfigINI{
	
	/**
	 * Read a ini FILE and parse
	 */
    public static function read($file) {
        $_file = APP_PATH . "config/{$file}.ini";
        $_conf= parse_ini_file($_file, true);
        foreach ($_conf as $key => $section) {
            foreach ($section as $variable => $valor) {
                if ($valor == 1) {
                    $_conf[$key][$variable] = 'On';
                } elseif (empty($valor)) {
                    $_conf[$key][$variable] = 'Off';
                }
            }
        }
        return $_conf;
    }

    public static function save() {
        $html = '';
        foreach (self::$_configuracion as $key => $section) {
            $html .= "[$key]" . PHP_EOL;
            foreach ($section as $variable => $valor) {
                $valor = empty($valor) ? ' ':$valor;
                if (in_array($valor, array('On', 'Off')) || is_numeric($valor)) {
                    $html .= "$variable = $valor" . PHP_EOL;
                } else {
                    $html .= "$variable = \"$valor\"" . PHP_EOL;
                }
            }
        }
        return file_put_contents(self::$_archivo_ini, $html);
    }

}
