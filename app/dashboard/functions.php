<?php

function var_dumps (...$vars) {
  echo '<pre>';
  foreach ($vars as $var) {
    
    echo "\n----\n" ;
    var_dump($var) ;
    echo "\n----\n" ;
    
  }
  echo '</pre>';
}

function print_rs (...$vars) {
  echo '<pre>';
  foreach ($vars as $var) {
    
    echo "\n----\n" ;
    print_r($var) ;
    echo "\n----\n" ;
    
  }
  echo '</pre>';
}

