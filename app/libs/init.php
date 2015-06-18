<?php
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
\KBackend\Libs\Event::bind('LoginSuccess', function(){
    \KBackend\Libs\Logger::log('Login Success');
});