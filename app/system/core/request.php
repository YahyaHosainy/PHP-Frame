<?php

class Request /*implements request_interface*/
{
  function __construct(...$arr) {
    foreach ($arr as $each) {
      $this->$each['key'] = $each['value'] ;
    }
  }
}
