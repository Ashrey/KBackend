<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class MyAcl {

    /**
     * Objeto Acl2
     *
     * @var SimpleAcl
     */
    static protected $_acl = null;

    /**
     * arreglo con los templates para cada usuario
     *
     * @var array 
     */
    protected $_templates = array();

    /**
     * Recurso al que se esta intentando acceder
     *
     * @var string 
     */
    protected $_recurso_actual = NULL;

    /**
     * Crea las reglas para el ACL.
     */
    public function __construct() {
        //cargamos la lib Acl2 con el adaptador por defecto (SimpleAcl)
        self::$_acl = \Acl2::factory();
        //obtenemos el rol actual
        $rol = \KBackend\Model\Role::_find_first(\Auth::get('role_id'));
        //establecemos los recursos permitidos para el rol
        $this->_establecerRecursos($rol->id, $rol->getRecursos());
        self::$_acl->user(\Auth::get('id'), array($rol->id));
    }

    /**
     * Establece los recursos a los que un rol tiene acceso
     *
     * @param int $rol id del rol
     * @param array $recursos resultado de una consulta del ActiveRecord
     */
    protected function _establecerRecursos($rol, $recursos) {
        $urls = array();
        foreach ($recursos as $e) {
			$e->recurso = !empty($e->modulo) ? "$e->module/" : '';
			$e->recurso .= "$e->controller/";
			$e->recurso .=!empty($e->accion) ? "$e->action" : '*';
            $urls[] = $e->recurso;
        }
        self::$_acl->allow($rol, $urls); //damos permiso al rol de acceder al arreglo de recursos
    }


    /**
     * Verifica si el usuario conectado tiene permisos de acceso al recurso actual
     *
     * Por defecto trabaja con el id del usuario en sesión.
     * Ademas hace uso del Router para obtener el recurso actual.
     *
     * @return boolean resultado del chequeo
     */
    public function check() {
        $usuario = \Auth::get('id');
        $modulo = \Router::get('module');
        $controlador = \Router::get('controller');
        $accion = \Router::get('action');
        if (isset($this->_templates["$usuario"])) {
            if (file_exists(APP_PATH . 'views/_shared/templates/' . $this->_templates["$usuario"] . '.phtml')) {
                \View::template("{$this->_templates["$usuario"]}");
            } else {
                \Flash::error("No existe el template <b>{$this->_templates["$usuario"]}</b> El cual está siendo usado por el perfil actual");
            }
        }
        if ($modulo) {
            $recurso1 = "$modulo/$controlador/$accion";
            $recurso2 = "$modulo/$controlador/*";  //por si tiene acceso a todas las acciones
            $recurso3 = "$modulo/*/*";  //por si tiene acceso a todos los controladores
        } else {
            $recurso1 = "$controlador/$accion";
            $recurso2 = "$controlador/*"; //por si tiene acceso a todas las acciones
            $recurso3 = "*/*";  //por si tiene acceso a todos los controladores
        }
        $recurso4 = "*";  //por si tiene acceso a todo el sistema
        //si se cumple algunas de las codiciones, el user tiene permiso.
        return self::$_acl->check($recurso1, $usuario) ||
                self::$_acl->check($recurso2, $usuario) ||
                self::$_acl->check($recurso3, $usuario) ||
                self::$_acl->check($recurso4, $usuario);
    }
}
