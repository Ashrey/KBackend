<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Config extends \Config {
     /**
     * Read a config file
     *
     * @param string $file file .ini
     * @param boolean $force force read of  .ini file
     * @return array
     */
    public static function & read($file, $force = FALSE)
    {
        if (isset(self::$_vars[$file]) && !$force) {
            return self::$_vars[$file];
        }

        self::$_vars[$file] = parse_ini_file(KBACKEND_PATH . "/config/$file.ini", TRUE);
        return self::$_vars[$file];
    }
}
