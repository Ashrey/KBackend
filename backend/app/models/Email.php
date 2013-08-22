<?php
namespace KBackend\Model;
require APP_PATH.'libs/phpmailer/class.phpmailer.php';
require APP_PATH.'libs/phpmailer/class.smtp.php';

/**
 * KBackend
 * PHP version 5
 * @package Model
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Email {

    protected $_mail = NULL;
    protected $_error = 'error';

    public function __construct() {
        $this->_mail = new \PHPMailer();
        $this->_mail->IsSMTP();
        $this->_mail->SMTPAuth = TRUE;
        $this->_mail->SMTPSecure = \Config::get('backend.email.security');
        $this->_mail->Host =   \Config::get('backend.email.server');
        $this->_mail->Port = \Config::get('backend.email.port');
        $this->_mail->Username = \Config::get('backend.email.user');//escribir el correo
        $this->_mail->Password = \Config::get('backend.email.password');//escribir la clave
        $this->_mail->From = \Config::get('backend.email.user'); //escribir el remitente
        $this->_mail->FromName = \Config::get('backend.email.from');
        $this->_mail->CharSet = 'UTF-8';
    }

    /**
     * Envia un correo de registro exitoso al usuario.
     * 
     * @param  Usuarios $usuario 
     * @return boolean        
     */
    public function enviarRegistro(User $u, $clave, $hash) {
        return $this->create('Tu cuenta ha sido creada con exito', $u->email, $u->login,
			'register', NULL, array('user' => $u, 'clave'=>$clave, 'hash'=> $hash));
    }
    
     /**
     * Envia un correo para recuperar la contraseña
     * @param  Usuarios $usuario 
     * @param  String $hash
     * @return boolean        
     */
    public function sendPass(User $u, $hash) {
		return $this->create('Pasos para recuperar tu contraseña', 
			$u->email, $u->login, 'forget',  array('user' => $u, 'hash'=> $hash)) ;
    }
    
     /**
     * Envia un correo con una nueva contraseña
     * @param  Usuarios $usuario 
     * @param  String $hash
     * @return boolean        
     */
    public function sendNewPass(User $u, $pass) {
		return $this->create('Nueva contraseña contraseña', 
			$u->email, $u->login, 'change',  array('user' => $u, 'pass'=> $pass)) ;
    }
   
    /**
	 * Create a new mail
	 */
	protected function create($subject, $to, $toName, $tpl, $var){
		ob_start();
        \View::partial("email/$tpl", NULL, $var);
        $msg =  ob_get_clean();
        $this->_mail->Subject = "$subject - " . \Config::get('backend.app.nombre');
        $this->_mail->AltBody = strip_tags($msg);
        $this->_mail->MsgHTML($msg);
        $this->_mail->IsHTML(TRUE);
        $this->_mail->AddAddress($to, $toName);
        return $this->_enviar();
	
	}

	/**
	 * Send mail
	 */
    protected function _enviar(){
        ob_start();
        $res = $this->_mail->Send();
        ob_clean();
        $this->_error = $this->_mail->ErrorInfo;
        return $res;
	}
	
    
    /**
     *Retorna el ultimo error  
     */
    function getError(){
        return $this->_error;
    }

}

