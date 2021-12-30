<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com>  
 * @Date: 2020-11-13
 * @Desc: init databases
 */

return [

  'mariaDB' => [
    'database_engine'  => 'mysql' ,
    'host'             => 'localhost' ,
    'database'         => 'test' ,
    'user_name'        => 'maria' ,
    'password'         => 'maria123',
    'port'             => '3306'
  ],
  
  'musqlDB' => [
    'database_engine'  => 'mysql' ,
    'host'             => '127.0.0.1' ,
    'database'         => 'blogpost' ,
    'user_name'        => 'yahya' ,
    'password'         => 'yahya123',
    'port'             => '3308'
  ]

] ;