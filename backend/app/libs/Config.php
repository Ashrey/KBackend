<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */

// @see Config
require_once CORE_PATH . 'kumbia/config.php';
class Config extends \Config{

    protected static $reader = NULL;
    
     /**
     * Read a config file
     *
     * @param string $file file .ini
     * @param boolean $force force read of  .ini file
     * @return array
     */
    public static function &read($file, $force = FALSE)
    {
        if(!self::$reader){
            self::$reader = new \Ashrey\Config\Config(KBACKEND_PATH . "/temp/cache/config");
        }
        $parse = self::$reader;
        self::$_vars[$file] =  $parse->read(KBACKEND_PATH . "/config/$file.yml");
        return self::$_vars[$file];
    }
}
