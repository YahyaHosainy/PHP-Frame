<?php

/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com> 
 * @Date: 2020-11-13 23:11:37 
 * @Desc: route class
 */

class route
{

  /**
   * main route function
   * 
   * @param string $route
   * @param callable $response
   * 
   * @return void
   */
  static function get_route(string $route, $response, array $datatype = [])
  {

    $is_method = false;

    if (
      is_array($response)
    ) {
      if (
        array_key_exists(0, $response) and
        array_key_exists(1, $response) and
        is_string($response[0]) and
        is_string($response[1]) and
        get\has_controller($response[0])
      ) {
        if (
          class_exists($response[0]) and
          in_array($response[1], get_class_methods($response[0]))
        ) {
          $obj = new $response[0];
          $method = $response[1];
          $is_method = true;
        } else {
          throw new Exception("route \"{$route}\" parameter 2, contrller method \"{$response[1]}\" not found!", 2);
        }
      } else {
        throw new Exception("route \"{$route}\" parameter 2, contrller not found!", 1);
      }
    } elseif (
      is_callable($response)
    ) {
    } else {
      throw new Exception("route \"{$route}\" parameter 2 shod be a function or an array of contrller name and method like ['controller','function']", 1);
    }

    $i_route = tools::url($route);
    $req_route = tools::url();

    if (
      $route === '$404'
    ) {
      http_response_code(404);
      goto end;
    }

    if (
      $route === '*'
    ) {
      goto end;
    }

    if (
      count($req_route['path']) === count($i_route['path']) or
      preg_match('/\/\[\*\]$/',$route)
    ) {

      $return = true;

      for ($i = 0; $i < count($i_route['path']); $i++) {
        if (
          !isset($req_route['path'][$i])
        ) {
          $return = false ;
          break ;
        }
        $i_route_i = $i_route['path'][$i];
        $req_route_i = $req_route['path'][$i];
        $cond = (isset($i_route_i[0]) and
          $i_route_i[0] === ':' and
          $req_route_i !== '');
        if (
          ($req_route_i !== $i_route_i) and
          $i_route_i !== '*' and
          $i_route_i !== '[*]' and
          !$cond
        ) {
          $return = false;
          break ;
        } elseif (
          $i_route_i === '[*]'
        ) {
          break ;
        } elseif ($cond) {
          if (
            isset($datatype[$i_route_i])
          ) {
            $type = trim($datatype[$i_route_i]);
          }
          if (
            !array_key_exists($i_route_i, $datatype)
          ) {
            $req_route['query'][$i_route_i] = $req_route_i;
          } elseif (
            $type[0] === '/'
          ) {
            if (
              preg_match($type, $req_route_i)
            ) {
              $req_route['query'][$i_route_i] = $req_route_i;
            } else {
              $return = false;
              break ;
            }
          } elseif (
            strtoupper($type) !== 'NUM' or
            strtoupper($type) !== 'NUMBER'
          ) {
            if (is_numeric($req_route_i)) {
              $req_route['query'][$i_route_i] = $req_route_i;
            } else {
              $return = false;
              break ;
            }
          }
        }
      }

      if ($return) {
        end:
        $req_route['query'] = array_merge($_GET, $req_route['query']);
        foreach ($_POST as $key => $value) {
          if (
            is_string($value)
          ) {
            if (
              trim($value) === ''
            ) {
              $_POST[$key] = null ;
            }
          }
        }
        foreach ($req_route['query'] as $key => $value) {
          if (
            is_string($value)
          ) {
            if (
              trim($value) === ''
            ) {
              $req_route['query'][$key] = null ;
            }
          }
        }
        if (
          strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'
        ) {
          if (
            !get_main_session()->isset('CSRF') or
            !array_key_exists('CSRF', $_POST) or
            get_main_session()->var('CSRF') !== $_POST['CSRF']
          ) {
            get_403();
            die();
            return;
          } else {
            $new = session::random_string(150) ;
            get_main_session()->var('CSRF',$new);
            el::$before = [
              'form' => el::input("@type=hidden @name=CSRF @value={$new}")
            ];
          }
        }
        $req_route = (object) $req_route ;
        $req_route->post = (object) $_POST ;
        $req_route->query = (object) $req_route->query ;
        $req_route->path = (object) $req_route->path ;
        $_POST = null ;
        $_GET = null ;
        if (
          $is_method
        ) {
          call_user_func_array([$obj, $method], [$req_route]);
        } else {
          call_user_func_array($response, [$req_route]);
        }
        die();
        return;
      }
    }
  }


  /**
   * get method
   * 
   * @param string $route
   * @param callable $response
   * 
   * @return void
   */
  public static function get(string $route, callable $response, array $type = [])
  {
    if (
      strtoupper($_SERVER['REQUEST_METHOD']) === 'GET'
    ) {
      self::get_route($route, $response, $type);
    }
  }


  public static function static(string $route,string $folder_in_views,array $type = [])
  {
    self::get($route,function()use($folder_in_views,$route){
      $route = str_replace('/[*]','',$route) ;
      $req = str_replace($route,'',$_SERVER['REQUEST_URI']) ;
      if (
        $get = get\get_view($folder_in_views.$req,true)
      ) {
        require_once $get ;
      } else {
        get_404();
      }
    },$type);
  }


  /**
   * match method
   *
   * @param array $match
   * @param string $route
   * @param callable $response
   * @return void
   */
  public static function match(array $match, string $route, callable $response, array $type = [])
  {
    foreach ($match as $mt) {
      if (
        is_string($mt)
      ) {
        if (
          strtoupper(trim($mt)) === strtoupper($_SERVER['REQUEST_METHOD'])
        ) {
          self::get_route($route, $response, $type);
          return true;
        }
      }
    }
  }


  /**
   * post method
   * 
   * @param string $route
   * @param callable $response
   * 
   * @return void
   */
  public static function post(string $route, callable $response, array $type = [])
  {
    if (
      strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'
    ) {
      self::get_route($route, $response, $type);
    }
  }


  /**
   * put method
   * 
   * @param string $route
   * @param callable $response
   * 
   * @return void
   */
  public static function put(string $route, callable $response, array $type = [])
  {
    if (
      strtoupper($_SERVER['REQUEST_METHOD']) === 'PUT'
    ) {
      self::get_route($route, $response, $type);
    }
  }


  /**
   * delete method
   * 
   * @param string $route
   * @param callable $response
   * 
   * @return void
   */
  public static function delete(string $route, callable $response, array $type)
  {
    if (
      strtoupper($_SERVER['REQUEST_METHOD']) === 'DELETE'
    ) {
      self::get_route($route, $response, $type);
    }
  }
}
