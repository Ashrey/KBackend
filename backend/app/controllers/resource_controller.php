<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ResourceController extends \KBackend\Libs\AuthController{

    /**
     * Luego de ejecutar las acciones, se verifica si la petición es ajax
     * para no mostrar ni vista ni template.
     */
    protected function after_filter() {
        if (Input::isAjax()) {
            View::select(NULL, NULL);
        }
    }

    public function index($pagina = 1) {
        try {
            $recursos = new \KBackend\Model\Resource();
            $this->recursos = $recursos->paginate("page: $pagina");
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function crear() {
        try {
            $this->titulo = 'Crear Recurso';

            if (Input::hasPost('recurso')) {
                $recurso = new Recursos(Input::post('recurso'));
                if ($recurso->save()) {
                    Flash::valid('El Recurso Ha Sido Agregado Exitosamente...!!!');
                    if (!Input::isAjax()) {
                        return Router::redirect();
                    }
                } else {
                    Flash::warning('No se Pudieron Guardar los Datos...!!!');
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function editar($id) {
        $this->titulo = 'Editar Recurso';
        try {
            $id = (int) $id;

            View::select('crear');

            $recurso = new Recursos();
            $this->recurso = $recurso->find_first($id);

            if ($this->recurso) {//validamos la existencia del recurso.
                if (Input::hasPost('recurso')) {
                    if ($recurso->update(Input::post('recurso'))) {
                        Flash::valid('El Recurso ha sido Actualizado Exitosamente...!!!');
                        if (!Input::isAjax()) {
                            return Router::redirect();
                        }
                    } else {
                        Flash::warning('No se Pudieron Guardar los Datos...!!!');
                        unset($this->recurso); //para que cargue el $_POST en el form
                    }
                }
            } else {
                Flash::warning("No existe ningun recurso con id '{$id}'");
                if (!Input::isAjax()) {
                    return Router::redirect();
                }
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function activar($id) {
        try {
            $rec = new Recursos();

            $id = (int) $id;

            if (!$rec->find_first($id)) {
                Flash::warning("No existe ningun recurso con id '{$id}'");
            } elseif ($rec->activar()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> Esta ahora <b>Activo</b>...!!!");
            } else {
                Flash::warning("No se Pudo Activar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function desactivar($id) {
        try {
            $rec = new Recursos();

            $id = (int) $id;

            if (!$rec->find_first($id)) {
                Flash::warning("No existe ningun recurso con id '{$id}'");
            } elseif ($rec->desactivar()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> Esta ahora <b>Inactivo</b>...!!!");
            } else {
                Flash::warning("No se Pudo Desactivar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

    public function eliminar($id) {
        try {
            $rec = new Recursos();
            if (!$rec->find_first($id)) {
                Flash::warning("No existe ningun recurso con id '{$id}'");
            } elseif ($rec->delete()) {
                Flash::valid("El recurso <b>{$rec->recurso}</b> ha sido Eliminado...!!!");
            } else {
                Flash::warning("No se Pudo Eliminar el Recurso <b>{$rec->recurso}</b>...!!!");
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
        return Router::redirect();
    }

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
