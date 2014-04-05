<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
use \SplSubject;
use \SplObserver;
use \SplObjectStorage;

class Event implements \SplSubject {
    private $_observers;

    static protected $_events = array();

    public function __construct() {
        $this->_observers = new SplObjectStorage();
    }

    public function attach(SplObserver $observer) {
        $this->_observers->attach($observer);
    }

    public function detach(SplObserver $observer) {
        $this->_observers->detach($observer);
    }

    public function notify() {
        foreach ($this->_observers as $observer) {
             $return = $observer->update($this);
             if(is_bool($return) && $return)
                return FALSE;
        }
        return TRUE;
    }

    /**
     * Bind a callback for a event
     * @param string $name name of event
     * @param Closure $cb Closure callback
     * @param Object|String hash
     */
    public static function bind($name, \Closure $cb, $scope = 'global'){
        $hash = is_object($scope) ? spl_object_hash ($scope): $scope;
        if(!isset(self::$_events[$hash][$name])){
            self::$_events[$hash][$name] = new self();
        }
        $observer = new Listener($cb);
        self::$_events[$hash][$name]->attach($observer);
    }

    /**
     * Fired a event
     * @param string $name name of event
     * @param Object|String hash
     * @return bool 
     */
    public static function fired($name,  $scope = 'global'){
        $hash = is_object($scope) ? spl_object_hash ($scope): $scope;
        if(isset(self::$_events[$hash][$name])){
           return  self::$_events[$hash][$name]->notify();
        }
        return TRUE;   
    }
}