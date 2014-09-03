<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Libs\Config;
use \KBackend\Libs\Captcha;
use \KBackend\Libs\Template;
use \KBackend\Model\User;
class RegisterController extends AppController{
	
	public function before_filter(){
		/*Inactive by config*/
		if (!Config::get('backend.app.register')){
			return View::notFound();
		}
	}
	
	/**
	 * Register new user
	 */
	public function index() {
		try{
			if (Input::hasPost('user')){
				$user = new User(\Input::post('user'));
				Captcha::check();
				$user->register();
				Flash::valid("Usuario Registrado, revise su correo para continuar");
				Template::select(null, 'success');
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}

	/**
	 * Active user by email
	 */
    public function active($id, $hash) {
		$user = User::get($id);
		if ($user->active($id, $hash)) {
			Redirect::toAction('profile');
		} else {
			View::response('error');			
		}
    }
    
    /**
     * Hace una petición para cambiar la contraseña
     */
    public function forget(){
    	$this->show = TRUE;
		try{
			if (($by = Input::post('forget'))) {
				User::forget($by);
				Redirect::to('register/success?message=Los pasos para recuperar su contraseña han sido enviados a su correo');
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}

	/**
     * manda la nueva contraseña luego de validar el hash
     */
    public function change($id, $hash){
        try{
            if($id && $hash){
                $user = User::get($id);
                $user->newPassword($hash);
                Flash::valid("La nueva contraseña ha sido enviada a su correo");
            }
        }catch(Exception $e){
            Flash::error($e->getMessage());
        }
    }

    public function success(){
    	$this->title   = Input::get('title');
    	$this->message = Input::get('message');
    }
}
