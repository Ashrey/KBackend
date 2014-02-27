<?php

/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 * */
// @see KumbiaView
require_once CORE_PATH . 'kumbia/kumbia_view.php';

class View extends KumbiaView {

    /**
     * Muestra las excepciones generadas y crea un log de las mismas.
     * 
     * @param  KumbiaException $e [description]
     */
    public static function excepcion(KumbiaException $e) {
        Flash::warning('Ha Ocurrido un error');
        if (Config::get('config.application.log_exception') || !PRODUCTION) {
            Flash::error($e->getMessage());
        }
        if (!PRODUCTION) {
            Flash::error($e->getTraceAsString());
        }
        Logger::critical($e); //comentar en caso de error de que no se pueda escribir en los logs.
        Flash::info('Si el problema persiste por favor informe al administrador del sistema');
    }

    /**
     * Muestra la vista de notFound de la app.
     * 
     */
    public function notFound() {
        throw new KumbiaException(NULL, 'no_controller');
    }

    /**
     * Renderiza la vista
     * @overload
     * @param Controller $controller
     * @param string $url url a renderizar
     */
    public static function render(/* Controller */ $controller, /* Router */  $_url)
    {
        /*Establece las configuraciones de haanga*/
        Haanga::configure(array(
            'template_dir' => APP_PATH.'views',
            'cache_dir' => APP_PATH.'temp/cache/haanga',
            'compiler' => array( /* opts for the tpl compiler */
                'strip_whitespace' => TRUE,
                'allow_exec'  => TRUE
            ),
        ));

        /*Si no hay nada termina el proceso y descarga el buffer*/
        if (!self::$_view && !self::$_template)
            return ob_end_flush();
        
        // Guarda el controlador
        self::$_controller = $controller;
        /*Captura el posible scaffold*/
        $scaffold = $controller->scaffold;
        
        /*extrae las variables del controllador*/
        $vars = get_object_vars($controller);

        // si se encuentra en produccion
        if (PRODUCTION) {
            // si se cachea vista
            if (self::$_cache['type'] == 'view') {
                // el contenido permanece nulo si no hay nada cacheado o la cache expiro
                self::$_content = Cache::driver()->get($_url, self::$_cache['group']);
            }
        }
        
         // carga la vista si no esta en produccion o se usa scaffold o no hay contenido cargado
        if (!PRODUCTION || $scaffold || !self::$_content) {
            // Carga el contenido del buffer de salida
            self::$_content = ob_get_clean();
            // Renderizar vista
            if ($view = self::$_view) {
                $file =  self::getPath();
               
                if (!is_file(APP_PATH ."views/$file")){
                    $file = $scaffold && !is_file(APP_PATH ."views/$view.phtml")? "_shared/scaffolds/$scaffold/$view.phtml": "$view.phtml";
                }
                

                self::$_content = Haanga::Load($file, $vars, true);
                // si esta en produccion y se cachea la vista
                if (PRODUCTION && self::$_cache['type'] == 'view') {
                    Cache::driver()->save(self::$_content, self::$_cache['time'], $_url, self::$_cache['group']);
                }
            }
        } else {
            ob_clean();
        }
        echo self::$_content;
    }

    public static function content()
    {
        if (isset($_SESSION['KUMBIA.CONTENT'])) {
            $content = $_SESSION['KUMBIA.CONTENT'];
            unset($_SESSION['KUMBIA.CONTENT']);
            return $content;
        }
        return self::$_content;
    }

    static function aclGet($key){
       return \KBackend\Libs\AuthACL::get($key);
    }


}
