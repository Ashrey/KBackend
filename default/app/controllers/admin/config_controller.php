<?php
/**
* Backend - KumbiaPHP Backend
* PHP version 5
* LICENSE
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* ERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*
* @package Controller
* @license http://www.gnu.org/licenses/agpl.txt GNU AFFERO GENERAL PUBLIC LICENSE version 3.
* @author Alberto Berroteran <programador.manuel@gmail.com>
*/
class ConfigController extends AdminController {

	
    public function index($file = 'config') {
        try {
            $this->config = $file;
            $temp = Configuracion::leer($file);
            $this->values = $temp;
            if (Input::hasPost('config')) {
                if (Configuracion::guardar()) {
                    Flash::valid('La Configuración fue Actualizada Exitosamente...!!!');
                    Acciones::add("Editó la Configuración de la aplicación", "archivo {$file}.ini");
                } else {
                    Flash::warning('No se Pudieron guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

}
