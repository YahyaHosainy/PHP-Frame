<?php
# date 31 october 2020
namespace frame {
  use \PDO;
  use \PDOException;
  // use \frame\tools;

  class security
  {
    // static $method = 'idea-cbc';
    // static $vi = '@EZHvt$1';
    static function encrypt($data, $key)
    {
      // return openssl_encrypt($data, self::$method, $key, 0, self::$vi);
      return $data ;
    }
    static function decrypt($data, $key)
    {
      // return openssl_decrypt($data, self::$method, $key, 0, self::$vi);
      return $data ;
    }
    static function echo_s ($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      echo $data;
    }
    static function s_echo_s ($data,&$var = null) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      if ($var) {
        $var = $data ;
        return true ;
      } else {
        return $data;
      }
    }
  } // end of class security

  class database
  {
    private $db;
    public $error = null;
    private $continue = true;

    function __construct($database_engine, $host, $database, $user_name, $password)
    {
      if (!tools::are_empty([$database_engine, $host, $database, $user_name, $password])) {
        try {
          $this->db = new PDO("{$database_engine}:host={$host};dbname={$database}", $user_name, $password);
          $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $error) {
          $this->error = "Connection failed: " . $error->getMessage();
          $this->continue = false;
          return false;
        }
        return true;
      } else {
        $this->error = 'Connection failed: same of parameter is empty';
        $this->continue = false;
        return false;
      }
    }

    /**
     * Function to set values in database
     * 
     * @param string $statement
     * EX: INSERT INTO `table_name` (`name`,`id`) VALUSE (?,?) or .... VALUSE (:name,:id)
     * @param array $values = [[]]
     * EX: array(
     *  [ 'Ahmad' , 0 ] ,
     *  [ 'Mahmod , 1 ] ,
     *  [ 'Akbar' , 3 ]
     * )
     * or 
     * array(
     *  [ 'name'=>'Ahmad' , 'id'=>0 ] ,
     *  .....
     * )
     * @param bool $last_insert_id
     * @return bool|string|int
     */
    public function set(string $statement, array $values = [[]], bool $is_last_insert_id = false)
    {
      if ($this->continue) {
        $last_insert_id = null;
        if (!tools::are_empty([$statement])) {
          try {
            $this->db->beginTransaction();
            $prepare = $this->db->prepare($statement);
            foreach ($values as $value) {
              $prepare->execute($value);
            }
            $last_insert_id = $this->db->lastInsertId();
            $this->db->commit();
          } catch (PDOException $th) {
            $this->db->rollBack();
            $this->error = 'Error: ' . $th->getMessage();
            return false ;
          }
          if ($is_last_insert_id) {
            return $last_insert_id;
          } else {
            return true;
          }
        } else {
          $this->error = 'Error: param 1 is null in $database->set()';
          return false;
        }
      } else {
        return false ;
      }
    }

    /**
     * Function to gat values form database
     * 
     * @param string $statement
     * EX: SELECT * FROM `table_name` WHERE `id` = ? or .... `id` = :id
     * @param array $values = []
     * EX: array(
     *  0
     * )
     * or 
     * array(
     *  'id'=>0
     * )
     * @return array|bool
     */
    public function get(string $statement, array $values = [])
    {
      if ($this->continue) {
        if (!tools::are_empty([$statement])) {
          try {
            $prepare = $this->db->prepare($statement);
            $prepare->execute($values);
            $get = $prepare->fetchAll();
            return $get;
          } catch (PDOException $th) {
            $this->error = 'Error: ' . $th->getMessage();
            return false ;
          }
        } else {
          $this->error = 'Empty parameter 1';
          return false;
        }
      }
    }
  } // end of class database

  class session
  {
    private $session_name = 'Session';
    private $encrypt_key = 'This is your encryption key';
    private $temp_address = __DIR__ .'/temp/' ;
    private $session_id_length = 0 ;
    private $session_id = null;
    private $variables_array = [] ;
    private $exp = 0 ;
    private $is_called = false ;

    function __construct(string $name,string $key,int $length = 100,int $exp = 12,string $address = NULL)
    {
      $this->session_name = $name ;
      $this->encrypt_key = $key ;
      if ($address !== NULL) {
        $this->temp_address = $address ;
      }
      $this->session_id_length = $length ;
      $this->exp = $exp ;
      $this->start();
    }

    public function end_all_sessions()
    {
      foreach (scandir($this->temp_address) as $temp) {
        if (
          $temp != '.' and
          $temp != '..' and
          strlen($temp) == 100 and
          empty(pathinfo($this->temp_address . $temp,PATHINFO_EXTENSION))
        ) {
          if (
            unlink($this->temp_address . $temp)
          ) {
            return true ;
          } else {
            $this->die('unlink() function not work !!') ;
          }
        }
      }
      $this->start();
    }

    public function end_expired()
    {
      foreach (scandir($this->temp_address) as $temp) {
        if (
          $temp != '.' and
          $temp != '..' and
          strlen($temp) == 100 and
          empty(pathinfo($this->temp_address . $temp,PATHINFO_EXTENSION))
        ) {
          return true ;
        }
      }
      $this->start();
    }

    public function get_array () {
      return $this->variables_array ;
    }
    
    public function get_session_id()
    {
      if ($this->session_id === null and array_key_exists($this->session_name, $_COOKIE)) {
        $this->session_id = $_COOKIE[$this->session_name];
      }
      return $this->session_id;
    }

    private static function die($backtrace,$msg)
    {
      if (is_string($backtrace)) {
        die(
          '<p style="padding:10px;background-color:red;border-radius:5px;color:$000;font-size:18px">
            <b>
              <code>
                '.$backtrace.'
              </code>
            </b>
          </p>'
        );
      } else {
        die(
          '<p style="padding:10px;background-color:red;border-radius:5px;color:$000;font-size:18px">
            <b>
              <code>
                '.$msg.' , in '.$backtrace[0]['file'].' at line '.$backtrace[0]['line'].'
              </code>
            </b>
          </p>'
        );
      }
    }

    private function condition()
    {
      $session_id = $this->get_session_id() ;
      if (
        ! tools::is_empty( $session_id ) and
        file_exists( $this->temp_address.$session_id ) and 
        is_string( $session_id )
      ) {
        return true ;
      } else {
        return false ;
      }
    }

    static function random_string( $length )
    {
      $length = (int) $length ;
      $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
      $size = strlen($chars);
      $return = '';
      for ($i = 0; $i < $length; $i++) {
        $str = $chars[rand(0, $size - 1)];
        $return .= $str;
      }
      return $return;
    }
    
    private function ATJ($address, $array)
    {
      $return = true;
      $arr_to_json = json_encode($array);
      $put = @file_put_contents($address, security::encrypt($arr_to_json, $this->encrypt_key));
      if ($put === false) {
        $return = false;
      }
      chmod($address, 0600);
      return $return;
    }
    
    private function JTA($address)
    {
      if (file_exists($address)) {
        $json = file_get_contents($address);
        chmod($address, 0600);
        $json = security::decrypt($json, $this->encrypt_key);
        $json = json_decode($json, true);
        return $json;
      } else {
        return false;
      }
    }

    public function get_json()
    {
      if (
        $this->condition()
      ) {
        $json = file_get_contents($this->temp_address.$this->session_id);
        $json = security::decrypt($json, $this->encrypt_key);
        return $json;
      } else {
        $this->die(debug_backtrace(),'session::get_json() error : session must start before get the json file') ;
      }
    }

    public function get_array_info()
    {
      if (
        $this->condition()
      ) {
        $array = $this->JTA($this->temp_address.$this->session_id);
        return array_splice($array,0,3) ;
      } else {
        $this->die(debug_backtrace(),'session::get_time() error : session must start before get the json file') ;
      }
    }

    public function same_ip ( string $ip1 , string $ip2 ) {
      $ip1 = explode(".",$ip1) ;
      $ip2 = explode(".",$ip2) ;
      if ( $ip1 === false or $ip2 === false ) return false ;
      if (
        isset($ip1[3]) and
        isset($ip2[3])
      ) {
        if (
          $ip1[0] !== $ip2[0] and
          $ip1[1] !== $ip2[1] and
          $ip1[2] !== $ip2[2]
        ) {
          return false ;
        }
        $res = ( (int) $ip1[3] ) - ( (int) $ip2[3] ) ;
        ( $res < 0 ) ? $res = $res * (-1) : null ;
        if (
          $res > 100
        ) {
          return false ;
        } else {
          return true ;
        }
      }
      return false ;
    }
    
    private function ok( string $location = '/')
    {
      $rand = self::random_string($this->session_id_length);
      while (file_exists($this->temp_address.$rand)) {
        $rand = self::random_string($this->session_id_length);
      }
      $this->session_id = $rand;
      $tmp = array();
      $time = time() + (3600 * $this->exp);
      $browser_time = $time + (3600 * $this->exp * 24 * 30) ;
      setcookie($this->session_name, $rand, $browser_time, $location, $_SERVER['SERVER_NAME'], false, true);
      $tmp['time'] = $time;
      $tmp['ip'] = $_SERVER['REMOTE_ADDR'];
      $tmp['browser'] = $_SERVER['HTTP_USER_AGENT'];
      $tmp['vars'] = [] ;
      if (
        $this->ATJ($this->temp_address.$rand, $tmp) === false
      ) {
        $this->die(debug_backtrace(),'session::start() error : Cant create file in '.$this->temp_address.' dir ') ;
      };
      $this->variables_array = [] ;
      $tmp = null;
      $time = null;
      $rand = null;
      unset($tmp);
      unset($time);
      unset($rand);
    }
    
    public function isset(string $key)
    {
      if (is_array($this->variables_array) and array_key_exists($key, $this->variables_array)) {
        return true;
      } else {
        return false;
      }
    }

    public function delete(string $key)
    {
      $tmp = $this->JTA($this->temp_address.$this->session_id) ;
      if ($tmp !== false) {
        unset($tmp['vars'][$key]);
        if ($this->ATJ($this->temp_address.$this->session_id, $tmp) === false) {
          $backtrace = debug_backtrace(); 
          $this->die('session::var() error : cant save change in session file may it is a permession error') ;    
        }
        $this->variables_array = $tmp['vars'];
        $tmp = null;
        unset($tmp);
        return true ;
      } else {
        $this->die(debug_backtrace(),'session::delete() error : cant\' get content of session file') ;
      }
    }
    
    public function var($key, $value = null)
    {
      if (
        $this->condition()
      ) {
        if ($value === null) {
          if (array_key_exists($key, $this->variables_array)) {
            return $this->variables_array[$key]; 
          } else {
            $tmp = $this->JTA($this->temp_address.$this->session_id);
            if ($tmp !== false) {
              if (array_key_exists($key,$tmp['vars'])) {
                return $tmp['vars'][$key] ;
              } else {
                $this->die(debug_backtrace(),'session::var() error : Undefined variable '. $key) ;    
              }
            } else {
              $this->die(debug_backtrace(),'session::var() error : cant\' get content of session file') ;
            }
          }
        } else {
          $tmp = $this->JTA($this->temp_address.$this->session_id) ;
          if ($tmp !== false) {
            $tmp['vars'][$key] = $value;
            if ($this->ATJ($this->temp_address.$this->session_id, $tmp) === false) {
              $backtrace = debug_backtrace(); 
              $this->die('session::var() error : cant save change in session file may it is a permession error') ;    
            }
            $this->variables_array = $tmp['vars'];
            $tmp = null;
            unset($tmp);
            return true ;
          } else {
            $this->die(debug_backtrace(),'session::var() error : cant\' get content of session file') ;
          }
        }
      } else {
        $this->die(debug_backtrace(),'session::end() error : session must start before get or set a session variable') ;
      }
    }
    
    public function start(string $location = '/')
    {
      if (
        !$this->is_called
      ) {
        $this->is_called = true ;
        if (!array_key_exists($this->session_name, $_COOKIE)) {
          $this->ok($location);
        } else {
          $tmp = $this->JTA($this->temp_address.$_COOKIE[$this->session_name]);
          if ($tmp !== false) {
            if (
              empty($tmp) or
              strlen($_COOKIE[$this->session_name]) != $this->session_id_length or
              $tmp['time'] < time() or
              $tmp['browser'] != $_SERVER['HTTP_USER_AGENT'] or
              !$this->same_ip( $_SERVER['REMOTE_ADDR'] , $tmp['ip'] )
            ) {
              $this->ok($location);
            } else {
              $this->variables_array = $tmp['vars'] ;
              $this->session_id = $_COOKIE[$this->session_name] ;
            }
          } else {
            $this->ok($location);
          }
        }
        $tmp = null;
        unset($tmp);
      }
    }

    public function end()
    {
      if (
        $this->condition()
      ) {
        if ( unlink( $this->temp_address.$this->session_id ) === false ) {
          $backtrace = debug_backtrace();
          $this->die(debug_backtrace(),'session::end() error : can\'t delete the session file may it is a permission error') ;
        }
        $this->is_called = false ;
      } else {
        $this->die(debug_backtrace(),'session::end() error : session must start before end the session') ;
      }
    }
  } // end of class session

  class image_handler
  {
    private $valid_extensions = ['png', 'pns', 'jpeg', 'jpg', 'jpe', 'bmp', 'gif', 'svg', 'wbmp', 'webp'];
    private $data_array = [];
    public $image_permission_key_in_data_array;
    public $admin = false;
    private $images_dir;

    function __construct(array $data, string $images_dir, $image_permission_key_in_data_array, bool $admin = false)
    {
      $this->data_array = $data;
      $this->images_dir = $images_dir;
      $this->image_permission_key_in_data_array = $image_permission_key_in_data_array;
      $this->admin = $admin;
    }

    public function show($target_image_name, $image_width = 'full')
    {
      $image_name = trim(basename($target_image_name));
      $image_permission = $this->data_array[$image_name][$this->image_permission_key_in_data_array];
      if ((!empty($image_permission)) and ($image_permission == 1 or $image_permission == true or $this->admin)) {
        $extension = strtolower(pathinfo($target_image_name, PATHINFO_EXTENSION));
        $backtrace = debug_backtrace();
        if (
          array_search($extension, $this->valid_extensions) !== false
        ) {
          if ($extension == 'svg') {
            header("Content-Type: image/svg+xml");
            echo (file_get_contents($this->images_dir . $image_name));
          } else {
            header("Content-Type: image/{$extension}");
            list($width, $height) = getimagesize($this->images_dir . $image_name);
            if ($image_width == 'full') {
              $newwidth = $width;
              $newheight = $height;
            } else {
              if ($image_width > $width) {
                $image_width = $width;
              }
              $AR = (float) $width / (float) $height;
              $newwidth = $image_width;
              $newheight = (float) $image_width / $AR;
            }
            $destination = imagecreatetruecolor($newwidth, $newheight);
            if ($extension == 'jpeg' or $extension == 'jpg' or $extension == 'jpe') {
              $source = imagecreatefromjpeg($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagejpeg($destination);
            } elseif ($extension == 'png' or $extension == 'pns') {
              $source = imagecreatefrompng($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagepng($destination);
            } elseif ($extension == 'gif') {
              $source = imagecreatefromgif($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagegif($destination);
            } elseif ($extension == 'bmp') {
              $source = imagecreatefrombmp($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagebmp($destination);
            } elseif ($extension == 'webp') {
              $source = imagecreatefromwebp($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagewebp($destination);
            } elseif ($extension == 'wbmp') {
              $source = imagecreatefromwbmp($this->images_dir . $image_name);
              imagecopyresized($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
              imagewbmp($destination);
            }
          }
        } else {
          die('<p style="padding:10px;background-color:red;border-radius:5px;color:$000;font-size:18px"><b><code>frame\image_handler error : image not found in ' . $backtrace[0]['file'] . ' at line ' . $backtrace[0]['line'] . '</code></b></p>');
        }
      } else {
        header('HTTP/1.1 404 ERROR');
      }
    }
  } // end of class image_handler

  class route
  {

    static function url ($route = null) {
      $urls = [
        'path' => [] ,
        'query' => []
      ] ;
      if ($route) {
        $parse = parse_url($route) ;
      } else {
        $parse = parse_url($_SERVER['REQUEST_URI']) ;
      }
      if (
        $parse and
        array_key_exists('query',$parse)
      ) {
        parse_str($parse['query'],$urls['query']);
      } else {
        $url['query'] = [] ;
      }
      $url = $parse['path'] ;
      if ($url[0] === '/') {
        $i = 1 ;
      } else {
        $i = 0 ;
      }
      $url = explode('/',$url) ;
      for (; $i < count($url); $i++) { 
        $urls['path'][] = urldecode($url[$i]) ; 
      }
      unset($url);
      unset($parse);
      return $urls ;
    }

    static function route(string $route,callable $response)
    {
      $i_route = self::url($route);
      $req_route = self::url();
      if (
        $route === '$404'
      ) {
        http_response_code(404) ;
        $response($req_route) ;
        die();
        return 0;
      }
      if (
        $route === '*'
      ) {
        $response($req_route) ;
        die();
        return 0;
      }
      $cond = (
        (count($i_route['path']) + 1) === count($req_route['path']) and
        trim($req_route['path'][count($i_route['path'])]) === ''
      ) ;
      if (
        count($req_route['path']) === count($i_route['path']) or
        $cond
      ) {
        if ($cond) {
          unset($req_route['path'][count($i_route)]);
        }
        $return = true ;
        for ($i=0; $i < count($i_route['path']); $i++) {
          $cond = (
            isset(trim($i_route['path'][$i])[0]) and
            trim($i_route['path'][$i])[0] === ':' and
            $req_route['path'][$i] !== ''
          ) ;
          if (
            ($req_route['path'][$i] !== $i_route['path'][$i]) and
            trim($i_route['path'][$i]) !== '*' and
            ! $cond
          ) {
            $return = false ;
          } elseif ( $cond ) {
            $req_route['query'][trim($i_route['path'][$i])] = $req_route['path'][$i] ;
          }
        }
        if ($return) {
          $response($req_route['query'],$req_route['path']) ;
          die();
        }
      }
    }

  } // end of class route

  class tools
  {
    public static function i_trim(string &$str)
    {
      $str = trim($str) ;
    }

    public static function exists($key, array $array)
    {
      if (array_key_exists($key, $array) and trim($array[$key]) !== null and trim($array[$key]) !== '') {
        return true;
      } else {
        return false;
      }
    }
    
    public static function exists_all(array $keys, array $array)
    {
      foreach ($keys as $k) {
        if (!self::exists($k, $array)) {
          return false;
        }
      }
      return true;
    }
    
    public static function is_exists($var, $array)
    {
      if (is_array($var)) {
        return self::exists_all($var, $array);
      } else {
        return self::exists($var, $array);
      }
    }

    public static function is_empty($var)
    {
      $each = trim($var) ;
      if ($each === null or $each === '') {
        return true ;
      } else {
        return false ;
      }
    }

    public static function are_empty(array $array)
    {
      foreach ($array as $each) {
        if ( self::is_empty($each) ) {
          return true ;
        }
      }
    }

    public static function redirect(string $location = '/')
    {
      self::i_trim($location);
      $js_redirect =<<<TEXT
<script>
window.location.replace("{$location}");
</script>
TEXT;
      header('Location: '.$location);
      die($js_redirect);
    }

    public static function println(string $str,bool $html = true)
    {
      if ($html) {
        echo $str . '<br />' ;
      } else {
        echo $str ."\n";
      }
    }

    public static function get_(String $path = "")
    {
    
      $path = trim($path) ;
    
      if (
        !empty($path)
      ) {
    
        if ($path[0] == '/') {
          $path = $_SERVER['DOCUMENT_ROOT'].$path ;
        }
    
        if (
          pathinfo($path,PATHINFO_EXTENSION)
        ) {
    
          if (
            file_exists($path)
          ) {
            return require_once $path ;
          }
    
          return false ;
        
        } else {
          
          if ( file_exists( $path.'.php' ) ) {
            return require_once $path.'.php';
          } elseif ( file_exists( $path.'.inc.php' ) ) {
            return require_once $path.'.inc.php';
          } elseif ( file_exists( $path.'.phtml' ) ) {
            return require_once $path.'.phtml';
          } elseif ( file_exists( $path.'.html' ) ) {
            return require_once $path.'.html';
          } elseif ( file_exists( $path.'.htm' ) ) {
            return require_once $path.'.htm';
          } elseif ( file_exists( $path.'.inc' ) ) {
            return require_once $path.'.inc';
          } else {
            return false ;
          }

        }
    
      }
      return false ;
    }

  } // end of tools class
  

} // end of frame\


namespace frame\auth {
  
  use \frame\session as session ;
  use \frame\security as security ;

  $SESSION = null ;

  function create ($name = 'Authorized',$key = 'this is secret key',$length = 100,$hours = 12,$tmp = null)
  {
    global $SESSION ;
    if ($SESSION === null) {
      $SESSION = new session($name,$key,$length,$hours,$tmp);
    }
  }

  function end_all_sessions()
  {
    global $SESSION ;
    $SESSION->end_all_sessions();
  }

  function set(array $info = [])
  {
    global $SESSION;
    $SESSION->start();
    $SESSION->var('@auth_arr',$info);
    $SESSION->var('@auth',true);
  }

  function get_arr()
  {
    global $SESSION;
    $SESSION->start();
    if ( $SESSION->isset('@auth') and $SESSION->isset('@auth_arr') ) {
      return $SESSION->var('@auth_arr') ;
    }
  }

  function vars($key,$value = null) {
    global $SESSION;
    $SESSION->start() ;
    if ( $SESSION->isset('@auth') and $SESSION->isset('@auth_arr') ) {
      $array = get_arr() ;
      if ($value === null) {
      if (array_key_exists($key,$array)) {
        return $array[$key];
      } else {
        return '' ;
      }
      } else {
      $array[$key] = $value ;
      if (
        $SESSION->var('@auth_arr',$array)
      ) {
        return true ;
      }
      return false ;
      }
    }
  }

  function delete_var($key)
  {
    global $SESSION;
    $SESSION->start() ;
    if ( $SESSION->isset('@auth') and $SESSION->isset('@auth_arr') ) {
      $array = get_arr() ;
      if (array_key_exists($key,$array)) {
        unset($array[$key]) ;
      } else {
        return false ;
      }
      $SESSION->var('@auth_arr',$array) ;
      return true ;
    }
    return false ;
  }

  function is_auth ( $redirect_if_not = null , $redirect_if_is = null )
  {
    global $SESSION;
    $SESSION->start() ;
    if ( $SESSION->isset('@auth') and ((bool) $SESSION->var('@auth')) === true ) {
      if ($redirect_if_is) {
      security::redirect($redirect_if_is);
      } else {
      return true ;
      }
    } else {
      if ($redirect_if_not) {
      security::redirect($redirect_if_not);
      } else {
      return false ;
      }
    }
  }

  function get_exp($format = 'text') {
    global $SESSION;
    $SESSION->start() ;
    $time = $SESSION->get_array_info()['time'] ;
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

  function unauthorize()
  {
    global $SESSION;
    $SESSION->start() ;
    $SESSION->end() ;
  }

}

