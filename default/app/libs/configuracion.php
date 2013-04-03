<?php

/**
 * Backend - KumbiaPHP Backend
 * PHP version 5
 * LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Libs
 * @license http://www.gnu.org/licenses/agpl.txt GNU AFFERO GENERAL PUBLIC LICENSE version 3.
 * @author Manuel José Aguirre Garcia <programador.manuel@gmail.com>
 */
class Configuracion {

    protected static $_archivo_ini = NULL;
    protected static $_configuracion = array();

    public static function leer($file) {
        self::$_archivo_ini = APP_PATH . "config/{$file}.ini";
        self::$_configuracion = parse_ini_file(self::$_archivo_ini, true);
        foreach (self::$_configuracion as $key => $section) {
            foreach ($section as $variable => $valor) {
                if ($valor == 1) {
                    self::$_configuracion[$key][$variable] = 'On';
                } elseif (empty($valor)) {
                    self::$_configuracion[$key][$variable] = 'Off';
                }
            }
        }
        return self::$_configuracion;
    }

    public static function set($seccion, $variable, $valor) {
        self::$_configuracion[$seccion]["$variable"] = $valor;
    }

    public static function guardar() {
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
