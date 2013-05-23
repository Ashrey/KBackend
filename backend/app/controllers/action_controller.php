<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ActionController extends  \KBackend\Libs\AuthController{

    public function index($pag= 1) {
        try {
            $this->user = \KBackend\Model\User::_numAcciones($pag);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function user($id, $pagina = 1) {
        $this->url = "action/user/$id";
        try {
            $id = (int)$id;
            $usr = new \KBackend\Model\User();
            $aud = new \KBackend\Model\Action();
            $this->usuario = $usr->find_first($id);
            $this->result = $aud->byUser($id, $pagina);
            if (!$this->result->items) {
                \Flash::info('Este usuario no ha realizado ninguna acción en el sistema');
                return Router::redirect();
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
}
