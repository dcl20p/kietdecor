<?php
return [
  'v1' => array_merge($acc = [
      'driver' => 'pdo_mysql',
      'host'    => '172.17.0.1',
      'port'    => 3308,
      'charset' => 'utf8mb4',
      'username'=> 'root',
      'user'    => 'root',
      'password'=> 'root'],
      ['dbname'  => 'db_kietdecor']
  ),
 'v2' => array_merge($acc, ['dbname'  => 'db_kietdecor']),
 'vtest' => array_merge($acc, ['dbname'  => 'db_kietdecor']),
];