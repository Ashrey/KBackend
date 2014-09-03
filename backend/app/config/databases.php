<?php
//Copiar en app/config/databases.php
//Conexión a Mysql
$databases['backend'] = array(
        'dsn' => 'mysql:host=localhost;dbname=alidel;charset=utf8',
        'username' => 'root',
        'password' => 'ashrey',
);
return $databases; //Siempre al final