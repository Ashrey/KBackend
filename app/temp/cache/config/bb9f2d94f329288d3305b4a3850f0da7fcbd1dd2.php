<?php
return array (
  'app' => 
  array (
    'name' => 'Ashrey Backend',
    'logger' => 1,
    'footer' => 'Backend Kumbia PHP (C) 2014',
    'per_page' => 10,
    'register' => 1,
    'login' => 'user',
  ),
  'security' => 
  array (
    'auth' => '\\KBackend\\Model\\User',
    'acl' => 'simple',
  ),
  'database' => 
  array (
    'backend' => 
    array (
      'dsn' => 'mysql:host=localhost;dbname=backend;charset=utf8',
      'username' => 'root',
      'password' => 'ashrey',
    ),
  ),
  'email' => 
  array (
    'server' => 'mail.server.com',
    'user' => 'user@server.com',
    'password' => 'yourpassword',
    'from' => 'Ashrey Backend',
    'port' => 25,
    'security' => 'ssl',
  ),
);
?>