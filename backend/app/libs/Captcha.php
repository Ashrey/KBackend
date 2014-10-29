<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Controller
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Captcha{
	/**
	 * Maximun lenght of generated string
	 * @var Integer
	 */
	protected $max;
	
	/**
	 * Generated string
	 * @var String
	 */
	protected $captcha = '';  
	
	/**
	 * Character selected
	 * @var  int
	 */
	protected $key = -1;
	
	/**
	 * Create new captcha
	 * @param $max Integer
	 */
	function __construct($max = 5){
		$this->max = $max;
		$this->generate();
	}
	
	/**
	 * Generate captcha
	 */
	protected function generate(){
		$this->captcha = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $this->max);
		$this->key = rand(0,$this->max - 1);
		\Session::set('answer', $this->captcha[$this->key], 'Captcha');
	}
	
	/**
	 * Get Aswer of Captcha
	 * @return string 
	 */
	function getAswer(){
		return \Session::get('answer', 'Captcha');
	}

	/**
	 * Return position as string
	 * @return string
	 */
	function getPos(){
		$str = array('primera', 'segunda', 'tercera', 'cuarta', 'última');
		return $str[$this->key];
	}
	
	/**
	 * To string magic method
	 * @return string
	 */
	function __toString(){
		return $this->captcha;
	}

	static function check(){
		$captcha = \Input::post('email');
		if(!isset($captcha) || !empty($captcha)){
			throw new \Exception('¿Are you a bot?');
		}
	}
}
