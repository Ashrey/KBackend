<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class RegisterController extends \AppController{
	public function before_filter(){
		if (!Config::get('backend.app.register')){
			return View::notFound();
		}
	}
	
	public function index() {
		try{
			if (Input::hasPost('user')) {
				$user = new \KBackend\Model\User(Input::post('user'));
				$user->register();
				Flash::valid("Usuario Registrado, revise su correo para continuar");
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}

    public function active($id, $hash) {
        $usuario = new \KBackend\Model\User();
        if ($usuario->active($id, $hash)) {
            $this->user = $usuario;
        } else {
            View::response('error');
        }
    }

}
