<?php
namespace KBackend\Model;
/**
 * KBackend
 * PHP version 5
 * @package Models
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Resource extends \KBackend\Libs\ARecord {
	protected $source = '_resource';

    protected function initialize() {
   }

    /**
     * Obtiene los recursos que no se han agregado al al bd.
     * 
     * @param  integer $pagina 
     * @return array          
     */
    public function obtener_recursos_nuevos($pagina = 1) {
        $recursos = LectorRecursos::obtenerRecursos();
        foreach ($recursos as $index => $re) {
            if ($this->exists('recurso = \'' . $re['recurso'] . '\'')) {
                unset($recursos[$index]);
            }
        }
        $recursos = LectorRecursos::paginar($recursos, $pagina, Config::get('backend.app.per_page'));
        $this->recursos_nuevos = $recursos->items;
        array_unshift($this->recursos_nuevos, null);
        return $recursos;
    }

    /**
     * Guarda los recursos que aun no estan en bd y fueron seleccionados
     * por el usuario.
     * 
     * @return boolean 
     */
    public function guardar_nuevos() {
        $chequeados = Input::post('check');
        $descripciones = Input::post('descripcion');
        $activos = Input::post('activo');
        $this->begin();
        if (!$chequeados) {
            return FALSE;
        }
        foreach ($chequeados as $valor) {
            if (empty($descripciones[$valor])) {
                Flash::error('Hay recursos seleccionados que no tienen descripción');
                $this->rollback();
                return FALSE;
            }
            $data = $this->recursos_nuevos[$valor];
            $data['descripcion'] = $descripciones[$valor];
            $data['activo'] = $activos[$valor];
            if (!$this->create($data)) {
                $this->rollback();
                return FALSE;
            }
        }
        $this->commit();
        return TRUE;
    }
}
