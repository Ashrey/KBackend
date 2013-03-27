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

/**
 * Clase que obtiene los archivos controladores creados y sus acciones.
 * 
 */
class LectorRecursos {

    public static $_controladores;
    public static $_recursos;

    /**
     * Obtiene los recursos que están creados en la app.
     * 
     * @return array 
     */
    public static function obtenerRecursos() {
        self::$_controladores = array();
        self::$_recursos = array();
        self::escanearDir();
        self::escanearControladores();
        return self::$_recursos;
    }

    /**
     * Obtiene los recuros y los pagina.
     * 
     * @param  integer $pagina    
     * @param  integer $por_pagina 
     * @return array             
     */
    public static function recursosPaginados($pagina = 1, $por_pagina = 10) {
        return self::paginar(self::obtenerRecursos(), "page: $pagina", "per_page: $por_pagina");
    }

    /**
     * Pagina un array de recursos.
     * 
     * @param  array $recursos   
     * @param  integer $pagina     
     * @param  integer $por_pagina 
     * @return array              
     */
    public static function paginar($recursos, $pagina = 1, $por_pagina = 10) {
        require_once CORE_PATH . 'libs/kumbia_active_record/behaviors/paginate.php';
        self::obtenerRecursos();
        return Paginator::paginate($recursos, "page: $pagina", "per_page: $por_pagina");
    }

    /**
     * Escanea los directorios dentro de app/controllers/ para obtener los
     * recursos. 
     * 
     * @param  string $modulo para buscar dentro de carpetas (modulos)
     * @return array
     */
    protected static function escanearDir($modulo = NUll) {
        $dir = APP_PATH . 'controllers';
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        while($it->valid()) {
            $file = $it->getFilename();
            if (!$it->isDot() && !$it->isDir() && strpos($file, '_controller.php')){
                    self::$_controladores[] = array(
                        'dir' => $it->getSubPathName(),
                        'controlador' => str_replace('_controller.php', '', $file),
                        'modulo' => $it->getSubPath()
                    );
            }
            $it->next();
        }
    }

    /**
     * Escanea los controladores y obtiene sus metodos publicos.
     * 
     * @return array
     */
    protected static function escanearControladores() {
        foreach (self::$_controladores as $e) {
            $modulo = $e['modulo'] ? $e['modulo'] . '/' : NULL;
            /*try{
            if (!include_once APP_PATH . "controllers/{$e['dir']}")
                $reflection_class = new ReflectionClass($e['controlador']);
            }catch(Exception $e){
                
            }*/
            self::$_recursos[] = array(
                'recurso' => "$modulo{$e['controlador']}/*",
                'modulo' => $e['modulo'],
                'controlador' => $e['controlador'],
                'accion' => NULL
            );
        }
    }

}

