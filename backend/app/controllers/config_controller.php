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
            $ini = new \KBackend\Libs\ConfigINI($file);
            $this->values = $ini->getAll();
            if (Input::hasPost('config')) {
                if ($ini->save(Input::post('config'))) {
                    Flash::valid('Configuración fue Actualizada ');
                    
                } else {
                    Flash::warning('No se pudo guardar');
                }
           }
           
        } catch (\Exception $e) {
            View::excepcion($e);
        }
    }
}
