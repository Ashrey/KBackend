<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ResourceController extends \KBackend\Libs\ScaffoldController{
	
	protected $_model = '\KBackend\Model\Resource';
	
	protected $_title = 'Resource';

    public function escaner($pagina = 1) {
        try {
            $recurso = new Recursos();
            if (Input::hasPost('descripcion') && Input::hasPost('check')) {
                //para obtener los valores de los nuevos controladores
                $recurso->obtener_recursos_nuevos($pagina);
                if ($recurso->guardar_nuevos()) {
                    Input::delete();
                    Flash::valid('Recursos Guardados Exitosamente');
                } else {
                    Flash::warning('Complete los datos requeridos e intente nuevamente');
                }
            }
            $this->recursos = $recurso->obtener_recursos_nuevos($pagina);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
}
