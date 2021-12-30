<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com> 
 * @Date: 2020-11-14
 * @Desc: setup classes with config
 */

$database_config = require get\get_config('database.inc');

foreach ($database_config as $key => $arr) {
  $key  .= '___db'; 
  $$key = new SIMPLE_PDO(
    $arr['database_engine'],
    $arr['host'],
    $arr['database'],
    $arr['user_name'],
    $arr['password'],
    $arr['port']
  ) ;
} ;

/**
 * @param string $name
 * 
 * @return object
 */
function get_db(string $name)
{
  $name .= '___db';
  global $$name ;
  if (
    isset($$name)
  ) {
    return $$name ;
  } else {
    return null ;
  }
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$session_config = require get\get_config('session.inc');

foreach ($session_config as $key => $arr) {
  $key .= '___sess';
  $$key = new session(
    $arr['encrypt_key'],
    $arr['name'],
    $arr['location'],
    $arr['domain'],
    $arr['http_only'],
    $arr['https_only'],
    $arr['expire'],
    $arr['session_id_length'],
    get\get_core($arr['session_dir'])
  ) ;
} ;


/**
 * @param string $name
 * 
 * @return object
 */
function get_sess(string $name)
{
  $name .= '___sess';
  global $$name ;
  if (
    isset($$name)
  ) {
    return $$name ;
  } else {
    return null ;
  }
}

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$auth_config = require get\get_config('auth.inc');

foreach ($auth_config as $key => $arr) {
  $key .= '___auth' ;
  $$key = new auth(
    $arr['name'],
    $arr['encrypt_key'],
    $arr['expire']
  ) ;
} ;

/**
 * @param string $name
 * 
 * @return object
 */
function get_auth(string $name)
{
  $name .= '___auth';
  global $$name ;
  if (
    isset($$name)
  ) {
    return $$name ;
  } else {
    return null ;
  }
}