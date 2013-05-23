<?php
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ConfigController extends \KBackend\Libs\AuthController {

	
    public function index() {
        try {
            $file = 'backend';
            $this->config = $file;
            $temp = \KBackend\Libs\ConfigINI::read($file);
            $this->values = $temp;
            if (Input::hasPost('config')) {
                $config = Input::post('config');
                foreach ($config as $key => $value) {
                    $val = explode('.', $key);
                    Configuracion::set($val[0], $val[1], $value);
                }
                if (Configuracion::guardar()) {
                    Flash::valid('Configuración fue Actualizada ');
                    Logger::alert("CONFIG archivo {$file}.ini modificado");
                } else {
                    Flash::warning('No se pudo guardar');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
