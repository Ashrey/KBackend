<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class ConfigINI{
	/**
	 * File to parse
	 */
	protected $_file = null;
	
	/**
	 * Values and key of file
	 */
	protected $_value = array();
	
	/**
	 * Last error ocurred
	 */
	protected $_lastError = 'unknoww error';
	
    public function __construct ($file) {
        $this->_file = APP_PATH . "config/{$file}.ini";
        $_conf = parse_ini_file($this->_file, true);
        foreach ($_conf as $key => $section) {
            foreach ($section as $variable => $valor) {
                if ($valor == 1) {
                    $_conf[$key][$variable] = 'On';
                } elseif (empty($valor)) {
                    $_conf[$key][$variable] = 'Off';
                }
            }
        }
        $this->_value = $_conf;
    }

    public function save($config) {
		//assign new values
		foreach ($config as $key => $value) {
			$val = explode('.', $key);
            $this->_value[$val[0]][$val[1]]=$value;
        }    
        $html = '';
        foreach ($this->_value as $key => $section) {
            $html .= "[$key]" . PHP_EOL;
            foreach ($section as $variable => $valor) {
                $valor = empty($valor) ? ' ':$valor;
                if (in_array($valor, array('On', 'Off')) || is_numeric($valor)) {
                    $html .= "$variable = $valor" . PHP_EOL;
                } else {
                    $html .= "$variable = \"$valor\"" . PHP_EOL;
                }
            }
        }
		if(is_writable($this->_file)){
			\Logger::alert("CONFIG archivo {$this->_file}.ini modificado");
			return file_put_contents($this->_file, $html);
		}else{
			$this->_lastError = '';
			return false;
		}
       
    }
	
	/**
	 * Get array with file value
	 */
	public function getAll(){
		return $this->_value;
	}
	
	/**
	 * Return last error
	 */
	public function getError(){
		return $this->_lastError;
	}
	
}
