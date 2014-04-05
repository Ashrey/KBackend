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
		try{
			if (Input::hasPost('forget')) {
				$by = Input::post('forget');
				$user = new \KBackend\Model\User();
				$u = $user->find_first("email = '$by' OR login='$by'");
				if(!$u)
					throw new \Exception('Usuario o email no válido');
				$u->forget();
				Flash::valid("Los pasos para recuperar su contraseña han sido enviados a su correo");
				$this->hidden = true;
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
	}
	
	/**
	 * manda la nueva contraseña luego de validar el hash
	 */
	public function change($id=null, $hash=null){
		try{
			if($id && $hash){
				$user = new \KBackend\Model\User();
				$user->newpass($id, $hash);
				Flash::valid("La nueva contraseña ha sido enviados a su correo");
			}
		}catch(Exception $e){
			Flash::error($e->getMessage());
		}
		if($id && $hash)Redirect::to('register/change');
	}
}
