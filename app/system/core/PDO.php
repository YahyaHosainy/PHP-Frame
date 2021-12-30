<?php
/** 
 * @Author: Yahya Hosainy 
 * @Date: 2020-11-29 14:27:54 
 * @Desc: PDO Driver Class
 */

class SIMPLE_PDO extends PDO implements SIMPLE_PDO_interface
{

  public $th = null ;
  
  function __construct(
    string $driver ,
    string $host ,
    string $database ,
    string $username = null ,
    string $password =null ,
    string $port = '3306',
    array $opt = null
  ) {

    if (
      $opt === null
    ) {

      $opt = array(
        self::ATTR_PERSISTENT          => true,
        self::ATTR_ERRMODE             => self::ERRMODE_EXCEPTION,
        self::ATTR_AUTOCOMMIT          => false,
        self::ATTR_DEFAULT_FETCH_MODE  => self::FETCH_ASSOC
      );

    }

    try {
      
      parent::__construct(
        "{$driver}:host={$host}:{$port};dbname={$database}",
        $username,
        $password,
        $opt
      );
      
    } catch (Throwable $ex) {
      
      $this->th = $ex ;
    
    }
    
  }

  /**
   * PDO simply set function
   *
   * @param string $statement
   * @param array $values
   * @param boolean $last_insert_id
   * @return bool
   */
  public function set (
    $statement ,
    array $values = [[]] ,
    bool $last_insert_id = false
  ) {

    try {
      
      $this->beginTransaction();
      $prepare = $this->prepare($statement);
      foreach ($values as $value) {
        $prepare->execute($value);
      }
      $last_i_i = $this->lastInsertId();
      $this->commit();
      if ($last_insert_id) {
        return $last_i_i ;
      }
      return true ;
      
    } catch (Throwable $th) {
      
      $this->th = $th;
      $this->rollBack();
      return false ;
    
    }
    
  }

  /**
   * Query multiple statement but if any if this has an erorr stop all
   *
   * @param array ...$array
   * @return array
   */
  public function query_all (array ...$array) {

    if (!empty($array)) {
      
      $index = [] ;
      $i = -1 ;
      
      try {
        
        $this->beginTransaction();

        foreach ($array as $arr) {

          $i++ ;

          if (
            isset($arr['stat'])
          ) {
            
            $prepare = $this->prepare($arr['stat']);

            if (
              isset($arr['vals']) and
              !empty($arr['vals']) and
              isset($arr['vals'][0]) and
              is_array($arr['vals'][0])
            ) {
              
              foreach ($arr['vals'] as $va) {
                $prepare->execute($va) ;
                
                if (
                  in_array('getid',$arr)
                ) {
                  $index[$i][]['last_insert_id'] = $this->lastInsertId() ;
                }
              
              }
            
            } elseif (
              isset($arr['vals']) and
              is_array($arr['vals']) and
              !empty($arr['vals'])
            ) {

              $prepare->execute($arr['vals']) ;
              
              if (
                in_array('getid',$arr)
              ) {
                $index[$i]['last_insert_id'] = $this->lastInsertId() ;
              }
              
            } else {
              
              $prepare->execute();
              
              if (
                in_array('getid',$arr)
              ) {
                $index[$i]['last_insert_id'] = $this->lastInsertId() ;
              }

            }

            if (
              strtolower(explode(' ',trim($arr['stat']))[0]) === 'select'
            ) {
              $index[] = $prepare->fetchAll();
            }
            
          } else {

            throw new Exception("Key stat not found in array of parameter {$i}th", 1);
            break;

          }

        }

        $this->commit();

        return $index ;
        
      } catch (Throwable $th) {
        
        $this->th = $th ;
        $this->rollBack();
        return false ;
        
      }
      
    }

  }

  /**
   * Simply get function
   *
   * @param string $statement
   * @param array $values
   * @return array|false
   */
  public function get (
    $statement ,
    array $values = []
  ) {

    try {
      
      $this->beginTransaction();
      $prepare = $this->prepare($statement);
      $prepare->execute($values);
      $get = $prepare->fetchAll();
      $this->commit();
      return $get ;
      
    } catch (Throwable $th) {
      
      $this->th = $th;
      $this->rollBack();
      return false ;
    
    }
    
  }

}