<?php
/**
 * session class
 */

class session implements session_interface {
  
  /**
   * Session name variable
   *
   * @var string
   */
  private $name ;

  /**
   * Session location default = /
   *
   * @var string
   */
  private $location ;

  /**
   * Session domain default = your website domain
   *
   * @var string
   */
  private $domain ;

  /**
   * Http only defualt = true
   *
   * @var string
   */
  private $http_only ;

  /**
   * Https only default = false
   *
   * @var string
   */
  private $https_only ;

  /**
   * Session expire date defualt set 24 hour latter
   *
   * @var int
   */
  private $expire ;

  /**
   * Session id
   *
   * @var string
   */
  private $id ;

  /**
   * Session id length < 101 and > 19
   *
   * @var string
   */
  private $id_length ;
  
  /**
   * Session directory
   *
   * @var string
   */
  private $dir ;
  
  /**
   * Encrypt key
   *
   * @var string
   */
  private $encrypt_key ;
  
  /**
   * If once session->start() called this will turn on
   *
   * @var bool
   */
  private $called ;
  
  /**
   * Error handler
   *
   * @var array
   */
  private $errors ;
  
  /**
   * Warning handler
   *
   * @var array
   */
  private $warnings ;

  /**
   * Array of session
   *
   * @var array
   */
  private $array ;


  /*
  +--------------------------+   ||
  |  Methods                 |   ||
  +--------------------------+   \/
  */


  /**
   * Get errors
   *
   * @return array
   */
  public function errors()
  {
    return $this->errors ;
  }

  
  /**
   * Get warnings
   *
   * @return array
   */
  public function warnings()
  {
    return $this->warnings ;
  }


  /**
   * To fast check for errors
   * 
   * @return boolean
   */
  public function all_right()
  {
    // echo $this->location.'<hr>' ;
    // echo $_SERVER['REQUEST_URI'] ;
    if (
      empty($this->errors) and
      trim($this->domain) === $_SERVER['SERVER_NAME'] and
      tools::is_sub(trim($this->location),$_SERVER['REQUEST_URI'],true)
    ) {
      return true ;
    }
    return false ;
  }


  /**
   * Random string generator
   *
   * @param integer $length
   * @return string
   */
  public static function random_string( int $length )
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


  /**
   * Set permission on session file to 0750
   *
   * @param string $error_on
   * @return bool
   */
  public function set_per_off (string $error_on = 'SESSION')
  {
    if (
      !@chmod($this->dir, 0750)
    ) {
      $this->warnings[] = "{$error_on} => can't set permission on session file !" ;
      return true ;
    } else {
      return false ;
    }
  }


  /**
   * Set permission on session file to 0600
   *
   * @param string $error_on
   * @return bool
   */
  public function set_per_on (string $error_on = 'SESSION')
  {
    if (
      !@chmod($this->dir.$this->id, 0600)
    ) {
      $this->warnings[] = "{$error_on} => can't set permission on session file !" ;
      return true ;
    } else {
      return false ;
    }
  }


  /**
   * function for save session variables to session file
   * 
   * @param string
   * @param array
   * @return bool
   */
  private function ATJ(
    string $name ,
    array $array ,
    string $error_on = 'SESSION'
  ) {

    $array = json_encode($array);

    $put = @file_put_contents(
      $this->dir.$name,
      security::encrypt(
        $array,
        $this->encrypt_key
      )
    );
    
    if ($put === false) {
      $this->warnings[] = "{$error_on} => cant put content on session file !" ;
      return false;
    }

    $this->set_per_on($error_on);

    return true ;

  }
  

  /**
   * function for get session file
   * 
   * @param string $name
   * @param string $error_on = 'SESSION'
   * @return array|bool
   */
  private function JTA (
    string $name ,
    $error_on = 'SESSION'
  ) {

    $json = @file_get_contents($this->dir.$name);

    if (
      $json === false
    ) {
      $this->warnings[] = "{$error_on} => can't get content of session file !" ;
      return false;
    }

    $json = security::decrypt(
      $json,
      $this->encrypt_key
    );

    $json = @json_decode($json, true);

    if (
      $json === false
    ) {
      $this->warnings[] = "{$error_on} => can't decode content of session file !" ;
      return false;
    }

    return $json;
    
  }


  /**
   * For ip location check
   *
   * @param string $ip1
   * @param string $ip2
   * @return bool
   */
  private function same_ip(string $ip1,string $ip2)
  {
    return true ;
  }


  /**
   * Construct
   *
   * @param string $encrypt_key
   * @param string $name
   * @param string $location
   * @param string $domain
   * @param boolean $http_only
   * @param boolean $https_only
   * @param integer $expire
   * @param integer $id_length
   * @param string $dir
   * 
   */
  function __construct (
    string $encrypt_key ,
    string $name = 'SESSION',
    string $location = '/',
    string $domain = '#',
    bool $http_only = true,
    bool $https_only = false ,
    int $expire = 24 ,
    int $id_length = 100 ,
    string $dir = 'sessions/'
  ) {
    
    if (
      !empty($encrypt_key)
    ) {

      tools::trim(
        $name,
        $location,
        $domain,
        $dir
      );

      if (
        empty($location)
      ) {
        $this->errors[] = '__construct => $location is empty' ;
      } else {
        $this->location = $location ;
      }

      if (
        $id_length < 20
      ) {
        $this->errors[] = '__construct => $id_length length is less than 20 char' ;
      } elseif (
        $id_length > 100
      ) {
        $this->errors[] = '__construct => $id_length length is more than 100 char' ;
      } else {
        $this->id_length = $id_length ;
      }

      if (
        empty($name)
      ) {
        $this->errors[] = '__construct => $name is empty' ;
      } elseif (
        strlen($name) > 100
      ) {
        $this->errors[] = '__construct => $name length is more than 100 char' ;
      } else {
        $this->name = $name ;
      }

      if (
        !file_exists($dir)
      ) {
        $this->errors[] = '__construct => $dir is not found!' ;
      } elseif (
        !is_writable($dir)
      ) {
        $this->errors[] = '__construct => $dir is not writable!' ;
      } else {
        if ( $dir[strlen($dir)-1] !== '/' ) {
          $dir .= '/' ;
        }
        $this->dir = $dir ;
      }

      $self = false ;

      if (
        '#' === $domain
      ) {
        $domain = $_SERVER['SERVER_NAME'] ;
        $self = true ;
      }

      if (
        !filter_var($domain, FILTER_VALIDATE_DOMAIN)
      ) {
        $this->errors[] = '__construct => $domain is incorrect!' ;
      } else {
        $this->domain = $domain ;
      }

      $this->http_only = $http_only ;

      if (
        $https_only and
        !security::is_secure() and
        (
          '#' === $domain or
          $self
        )
      ) {
        $this->errors[] = '__construct => $https_only is set to ture but your domain is not https !' ;
      } else {
        $this->https_only = $https_only ;
      }

      if (
        $expire < 1
      ) {
        $this->errors[] = '__construct => $expire is less than 1 hour !' ;
      } else {
        $this->expire = $expire ;
      }

      if (
        strlen($encrypt_key) < 8
      ) {
        $this->errors[] = '__construct => $encrypt_key is less than 8 char !' ;
      } elseif (
        strlen($encrypt_key) > 50
      ) {
        $this->errors[] = '__construct => $encrypt_key is more than 50 char !' ;
      } else {
        $this->encrypt_key = $encrypt_key ;
      }

      
    } else {
      $this->errors[] = '__construct => $encrypt_key is empty' ;
    }
    
  }


  /**
   * Private function for set cookies
   *
   * @param string $error_on
   * @param array $array
   * @return bool
   */
  private function set_cookie (
    string $error_on = 'start',
    array $array = []
  ) {

    $this->id = $this->random_string(
      $this->id_length
    );
   
    while (
      file_exists(
        $this->dir.$this->id
      )
    ) {
      $this->id = $this->random_string($this->id_length);
    }
   
    $tmp = array();
    $time = (int) (time() + (3600 * $this->expire));
    $browser_time = (int) ($time + (3600 * $this->expire)) ;
    
    $set_cookie = setcookie(
      $this->name,
      $this->id,
      $browser_time,
      $this->location,
      $this->domain,
      $this->https_only,
      $this->http_only
    );
    
    if (
      $set_cookie
    ) {
      
      $tmp['session_name']  = $this->name;
      $tmp['time']          = $time;
      $tmp['ip']            = $_SERVER['REMOTE_ADDR'];
      $tmp['browser']       = $_SERVER['HTTP_USER_AGENT'];
      $tmp['vars']          = $array ;
      
      if (
        !$this->ATJ(
          $this->id,
          $tmp,
          $error_on
        )
      ) {
        return false ;
      };

      $this->array = $tmp ;

      return true ;
      
    } else {
    
      $this->warnings[] = $error_on.' => function setcookie returns false!' ;
      return false ;
    
    }
    
  }


  /**
   * Start session function
   *
   * @param array $array
   * @return bool
   */
  public function start(array $array = []){

    if (
      !$this->called and
      $this->all_right()
    ) {

      $this->called = true ;

      if (
        !array_key_exists(
          $this->name,
          $_COOKIE
        )
      ) {
      
        if (
          $this->set_cookie('start',$array)
        ) {
          return true ;
        };

        return false ;
      
      } elseif (
        file_exists($this->dir.$_COOKIE[$this->name])
      ) {
     
        $id = $_COOKIE[$this->name] ;

        $tmp = $this->JTA($id,'start');
        
        if ($tmp !== false) {
          
          if (
            empty($tmp) or
            $tmp['session_name'] !== $this->name or
            strlen($id) != $this->id_length or
            $tmp['time'] < time() or
            $tmp['browser'] != $_SERVER['HTTP_USER_AGENT'] or
            !$this->same_ip( $_SERVER['REMOTE_ADDR'] , $tmp['ip'] )
          ) {
      
            if (
              $this->set_cookie('start',$array)
            ) {
              return true ;
            };

            return false ;
      
          } else {
           
            $this->array = $tmp ;

            $this->id = $id ;

            return true ;
          
          }
        
        } else {

          if (
            $this->set_cookie('start',$array)
          ) {
            return true ;
          };
        
          return false ;

        }
     
      } else {
        
        if (
          $this->set_cookie('start',$array)
        ) {
          return true ;
        };

        return false ;
        
      }

    } elseif (
      !$this->all_right()
    ) {
      return false ;
    }

    return true ;

  }


  /**
   * Get all seted variables
   *
   * @return array
   */
  public function get_array()
  {
    if (
      $this->called and
      $this->all_right()
    ) {
      return $this->array['vars'] ;
    }
  }


  /**
   * Get session information like time or session id with session variables
   *
   * @return array
   */
  public function get_info()
  {
    if (
      $this->called and
      $this->all_right()
    ) {
      return $this->array ;
    }
  }


  /**
   * For set variables with an array
   *
   * @param array $array
   * @param boolean $clear  clear existing data or not
   * @return bool
   */
  public function set_array(
    array $array,
    bool $clear = false
  ){
    if (
      $this->called and
      $this->all_right()
    ) {
      
      $this_array = $this->array ;

      if ($clear) {
        $this_array['vars'] = [] ;
      }

      foreach ($array as $key => $value) {
        $this_array['vars'][$key] = $value ;
      }

      if (
        !$this->ATJ(
          $this->id,
          $this_array,
          'set_array'
        )
      ) {
        return false ;
      };

      $this->array = $this_array ;

      return true ;
    }
  }


  /**
   * Get session private properties
   * 
   * @return array
   */
  public function get_object_info () {

    if (
      $this->called and
      $this->all_right()
    ) {

      return [
        'name' => $this->name ,
        'location' => $this->location ,
        'domain' => $this->domain ,
        'http_only' => $this->http_only ,
        'https_only' => $this->https_only ,
        'expire' => $this->expire ,
        'id' => $this->id ,
        'id_length' => $this->id_length ,
        'dir' => $this->dir ,
        'encrypt_key' => $this->encrypt_key
      ] ;

    }
  
  }
  
  
  /**
   * Set or get variables .
   *
   * @param mixed $key
   * @param mixed $value
   * @return bool
   */
  public function var(
    $key,
    $value = null
  ){
    
    if (
      $this->called and
      $this->all_right()
    ) {
      
      if ($value === null) {

        if (
          array_key_exists(
            $key,
            $this->array['vars']
          )
        ) {
        
          return $this->array['vars'][$key]; 
        
        } else {

          return null ;
        
        }
     
      } elseif (
        array_key_exists(
          $key,
          $this->array['vars']
        ) and
        $this->array['vars'][$key] === $value
      ) {
        
        return true ;

      } else {
       
        $tmp = $this->JTA($this->id,'var') ;

        if ($tmp !== false) {
          
          $tmp['vars'][$key] = $value;

          if (
            !$this->ATJ(
              $this->id,
              $tmp,
              'var'
            )
          ) {
            return false ;
          }

          $this->array = $tmp ;

          return true ;
        
        } else {
          
          return false ;
        
        }
      
      }
    
    }

    return false ;
    
  }
  
  
  /**
   * Check for a variable
   *
   * @param mixed $key
   * @return bool
   */
  public function isset( $key ){

    if (
      $this->called and
      $this->all_right()
    ) {

      if (
        array_key_exists(
          $key,
          $this->array['vars']
        )
      ) {
        
        return true;
      
      } else {
      
        return false;
      
      }

    }
    
  }
  
  
  /**
   * Delete a variable
   *
   * @param mixed $key
   * @return bool
   */
  public function delete( $key ){
    
    if (
      $this->called and
      $this->all_right()
    ) {

      $tmp = $this->JTA($this->id) ;
    
      if ($tmp !== false) {

        unset($tmp['vars'][$key]);

        if (
          $this->ATJ(
            $this->id,
            $tmp
          ) === false
        ) {

          return false ;
        
        }

        $this->array = $tmp ;

        return true ;

      } else {
        return false ;
      }

    }
  
  }
  
  
  /**
   * End all existting sessions for all browsers
   *
   * @return bool
   */
  public function end_all_sessions(){

    if (
      $this->called and
      $this->all_right()
    ) {

      foreach (
        scandir($this->dir) as $temp
      ) {
        
        if (
          strlen($temp) == 100 and
          empty(
            pathinfo(
              $this->dir . $temp,
              PATHINFO_EXTENSION
            )
          )
        ) {

          $file = $this->JTA($temp) ;
  
          if (
            $file['session_name'] === $this->name
          ) {
            
            if (
              !unlink($this->dir . $file)
            ) {

              $all_right = false ;
              $this->warnings[] = 'end_all_sessions => unlink() functions returns false may be a permission error for PHP' ;
            
            }
  
          }
        
        }
      
      }
      
      $this->is_called = false ;
      $this->start();

      return true ;

    }
    
  }
  
  
  /**
   * Clear sessions that was expired
   *
   * @param string $error_on for error name
   * @return bool
   */
  private function clear_junk(
    string $error_on = 'clear_junk'
  ){
    
    if (
      $this->called and
      $this->all_right()
    ) {
      
      $all_right = true ;

      foreach (
        scandir($this->dir) as $temp
      ) {

        if (
          strlen($temp) == 100 and
          empty(
            pathinfo(
              $this->dir . $temp,
              PATHINFO_EXTENSION
            )
          )
        ) {
          
          $file = $this->JTA($temp) ;
  
          if (
            $file['session_name'] === $this->name and
            $file['time'] < time()
          ) {
            
            if (
              !unlink($this->dir . $file)
            ) {

              $all_right = false ;
              $this->warnings[] = $error_on.' => unlink() functions returns false may be a permission error for PHP' ;
            
            }
  
          }
  
        }
  
      }

      return $all_right ;

    }

  }
  
  
  /**
   * Some as clear_junk but for user use
   *
   * @return bool
   */
  public function garbage_collector(){
    
    if (
      $this->called and
      $this->all_right()
    ) {
      
      if (
        count(scandir($this->dir)) > 1000
      ) {

        if (
          $this->clear_junk('garbage_collector')
        ) {
          return true ;
        }
  
        return false ;
      
      }

    }

  }
  
  
  /**
   * End just cuurnt sesison
   *
   * @return bool
   */
  public function end(){
    
    if (
      $this->called and
      $this->all_right()
    ) {
      
      if (
        unlink (
          $this->dir.$this->id
        ) === false
      ) {
        $this->warnings[] = 'end => cant delete sesison file' ;
        return false ;
      }

      $this->is_called = false ;
      setcookie(
        $this->name,
        "",
        time() - ( 3600 * 24 * 30 )
      );

      return true ;

    }

  }

  
}