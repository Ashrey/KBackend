<?php
namespace KBackend\Controller;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use KBackend\Libs\Config;
use \Input;
class ConfigController extends \KBackend\Libs\AuthController {
	public function index() {
		$this->values = Config::get('backend');
	}

	public function section($sec) {
		try {
			$this->values = Config::get("backend.$sec");
			if (Input::hasPost('config')) {
				$this->flash(FALSE,
					'Configuración fue Actualizada',

					'No se pudo guardar');
			}

		} catch (\Exception $e) {
			View::excepcion($e);
		}
	}
}
