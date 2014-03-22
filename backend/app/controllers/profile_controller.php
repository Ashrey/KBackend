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

    public function logout() {
		parent::logout();
    }  
}