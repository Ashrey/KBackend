<?php
//namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \KBackend\Libs\AuthACL;
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
        if (in_array($action, array('SELECT', 'DESCRIBE')) ||  Config::get('backend.app.logger') == true)
            return;
        try {
            $log = new \KBackend\Model\Action();
            $log->user_id = AuthACL::isLogin() ? AuthACL::get('id') : NULL;
            $log->action = $action;
            $log->extra = $msg;
            $log->date_at = date('Y-m-d H:i:s');
            $log->save();
        } catch (\KumbiaException $e) {
            \View::excepcion($e);
        }
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
        $t = isset($tmp[0]) && is_string($tmp[0]) ? strtoupper($tmp[0]) : 'UNKNOW';
    }
}
