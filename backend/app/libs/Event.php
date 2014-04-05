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
             if(is_bool($return) && !$return)
                return FALSE;
        }
        return TRUE;
    }

    /**
     * Bind a callback for a event
     * @param string $name name of event
     * @param Closure $cb Closure callback
     * @param object|string hash
     */
    public static function bind($name, \Closure $cb, $scope = 'global'){
        $hash = self::hash($scope);
        if(!self::exist($name, $scope)){
            self::$_events[$hash][$name] = new self();
        }
        $observer = new Listener($cb);
        self::$_events[$hash][$name]->attach($observer);
    }

    /**
     * Fired a event
     * @param string $name name of event
     * @param mixed hash
     * @return bool 
     */
    public static function fired($name,  $scope = 'global'){
        $hash = self::hash($scope);
        if(self::exist($name, $scope)){
           return  self::$_events[$hash][$name]->notify();
        }
        return TRUE;   
    }

    /**
     * Return if exist event
     * @param string $name
     * @param mixed $scope
     * @return bool 
     */
    protected static function exist($name, $scope){
        $hash = self::hash($scope);
        return isset(self::$_events[$hash][$name]);
    }

    /**
     * Return the hash of scope
     * @param mixed $scope
     * @return string
     */
    protected static function hash($scope){
        return is_object($scope) ? spl_object_hash ($scope): (string)$scope;
    }
}