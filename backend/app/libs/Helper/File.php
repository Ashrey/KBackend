<?php
namespace KBackend\Libs\Helper;
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */ 
class File{

    /**
     * name of file
     * @var string
     */
    protected $file;

    /**
     * Type of file
     * @var string
     */
    protected $type;

    protected $name;

    function __construct($data, $name){
        $this->file  = $data['name'][$name];
        $this->type  = $data['type'][$name];
        $this->name  = $data["tmp_name"][$name];
        $this->error = $data["error"][$name];
        $this->size  = $data["size"][$name];
    }

}