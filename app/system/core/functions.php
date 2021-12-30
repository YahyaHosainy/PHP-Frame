<?php

$sess = new session(
  'THis is an encrypt key',
  'COOKIE_TEST',
  '/',
  '#',
  true,
  security::is_secure(),
  24,
  60,
  get\get_core('sessions/')
) ;

if (
  $sess->errors()
) {
  foreach ($sess->errors() as $er) {
    throw new Exception("System functions => {$er}",5);
  }
}

$sess->start() ;

if (
  !$sess->isset('CSRF')
) {
  $sess->var('CSRF', session::random_string(150));
}

el::$before = [
  'form' => el::input("@type=hidden @name=CSRF @value={$sess->var('CSRF')}")
] ;

function get_main_session()
{
  global $sess ;
  return $sess ;
}

if (
  $sess->warnings()
) {
  foreach ($sess->warnings() as $er) {
    throw new Exception("System functions => {$er}",6);
  }
}

function redirect(string $to, $data = null)
{
  global $sess ;
  if ($data) {
    $sess->var('flash-data', [
      'data' => $data ,
      'times' => 0
    ]) ;
  }
  tools::redirect($to);
}

function setFlash($data)
{
  global $sess ;
  $sess->var('flash-data', [
    'data' => $data ,
    'times' => 0
  ]) ;
}

function getFlash()
{
  global $sess ;
  if ($sess->isset('flash-data')) {
    $return = $sess->var('flash-data')['data'] ;
    $sess->delete('flash-data') ;
    return $return ;
  } else {
    return null ;
  }
}

if (
  $sess->isset('flash-data') and
  $sess->var('flash-data')['times'] > 0
) {
  $sess->delete('flash-data') ;
} elseif (
  $sess->isset('flash-data')
) {
  $sess->var('flash-data', [
    'data' => $sess->var('flash-data')['data'] ,
    'times' => 1
  ]);
}

function get_404() {
  http_response_code(404);
  echo
  el::h1('@style=text-align:center; margin-top:30px',
    el::span(['style'=>'color:gray'],'Ops!').
    ' Page not found!'
  ).
  el::hr() ;
}

function get_403() {
  http_response_code(403);
  echo
  el::h1('@style=text-align:center; margin-top:30px',
    el::span(['style'=>'color:gray'],'Ops!').
    ' Bad request!'
  ).
  el::hr() ;
}
