<?php
namespace KBackend\Libs\Helper;
use \Input;
/**
 * KBackend
 * PHP version 5
 * @package Helper
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team   
 */
use \View; 
class Field extends \Form
{
    public static function select($field, $attrs = NULL, $value = NULL,  $data, $blank = '',$itemId = 'id', $show='')
    {
        return parent::select($field, $data, $attrs, $value, $blank,$itemId, $show);
    }
}
