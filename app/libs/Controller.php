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
/*Carga el namespace*/
\Haanga_Haanga::addUse('\KBackend\Libs\AuthACL');
class_alias('Haanga_Haanga', 'Haanga', FALSE);
class Controller extends \Controller {

}