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
				$user = User::forget($by);
				Flash::valid("Los pasos para recuperar su contraseña han sido enviados a su correo");
				$this->show = FALSE;
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}
}
