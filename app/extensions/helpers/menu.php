<?php
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Libs\Config;
class Menu {
    /**
     * Crea los menus para la app. 
     * @return string          
     */
    public static function render() {
        $menus =  Config::read('menu');
        return self::generate($menus);
    }

    /**
     * Generate a menu
     * @param array $menus Array of menus
     * @return string 
     */
    public static function generate($menus){
        return Haanga::Load('_shared/menu.phtml', array(
            'menu' => $menus
        ), true);
    }

    /**
     * Verifica si el item es el de la url donde nos encontramos.
     * 
     * @param  string $url 
     * @return boolean      
     */
    protected static function es_activa($url) {
        $url_actual = substr(Router::get('route'), 1);
        return (strpos($url, $url_actual) !== false || strpos($url, "$url_actual/index") !== false);
    }
}
