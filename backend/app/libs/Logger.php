<?php
//namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class Logger {

    /**
     * Indica si hay transaccion o no
     *
     * @var boolean
     */
    private static $transaction = false;

    /**
     * Array con mensajes de log en cola en una transsaccion
     *
     * @var array
     */
    private static $quenue = array();

    /**
     * Inicializa el Logger
     */
    public static function initialize($name = '') {
        
    }

    /**
     * Especifica el PATH donde se guardan los logs
     *
     * @param string $path
     */
    public static function set_path($path) {
        
    }

    /**
     * Obtener el path actual
     *
     * @return $path
     */
    public static function get_path() {
        
    }

    /**
     * Almacena un mensaje en el log
     *
     * @param string $type
     * @param string $msg
     * @param string $name_log
     */
    public static function log($type = 'DEBUG', $msg, $name_log) {
        if (is_array($msg)) {
            $msg = print_r($msg, true);
        }
        if (self::$transaction) {
            self::$quenue[] = "[$date][$type] " . $msg;
        } else {
            try {
                $tmp = explode(' ', trim($msg));
                $t = isset($tmp[0]) && is_string($tmp[0]) ? strtoupper($tmp[0]) : 'UNKNOW';
                /*No audito select, ni describe*/
                if ($t === 'SELECT' ||    $t === 'DESCRIBE')
                    return;
                if (\Config::get('backend.app.guardar_auditorias') == true) {
                    $auditoria = new \KBackend\Model\Action();
                    $auditoria->user_id = \Auth::is_valid()? \Auth::get('id') : NULL;
                    $auditoria->action = $t;
                    $auditoria->type = $type;
                    $auditoria->extra = $msg;
                    $auditoria->save();
                }
            } catch (\KumbiaException $e) {
                \View::excepcion($e);
            }
        }
    }

    /**
     * Inicia una transacción
     *
     */
    public static function begin() {
        self::$transaction = true;
    }

    /**
     * Deshace una transacción
     *
     */
    public static function rollback() {
        self::$transaction = false;
        self::$quenue = array();
    }

    /**
     * Commit a una transacción
     */
    public static function commit() {
        self::$transaction = false;
        foreach (self::$quenue as $msg) {
            self::log($msg);
        }
    }

    /**
     * Cierra el Logger
     *
     */
    public static function close() {
        
    }

    /**
     * Genera un log de tipo WARNING
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function warning($msg, $name_log = '') {
        self::log('WARNING', $msg, $name_log);
    }

    /**
     * Genera un log de tipo ERROR
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function error($msg, $name_log = '') {
        self::log('ERROR', $msg, $name_log);
    }

    /**
     * Genera un log de tipo DEBUG
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function debug($msg, $name_log = '') {
        self::log('DEBUG', $msg, $name_log);
    }

    /**
     * Genera un log de tipo ALERT
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function alert($msg, $name_log = '') {
        self::log('ALERT', $msg, $name_log);
    }

    /**
     * Genera un log de tipo CRITICAL
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function critical($msg, $name_log = '') {
        self::log('CRITICAL', $msg, $name_log);
    }

    /**
     * Genera un log de tipo NOTICE
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function notice($msg, $name_log = '') {
        self::log('NOTICE', $msg, $name_log);
    }

    /**
     * Genera un log de tipo INFO
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function info($msg, $name_log = '') {
        self::log('INFO', $msg, $name_log);
    }

    /**
     * Genera un log de tipo EMERGENCE
     * 
     * @return 
     * @param string $msg
     * @param string $name_log
     */
    public static function emergence($msg, $name_log = '') {
        self::log('EMERGENCE', $msg, $name_log);
    }

    /**
     * Genera un log Personalizado
     * 
     * @param string $type
     * @param string $msg
     * @param string $name_log
     */
    public static function custom($type = 'CUSTOM', $msg, $name_log = '') {
        self::log($type, $msg, $name_log);
    }

}
