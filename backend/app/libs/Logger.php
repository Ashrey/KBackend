<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KumbiaAuth;
class Logger {
    /**
     * Almacena un mensaje en el log
     *
     * @param string $type
     * @param string $msg
     */
    public static function log($msg) {
        $msg = static::getMessage($msg);
        $action = static::getAction($msg);
        /*No audito select, ni describe*/
        if (in_array($action, array('SELECT', 'DESCRIBE')) ||  Config::get('backend.app.logger') != true)
            return;
        $log = new \KBackend\Model\Action();
        $log->user_id = KumbiaAuth::isLogin() ? KumbiaAuth::get('id') : NULL;
        $log->action = $action;
        $log->extra = $msg;
        $log->date_at = date('Y-m-d H:i:s');
        if(!$log->save()) throw new \Exception("Error Processing Request");
    }

    /**
     * Return Message String
     * @param mixed $msg Message
     * @return string String messsage
     */
    protected static function getMessage($msg){
        return is_array($msg) ? print_r($msg, true): trim($msg);
    }

    /**
     * Return the action
     * @param string $msg Message string
     * @return string
     */
    protected static function getAction($msg){
        $tmp = explode(' ', $msg);
        return strtoupper($tmp[0]);
    }
}
