<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
require_once CORE_PATH . 'kumbia/kumbia_view.php';
use \Haanga;
class Template extends \KumbiaView {
    /**
     * Muestra las excepciones generadas y crea un log de las mismas.
     * @param  Exception $e [description]
     */
    public static function excepcion(\Exception $e) {
        Flash::warning('Ha Ocurrido un error');
        if (Config::get('config.application.log_exception') || !PRODUCTION) {
            Flash::error($e->getMessage());
        }else{
            Flash::error($e->getTraceAsString());
        }
        Logger::log("Exception $e");
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
     * @todo add the cache
     * @param Controller $controller
     */
    public static function render(\Controller $controller)
    {

        /*Si no hay nada termina el proceso y descarga el buffer*/
        if (!self::$_view && !self::$_template)
            return ob_end_flush();
         
        // Guarda el controlador
        self::$_controller = $controller;
         
        /*extrae las variables del controllador*/
        $vars = get_object_vars($controller);
        
         // carga la vista si no esta en produccion o se usa scaffold o no hay contenido cargado
        if (!PRODUCTION || $scaffold || !self::$_content) {
            $file = static::getFile();
            try{
                self::$_content = static::getTpl($file, $vars);
            }catch(\Exception $e){
                parent::render($controller);
            }
        } else {
            ob_clean();
        }
        echo self::$_content;
    }

    static protected function getFile(){
        /*Captura el posible scaffold*/
        $c = self::$_controller;
        $scaffold = $c->scaffold;
        $file =  self::getPath();
        if (!is_file(KBACKEND_PATH ."/views/$file")){
            $view = self::$_view;
            return ( $scaffold && $view ) ?
                "_shared/scaffolds/$scaffold/$view.phtml":
                self::$_template. '.phtml';
        }
        return $file;
    }

    public static function getTpl($file, $vars){
         /*Establece las configuraciones de haanga*/
        Haanga::configure(array(
            'template_dir' => KBACKEND_PATH.'/views',
            'cache_dir' => KBACKEND_PATH.'/temp/cache/haanga',
            'compiler' => array( /* opts for the tpl compiler */
                'strip_whitespace' => TRUE,
                'allow_exec'  => TRUE
            ),
        ));
        return Haanga::Load($file, $vars, true);
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
}
