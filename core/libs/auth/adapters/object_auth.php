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
 * @category   extensions
 * @package    Auth 
 * @copyright  Copyright (c) 2005-2012 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

/**
 * Esta clase permite autenticar usuarios usando un objeto
 *
 * @category   extensions
 * @package    Auth
 */
class ObjectAuth implements AuthInterface
{
    /**
     * Identidad encontrara
     */
    private $identity = array();

    /**
     * Constructor del adaptador
     *
     * @param $auth
     * @param $extra_args
     */
    public function __construct($auth, $extra_args)
    {
        foreach (array('obj') as $param) {
            if (isset($extra_args[$param])) {
                $this->$param = $extra_args[$param];
            } else {
                throw new KumbiaException("Debe especificar el parámetro '$param' en los parámetros");
            }
        }
        unset($extra_args[0]);
        unset($extra_args['obj']);
        $this->args = $extra_args;
    }

    /**
     * Obtiene los datos de identidad obtenidos al autenticar
     * 
     */
    public function get_identity()
    {
        return $this->identity;
    }

    /**
     * Autentica un usuario usando el adaptador
     *
     * @return boolean
     */
    public function authenticate()
    {
		$obj = new $this->obj;
        $result = $obj->auth($this->args);
        if ($result) {
			$this->identity = get_object_vars($result);
        }
        return is_object($result);
    }

    /**
     * Asigna los valores de los parametros al objeto autenticador
     *
     * @param array $extra_args
     */
    public function set_params($extra_args)
    {
        foreach (array('server', 'secret', 'principal', 'password', 'port', 'max_retries') as $param) {
            if (isset($extra_args[$param])) {
                $this->$param = $extra_args[$param];
            }
        }
    }

}
