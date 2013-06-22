<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * Clase de Autenticacón por BD
 * 
 * @category   Kumbia
 * @package    Auth
 * @subpackage Adapters
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

/**
 * Clase de Autenticacón por BD
 *
 * @category   Kumbia
 * @package    Auth
 * @subpackage Adapters
 */
class ObjectAuth extends Auth2
{
    /**
     * Campos que se cargan del modelo
     * 
     * @var array
     */
    protected $_fields = array('id');

    /**
     * Asigna el modelo a utilizar
     *  
     * @param string $model nombre de modelo
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * Asigna el namespace de sesion donde se cargaran los campos de modelo
     *  
     * @param string $namespace namespace de sesion
     */
    public function setSessionNamespace($namespace)
    {
        $this->_sessionNamespace = $namespace;
    }

    /**
     * Indica que campos del modelo se cargaran en sesion
     *  
     * @param array $fields campos a cargar
     */
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    /**
     * Check
     * 
     * @param $username
     * @param $password
     * @return bool
     */
    protected function _check()
    {
        $arg = Util::getParams(func_get_args());
        $_obj = $arg['obj'];
        $obj = new $_obj();
        unset($arg['obj']);
        $result = $obj->auth($arg);
        if ($result) {
            $this->_data += get_object_vars($result);
        }
        $this->_data['_valid'] = is_object($result);
        return $this->_data['_valid'];
    }

}
