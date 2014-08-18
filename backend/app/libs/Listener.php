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
use \Closure;
class Listener implements SplObserver{
    /**
     * @var Closure
     * Callback to call when event fired
     */
    private $_callback;
    
    /**
     * @param Closure $cb Callback
     */
    public function __construct(Closure $cb) {
        $this->_callback = $cb;
    }
    
    /**
    * Callback
    * @param SplSubject $subject Event info.
    */
    public function update(\SplSubject $subject) {
        $cb = $this->_callback;
        return $cb($subject);
    }
}