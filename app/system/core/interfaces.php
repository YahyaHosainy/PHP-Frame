<?php

/**
 * 
 * Session class interface
 * 
**/

interface session_interface
{
  /**
   * Start session function
   *
   * @param array $array
   * @return bool
   */
  public function start();

  /**
   * Get all seted variables
   *
   * @return array
   */
  public function get_array();

  /**
   * For set variables with an array
   *
   * @param array $array
   * @param boolean $clear  clear existing data or not
   * @return bool
   */
  public function set_array(array $array,bool $clear = false);
  
  /**
   * Get session private informations
   * 
   * @return array
   */
  public function get_object_info();

  /**
   * Get session information like time or session id with session variables
   *
   * @return array
   */
  public function get_info();

  /**
   * Set or get variables .
   *
   * @param mixed $key
   * @param mixed $value
   * @return bool
   */
  public function var($key,$value = null);

  /**
   * Check for a variable
   *
   * @param mixed $key
   * @return bool
   */
  public function isset($key);
  
  /**
   * Delete a variable
   *
   * @param mixed $key
   * @return bool
   */
  public function delete($key);

  /**
   * End all existting sessions for all browsers
   *
   * @return bool
   */
  public function end_all_sessions();

  /**
   * For delete expired session files if its count > 500
   *
   * @return bool
   */
  public function garbage_collector();
  
  /**
   * End all existting sessions for all browsers
   *
   * @return bool
   */
  public function end();
}


interface SIMPLE_PDO_interface {

  /**
   * Single get query like "SELECT * FROM ... WHERE ....."
   *
   * @param string $statement
   * @param array $values
   * @return false|array
   */
  public function get(
    string $statement,
    array $values = []
  );

  /**
   * For multi query (if one fails all fails)
   *
   * @param array ...$array
   * @return array
   */
  public function query_all (
    array ...$array
  );

  /**
   * Set into database with multiple insert like => "INSERT ..." => values(??) $values = [[??],[??]]
   *
   * @param string $statement
   * @param array $values
   * @param boolean $is_last_insert_id
   * @return bool
   */
  public function set(
    string $statement,
    array $values = [[]],
    bool $is_last_insert_id = false
  );

}

interface auth_interface {

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
  ) ;

  /**
   * Ends all authorized that created with this class
   *
   * @return bool
   */
  public function end_all_sessions () ;
  
  /**
   * Clear all authorized sessions that is expired for free the disk space
   *
   * @return void
   */
  public function clear_junks () ;
 
  /**
   * Set new authorized
   *
   * @param array $info
   * @return bool
   */
  public function set (array $info = []) ;
  
  /**
   * Give's you info array
   *
   * @return false|array
   */
  public function get_arr () ;
  
  /**
   * Set or get variable from info array
   *
   * @param mixed $key
   * @param mixed $value
   * @return false|mixed
   */
  public function var (
    $key,
    $value = null
  ) ;

  /**
   * Delete's variable form info array
   *
   * @param mixed $key
   * @return bool
   */
  public function delete_var ($key) ;
  
  /**
   * Check for authrized and redirect if not or if is in your choose
   *
   * @param string|null $redirect_if_not
   * @param string|null $redirect_if_is
   * @return boolean
   */
  public function is_auth (
    $redirect_if_not = null ,
    $redirect_if_is = null
  ) ;

  /**
   * Get remain time to expire the auth
   *
   * @param string $format => 'string'|'array'
   * @return string|array
   */
  public function get_exp (string $format = 'text') ;

  /**
   * Finish the auth
   *
   * @return bool
   */
  public function unauthorize () ;

}