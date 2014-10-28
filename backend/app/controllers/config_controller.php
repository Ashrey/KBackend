<?php
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use KBackend\Libs\Config;
class ConfigController extends \KBackend\Libs\AuthController {
    public function index() {
        try {
            $this->values = Config::get('backend');
            if (Input::hasPost('config')) {
                $this->flash($ini->save(Input::post('config')),
                    'Configuración fue Actualizada', 
                    'No se pudo guardar');
           }  
        } catch (\Exception $e) {
            View::excepcion($e);
        }
    }

    public function section($sec) {
        try {
            $this->values = Config::get("backend.$sec");
            if (Input::hasPost('config')) {
                $this->flash($ini->save(Input::post('config')),
                    'Configuración fue Actualizada', 
                    'No se pudo guardar');
           }  
        } catch (\Exception $e) {
            View::excepcion($e);
        }
    }
}
