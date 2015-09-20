<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    
    function hello($world)
    {
        $this->say("Hello, $world");
    }

    function cleanCache(){
    	$this->say('Cleaning cache...');
    	$this->taskCleanDir(['app/temp/cache/haanga','app/temp/cache/config'])->run();
    	$this->say('Done!');
    }   
}