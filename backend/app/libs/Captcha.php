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
	 * @var Integer
	 */
	protected $captcha = '';  
	
	/**
	 * Character selected
	 * @var  String
	 */
	protected $aswer = '';
	
	/**
	 * Create new captcha
	 * @param $max Integer
	 */
	function __construct($max = 5){
		$this->max = $max;
	}
	
	/**
	 * Get key of string
	 */
	function getKey(){
		$this->captcha = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $this->max);
		$key = rand(0,$this->max - 1);
		$this->aswer = $this->captcha[$key];
		return $key;
	}
	
	/**
	 * Get Aswer of Captcha
	 */
	function getAswer(){
		return $this->aswer;
	}
	
	/**
	 * Get generate string
	 */
	function getCaptcha(){
		return $this->captcha;
	}
}
