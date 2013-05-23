<?php
namespace KBackend\Libs;
/**
 * KBackend
 * PHP version 5
 * @package Libs
 * @license https://raw.github.com/Ashrey/KBackend/master/LICENSE.txt
 * @author KumbiaPHP Development Team
 */
class MyAuth
{

    /**
     * Namespace de las cookies y el hash de clave que se va a encriptar
     * Recordar que si se cambian, se deben actualizar las claves en la bd.
     */ 
    protected static $_hash = '$2a$05$AcoE7zCEG276ztq4bGUADu';

    /**
     * Realiza el proceso de autenticación de un usuario en el sistema.
     * @param  string  $user      
     * @param  string  $pass      
     * @param  boolean $encriptar 
     * @return boolean             
     */
    public static function autenticar($user, $pass, $encriptar = TRUE)
    {
        $pass = $encriptar ? self::hash($pass) : $pass;
        $auth = new \Auth('object', 'obj: \KBackend\Model\User',
                        'login: ' . $user,
                        'clave: ' . $pass,
                        "activo: 1");
        $auth->authenticate();
        return \Auth::is_valid();
    }


    /**
     * Cierra la sesion de un usuario en la app.
     * 
     */
    public static function finish()
    {
        \Auth::destroy_identity();
    }

    /**
     * Crea una encriptacion de la clave para el usuario.
     * 
     * Usada para la verificación al loguear y cuando se crea un user en la bd.
     * 
     * @param  string $pass 
     * @return string       
     */
    public static function hash($pass)
    {
        return crypt($pass, self::$_hash);
    }

}

