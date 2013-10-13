<?php
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */

class IndexController extends AppController {

    public function index() {

    }
    
    public function logout() {
		\KBackend\Libs\AuthACL::logout();
		return Router::redirect();
    }
    
    
}
