<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com>  
 * @Date: 2020-11-13
 * @Desc: init Session
 */

return [

  'session' => [
    'encrypt_key'            => 'THisIs_an-incryptKey7&6#2020',
    'name'                   => 'SESSION',
    'location'               => '/',
    'domain'                 => '#',
    'http_only'              => true,
    'https_only'             => security::is_secure(),
    'expire'                 => 24,
    'session_id_length'      => 100,
    'session_dir'            => 'sessions/'
  ] ,

];

