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
     * Permite "extender" una vista
     *
     * @param string $partial vista a extender
     * @return string
     * @throw KumbiaException
     */
    public static function extend($partial)
    {
        //Verificando el partials en el dir app
        $__file = APP_PATH . "views/$partial.phtml";

         // carga los parametros en el scope
        extract(self::getVar(), EXTR_OVERWRITE);

        // carga la vista parcial
        if (!include $__file)
            throw new KumbiaException('Vista para extender "' . $__file . '" no se encontró');
    }

}
