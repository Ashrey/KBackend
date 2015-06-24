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
use \Haanga, \Flash;

class Template extends \KumbiaView {
    protected static $_dirs = array();

    /**
     * Muestra las excepciones generadas y crea un log de las mismas.
     * @param  \Exception $e [description]
     */
    public static function excepcion(\Exception $e) {
        Flash::warning('Ha Ocurrido un error');
        if (Config::get('config.application.log_exception')) {
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
        throw new \KumbiaException(NULL, 'no_controller');
    }

    /**
     * Renderiza la vista
     * @overload
     * @todo add the cache
     * @param Controller $controller
     */
    public static function render(\Controller $controller)
    {
        spl_autoload_register(array('KumbiaAutoload', 'helper'), true, true);
        
        /*Si no hay nada termina el proceso y descarga el buffer*/
        if (!self::$_view && !self::$_template)
            return ob_end_flush();
         
        // Guarda el controlador
        self::$_controller = $controller;
         
        /*extrae las variables del controllador*/
        $vars = get_object_vars($controller);
        
         // carga la vista si no esta en produccion o se usa scaffold o no hay contenido cargado
        if ($controller->scaffold || !self::$_content) {
            $file = static::getFile();
            self::$_content = static::getTpl($file, $vars);
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
        $view = self::$_view;
        foreach (self::$_dirs as  $value) {
            if (is_file("$value{$file}")){   
                return $file;
            }elseif($scaffold && $view && is_file("{$value}_shared/scaffolds/$scaffold/$view.phtml")){
                return "_shared/scaffolds/$scaffold/$view.phtml";
            }
        }
        $tpl=self::$_template;
        return "$tpl.phtml";
    }

    /**
     * @return string
     */
    public static function getTpl($file, $vars){
         /*Establece las configuraciones de haanga*/
        Haanga::configure(array(
            'template_dir' => self::$_dirs,
            'cache_dir' => KBACKEND_PATH.'/temp/cache/haanga',
            'compiler' => array( /* opts for the tpl compiler */
                'strip_whitespace' => FALSE,
                'allow_exec'  => TRUE
            ),
        ));
        return Haanga::Load($file, $vars, true);
    }

    /**
     * Add new path for template
     * @param string $dir
     * @param string|null $key
     */ 
    public static function addPath($dir, $key = NULL){
        if(is_null($key)){
            array_unshift(self::$_dirs, $dir);
        }else{
            self::$_dirs[$key] = $dir;
        }
    }
}
Template::addPath(KBACKEND_PATH.'/views/', 'Backend');