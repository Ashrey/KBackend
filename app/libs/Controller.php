<?php
namespace KBackend\Libs;
/**
 * Todas las controladores heredan de esta clase en un nivel superior
 * por lo tanto los metodos aqui definidos estan disponibles para
 * cualquier controlador.
 *
 * @category Kumbia
 * @package Controller
 * */
// @see Controller nuevo controller
require_once CORE_PATH . 'kumbia/controller.php';
/*Carga el namespace*/
\Haanga::addUse('\KBackend\Libs\AuthACL');
class Controller extends \Controller {

}