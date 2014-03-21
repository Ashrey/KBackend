<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */

use \KBackend\Model\User;
use \KBackend\Model\Action;

class ActionController extends  \KBackend\Libs\AuthController{

    public function index($pag= 1) {
        try {
            $this->user = User::actions($pag);
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }

    public function user($id) {
        $this->url = "action/user/$id";
        try {
            $id = (int)$id;
            $this->usuario = User::get($id);
            $this->result = Action::byUser($id);
            if (!$this->result || !$this->usuario) {
                \Flash::info('Este usuario no ha realizado ninguna acción en el sistema');
                return Redirect::to();
            }
        } catch (KumbiaException $e) {
            View::excepcion($e);
        }
    }
}
