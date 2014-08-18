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
     * @param string $file
     */
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

    /**
     * Save the file
     * @param Array $config
     * @return integer
     */
    public function save(Array $config) {
        $this->assign($config);
        $str = $this->generate();
        if(is_writable($this->_file)){
            Logger::log("CONFIG archivo {$this->_file}.ini modificado");
            return file_put_contents($this->_file, $str);
        }
        throw new \Exception('File not writable');
    }
    
    /**
     * Assign values
     * @param Array $config values to assign
     */
    protected function assign(Array $config){
        foreach ($config as $key => $value) {
            $val = explode('.', $key);
            $this->_value[$val[0]][$val[1]]=$value;
        }   
    }

    /**
     * Generate ini content
     * @return string 
     */
    protected function generate(){
        $buffer = '';
         foreach ($this->_value as $key => $section) {
            $buffer .= "[$key]" . PHP_EOL;
            foreach ($section as $variable => $valor) {
                $buffer .= "$variable = ". $this->getValue($valor);
                $buffer .=  PHP_EOL;
            }
        }
        return $buffer;
    }

    /**
     * Add quote if not if numeric o boolan
     * @param  mixed $val value
     * @return string
     */
    protected function getValue($val){
        return (in_array($val, array('On', 'Off')) || is_numeric($valor)) ?
                     $val: "\"$valor\"";
    }

    /**
     * Get array with file value
     */
    public function getAll(){
        return $this->_value;
    }
    
}
