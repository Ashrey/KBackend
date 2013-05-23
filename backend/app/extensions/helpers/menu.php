<?php
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Menu {
    /**
     * Crea los menus para la app.
     * 
     * @param  int $id_user 
     * @param  int $entorno 
     * @return string          
     */
    public static function render() {
        $registros = self::load();
        $html = '';
        if ($registros) {
            $html .= '<ul class="nav">' . PHP_EOL;
            foreach ($registros as $e) {
                $html .= self::generarItems($e);
            }
            $html .= '</ul>' . PHP_EOL;
        }
        return $html;
    }

    /**
     * Genera los items del menu.
     * 
     * @param  Model $objeto_menu 
     * @param  int $entorno     
     * @return string              
     */
    protected static function generarItems($objeto_menu) {
        $sub_menu = isset($objeto_menu->sub)?$objeto_menu->sub:null;
        $class = 'menu_' . str_replace('/', '_', $objeto_menu->name);
        $class .= h(isset($objeto_menu->clases)?$objeto_menu->clases:null);
        if ($sub_menu) {
            $html = "<li class='" . h($class) . " dropdown'>" .
                    Html::link('#', h($objeto_menu->name) .
                            ' <b class="caret"></b>',
                            'class="dropdown-toggle" data-toggle="dropdown"') . PHP_EOL;
        } else {
            $html = "<li class='" . h($class) . "'>" .
                    Html::link($objeto_menu->url, h($objeto_menu->name)) . PHP_EOL;
        }
        if ($sub_menu) {
            $html .= '<ul class="dropdown-menu">' . PHP_EOL;
            foreach ($sub_menu as $e) {
                $html .= self::generarItems($e);
            }
            $html .= '</ul>' . PHP_EOL;
        }
        return $html . "</li>" . PHP_EOL;
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
    
    protected static function load(){
		return json_decode(file_get_contents(APP_PATH . 'config/menu.json'));
	}

}
