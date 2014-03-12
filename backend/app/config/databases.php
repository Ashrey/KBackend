<?php
//Copiar en app/config/databases.php
//Conexión a Mysql
$databases['default'] = array(
        'dsn' => 'mysql:host=localhost;dbname=backend;charset=utf8',
        'username' => 'root',
        'password' => 'ashrey',
        'params' => array(
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', //UTF8 en PHP < 5.3.6
            PDO::ATTR_PERSISTENT => true, //conexión persistente
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
);
return $databases; //Siempre al final