<?php

use function get\get_view;

class Main extends Controller
{

  public static function home()
  {
    require_once get_view('home');
  }

  public static function users($req)
  {
    var_dumps($req) ;
  }

}

