<?php
return array (
  'app' => 
  array (
    'name' => 'Pagos Alidel Contigo',
    'logger' => '1',
    'footer' => 'Backend Kumbia PHP (C) 2014',
    'per_page' => '10',
    'register' => '1',
    'login' => 'user',
  ),
  'security' => 
  array (
    'auth' => '\\KBackend\\Model\\User',
    'acl' => 'simple',
  ),
  'email' => 
  array (
    'server' => 'mail.alidelcontigo.com',
    'user' => 'bot@alidelcontigo.com',
    'password' => 'B0tAlid3l',
    'from' => 'Pagos Alidel Contigo',
    'port' => '25',
  ),
);
?>