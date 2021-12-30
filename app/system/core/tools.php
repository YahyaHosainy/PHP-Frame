<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com>
 * @Date: 2020-11-13 
 * @Desc:  tools class
 */

class tools
{

  /**
   * Check is second paramater sub dir of first param or not
   *
   * @param string $parent
   * @param string $child
   * @param boolean $lt_or_eq  => if both is equal than return true
   * @return boolean
   */
  static function is_sub(string $parent,string $child,bool $lt_or_eq = false)
  {

    self::trim($parent,$child);

    $parent = self::url($parent)['path'] ;
    $child = self::url($child)['path'] ;

    if (
      $lt_or_eq
    ) {
      $cond = count($parent) > count($child) ;
    } else {
      $cond = count($parent) >= count($child) ;
    }

    if (
      $cond
    ) {
      return false ;
    } else {

      if (
        trim($parent[0]) === trim($child[0])
      ) {

        if (
          isset($child[1])
        ) {
         
          for ($i=1; $i < count($child); $i++) {
        
            if (
              isset($parent[$i]) and
              trim($parent[$i]) !== trim($child[$i])
            ) {
              return false ;
            } elseif (
              isset($parent[$i]) and
              !isset($parent[$i+1]) and
              !isset($child[$i+1]) and
              $lt_or_eq === false
            ) {
              return false ;
            } elseif (
              !isset($parent[$i])
            ) {
              return true ;
            }
  
          }

        } else {
          if (
            $lt_or_eq
          ) {
            return true ;
          }
          return false ;
        }

      } else {
        return false ;
      }
      
    }

  }

  /**
  * function url to handle url
  * 
  * @param null $route
  * 
  * @return array
  */
  static function url ($route = null) {
    
    $urls = [
      'path' => [] ,
      'query' => []
    ] ;
    
    if ($route === null) {
      $route = $_SERVER['REQUEST_URI'] ;
    }

    if (
      $route[strlen($route)-1] === '/'
    ) {
      $route = substr($route,0,strlen($route)-1);
    }

    $parse = parse_url($route) ;
    
    if (
      $parse and
      array_key_exists('query',$parse)
    ) {
      parse_str($parse['query'],$urls['query']);
    } else {
      $url['query'] = [] ;
    }
    
    $url = $parse['path'] ;

    if (
      isset($url[0]) and
      $url[0] === '/'
    ) {
      $i = 1 ;
    } else {
      $i = 0 ;
    }
    
    $url = explode('/',$url) ;
    
    for ($i=0; $i < count($url); $i++) { 
      $urls['path'][] = urldecode(trim($url[$i])) ; 
    }
    
    return $urls ;
  }

  /**
   * Trim function
   *
   * @param string $str
   * @return string
   * 
   */
  static function trim(& ...$var) {
      
    foreach ($var as $variable) {
      
      $variable = trim($variable) ;
      
    }
      
  }

  /**
   * for check is empty
   *
   * @param mixed ...$vars
   * @return boolean
   */
  static function empty (...$vars)
  {
    foreach ($vars as $var) {
      if (is_string($var) and trim($var) === "0") {
        // nothing
      } else {
        if (empty($var)) {
          return true ;
        }
      }
    }
    return false ;
  }

  /**
   * @param string $location
   * 
   * @return void
   */
  static function redirect(string $location = '/')
  {
    self::trim($location);
    $js_redirect =<<<TEXT
<script>
window.location.replace("{$location}");
</script>
TEXT;
    header('Location: '.$location);
    die($js_redirect);
  }

  static function println(string $str,bool $html = true)
  {
    if ($html) {
      echo $str . '<br />' ;
    } else {
      echo $str ."\n";
    }
  }

  static function random_string( int $length )
  {

    $length = $length ;
    $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
    $size = strlen($chars);
    
    $return = '';
    
    for ($i = 0; $i < $length; $i++) {
      $str = $chars[rand(0, $size - 1)];
      $return .= $str;
    }

    return $return;
  
  }

}