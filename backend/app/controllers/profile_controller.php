<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ProfileController extends  \KBackend\Libs\AuthController{

	/**
	 * Only auth is necessary
	 */
	protected $_checkPermission = false;

    public function index() {

    }

        
    /**
     * manda la nueva contraseña luego de validar el hash
     */
    public function change($id, $hash){
        try{
            if($id && $hash){
                $user = new \KBackend\Model\User();
                $user->newpass($id, $hash);
                Flash::valid("La nueva contraseña ha sido enviados a su correo");
                Redirect::to('profile');
            }
        }catch(Exception $e){
            Flash::error($e->getMessage());
        }
    }

    public function logout() {
		parent::logout();
    }  
}