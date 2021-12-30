<?php
/** 
 * @Author: Yahya Hosainy <yahyayakta@gmail.com>
 * @Date: 2020-11-13
 * @Desc: authorization with session 
 */

class auth implements auth_interface {
  
  /**
   * sesison obj private variable
   *
   * @var sesison
   */
  private $SESSION = null ;

  /**
   * Hold Authorization errors
   *
   * @var array
   */
  public $errors = [] ;

  /**
   * First function for calle before any other in this class to create its session object
   *
   * @param string $name
   * @param string $key
   * @param integer $hours
   * @return void
   */
  public function create (
    string $name = 'Authorized',
    string $key = 'this is secret key',
    int $hours = 12
  ) {

    if ($this->SESSION === null) {

      $this->SESSION = new session (
        $key,
        $name,
        '/',
        '#',
        true,
        (security::is_secure() ? true : false),
        $hours,
        100,
        _core_.'/auth'
      );

      if ($this->SESSION->errors()) {
        $this->errors[] = $this->SESSION->errors() ;
        return false ;
      }

      if (
        !$this->SESSION->start()
      ) {
        $this->errors[] = $this->SESSION->warnings() ;
        return false ;
      }

      return true ;

    }
  
  }

  /**
   * @return void
   */
  public function end_all_sessions()
  {
    if ($this->SESSION) {
      
      if (
        !$this->SESSION->start() or
        !$this->SESSION->end_all_sessions()
      ) {
        $this->errors[] = $this->SESSION->warnings() ;
        return false ;
      } else {
        return true ;
      }
    
    }
  }

  /**
   * @return void
   */
  public function clear_junks()
  {
    if ($this->SESSION) {

      if (
        !$this->SESSION->start() or
        !$this->SESSION->garbage_collector()
      ) {
        $this->errors[] = $this->SESSION->warnings() ;
        return false ;
      } else {
        return true ;
      }
    
    }
  }

  /**
   * @param array $info
   * 
   * @return void
   */
  public function set(array $info = [])
  {
    if ($this->SESSION) {
      if (
        $this->SESSION->start() and
        $this->SESSION->var('@auth_arr',$info) and
        $this->SESSION->var('@auth',true)
      ) {
        return true ;
      } else {
        $this->errors[] = $this->SESSION->warnings() ;
        return false ;
      }
    }
    return false ;
  }

  /**
   * @return array
   */
  public function get_arr()
  {
    if ($this->SESSION) {

      if (
        !$this->SESSION->start() or
        !$this->SESSION->isset('@auth') or
        !$this->SESSION->isset('@auth_arr')
      ) {
        $this->errors[] = $this->SESSION->warnings() ;
        return false ;
      } else {
        return $this->SESSION->var('@auth_arr') ;
      }

    }
  }

  /**
   * @param mixed $key
   * @param null $value
   * 
   * @return bool|mixed
   */
  public function var($key,$value = null) {
    if ($this->SESSION) {
      if (
        $this->SESSION->start() and
        $this->SESSION->isset('@auth') and
        $this->SESSION->isset('@auth_arr')
      ) {
        $array = $this->get_arr() ;
        if ($value === null) {
          if (array_key_exists($key,$array)) {
            return $array[$key];
          } else {
            $this->errors[] = 'Vars => key not found!' ;
            return '' ;
          }
        } else {
          $array[$key] = $value ;
          if (
            $this->SESSION->var('@auth_arr',$array)
          ) {
            return true ;
          }
          $this->errors[] = $this->SESSION->warnings();
          return false ;
        }
      }
      return false ;
    }
  }

  /**
   * @param mixed $key
   * 
   * @return bool
   */
  public function delete_var($key)
  {
    if ($this->SESSION) {

      if (
        $this->SESSION->start() and
        $this->SESSION->isset('@auth') and
        $this->SESSION->isset('@auth_arr')
      ) {
        $array = $this->get_arr() ;
        if (array_key_exists($key,$array)) {
          unset($array[$key]) ;
        } else {
          return false ;
        }
        $this->SESSION->var('@auth_arr',$array) ;
        return true ;
      }
      return false ;
    }
  }

  /**
   * @param null $redirect_if_not
   * @param null $redirect_if_is
   * 
   * @return bool
   */
  public function is_auth ( $redirect_if_not = null , $redirect_if_is = null )
  {
    if ($this->SESSION) {

      if (
        $this->SESSION->start() and
        $this->SESSION->isset('@auth') and
        ((bool) $this->SESSION->var('@auth')) === true
      ) {
        if ($redirect_if_is) {
          tools::redirect($redirect_if_is);
        } else {
          return true ;
        }
      } else {
        if ($redirect_if_not) {
          tools::redirect($redirect_if_not);
        } else {
          return false ;
        }
      }
      
    }

  }

  /**
   * @param string $format
   * 
   * @return string
   */
  public function get_exp(string $format = 'text') {
    if ($this->SESSION) {
      if (
        $this->SESSION->start() and
        $time = $this->SESSION->get_info()['time']
      ) {

        $hours = null ;
        $days = null ;
        $minutes = (int) ( ( $time - time() ) / 60 ) ;
        if ( $minutes >= 60 ) {
          $hours = (int) ( $minutes / 60 ) ;
          $minutes = ( ($minutes % 60) == 0 ? null : ($minutes % 60) ) ;
          if ( $hours >= 24 ) {
          $days = (int) ( $hours / 24 ) ;
          $hours = ( ($hours % 24) == 0 ? null : ($hours % 24) ) ;
          }
        }
        if ( $format === 'array' ) {
          return [
            'minutes' => $minutes ,
            'hours' => $hours ,
            'days' => $days
          ] ;
        } else {
          $text = $minutes.' minutes' ;
          if ($hours) {
          if ($hours === 1) {
            $text = 'a hour and '.$text ;
          } else {
            $text = $hours.' hours and '.$text ;
          }
          }
          if ($days) {
          if ($days === 1) {
            if ($hours) {
            $text = 'a day , '.$text ;
            } else {
            $text = 'a day and '.$text ;
            }
          } else {
            if ($hours) {
            $text = $days.' days , '.$text ;
            } else {
            $text = $days.' days and '.$text ;
            }
          }
          }
          return $text ;
        }

      }
      return false ;
    }
  }

  /**
   * @return void
   */
  public function unauthorize()
  {
    if ($this->SESSION) {
      if (
        $this->SESSION->start() and
        $this->SESSION->end()
      ) {
        return true ;
      }
      return false ;
    }
  }

}