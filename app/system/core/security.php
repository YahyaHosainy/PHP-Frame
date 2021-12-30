<?php
/**
 * @Author: Yahya Hosainy <yahyayakta@gmail.com>
 * @Date: 2020-11-13
 * @Desc: security class 
 */

/**
 * Security class
 */
class security
{

  /**
  * decrypt AES 256
  *
  * @param string $edata
  * @param string $password
  * @return decrypted data
  */
  static function decrypt(string $edata,string $password) {
    
    $data = base64_decode($edata);
    $salt = substr($data, 0, 16);
    $ct = substr($data, 16);

    $rounds = 3; // depends on key length
    $data00 = $password.$salt;
    $hash = array();
    $hash[0] = hash('sha256', $data00, true);
    $result = $hash[0];
    
    for ($i = 1; $i < $rounds; $i++) {
      $hash[$i] = hash('sha256', $hash[$i - 1].$data00, true);
      $result .= $hash[$i];
    }
    
    $key = substr($result, 0, 32);
    $iv  = substr($result, 32,16);

    return openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv);
  }

  /**
   * crypt AES 256
   *
   * @param string $data
   * @param string $password
   * @return base64 encrypted data
   */
  static function encrypt(string $data,string $password) {
    
    // Set a random salt
    $salt = openssl_random_pseudo_bytes(16);

    $salted = '';
    $dx = '';
    // Salt the key(32) and iv(16) = 48
    while (strlen($salted) < 48) {
      $dx = hash('sha256', $dx.$password.$salt, true);
      $salted .= $dx;
    }

    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);

    $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, true, $iv);
    return base64_encode($salt . $encrypted_data);
  
  }

  /**
   * @param string $data
   * 
   * @return string
   */
  public static function echo_s(string $data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    echo $data;
  }

  /**
   * @param string $data
   * @param null $var
   * 
   * @return string
   */
  public static function s_echo_s(string $data,&$var = null)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($var) {
      $var = $data;
      return true;
    } else {
      return $data;
    }
  }

  /**
   * Function to check is SSL / LTS is on or not
   *
   * @return boolean
   */
  static function is_secure() {

    if (
      (
        !empty($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== 'off'
      ) or (
        ! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) and
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
      ) or (
        !empty($_SERVER['HTTP_X_FORWARDED_SSL']) and $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'
      ) or (
        isset($_SERVER['SERVER_PORT']) and
        $_SERVER['SERVER_PORT'] == 443
      ) or (
        isset($_SERVER['HTTP_X_FORWARDED_PORT']) and
        $_SERVER['HTTP_X_FORWARDED_PORT'] == 443
      ) or (
        isset($_SERVER['REQUEST_SCHEME']) and
        $_SERVER['REQUEST_SCHEME'] == 'https'
      )
    ) {
      return true;
    } else {
      return false;
    }

  }
}
