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
			$str = array('primera', 'segunda', 'tercera', 'cuarta', 'última');
			$captcha = new  \KBackend\Libs\Captcha();
			$this->captcha = $str[$captcha->getKey()];
			$this->text = $captcha->getCaptcha();	
			if (Input::hasPost('user')) {
				$user = new \KBackend\Model\User(Input::post('user'));
				$user->register();
				Flash::valid("Usuario Registrado, revise su correo para continuar");
			}
			Session::set('captcha', $captcha->getAswer());
		}catch(Exception $e){
			/*On error erase value*/
			unset($_POST['user']['captcha']);
			unset($_POST['user']['clave']);
			Flash::error($e->getMessage());
		}
		Session::set('captcha', $captcha->getAswer());

	}

	/**
	 * Active user by email
	 */
    public function active($id, $hash) {
        $usuario = new \KBackend\Model\User();
        if ($usuario->active($id, $hash)) {
            $this->user = $usuario;
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
