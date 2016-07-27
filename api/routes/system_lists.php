<?php
include_once 'functions.php';
/**
 * Catch all function handling all user related api requests
 * @param  [type] $app [description]
 * @return [type]      [description]
 */
function getSystemLists($app){
  $app->get('/', function() use($app){
    $users = getUsers(2);
    $activities = getFileActivityTypes();
    $parishes = getParishes();
    $currencies = getCurrencies();
    $receipt_types = getReceiptTypes();
    $result = array('users' => $users,
                    'activities' => $activities,
                    'parishes' => $parishes,
                    'currencies' => $currencies,
                    'receipt_types' => $receipt_types);
    setResponseHeader($app);
    echo json_encode($result);
  });
  /**
   * Get a list of data entry users
   * @param  [type] $type user type id
   * @return Array       List of users
   */
  function getUsers($type){
    $sql = "SELECT CONCAT(first_name, ' ', last_name) as name,
            id as user_id
            FROM users WHERE user_type_id = :type";
    try{
      $db = openDBConnection();
      $stmt = $db->prepare( $sql );
      $stmt->bindParam("type", $type);
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_OBJ );
      closeDBConnection( $db );
    }catch(PDOException $e){
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
  /**
   * Get a list of file activity states
   * @return Array List of file activity states
   */
  function getFileActivityTypes(){
    $sql = "SELECT * FROM activity_types";
    try{
      $db = openDBConnection();
      $stmt = $db->prepare( $sql );
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_OBJ );
      closeDBConnection( $db );
    }catch(PDOException $e){
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
  /**
   * Get a list of file activity states
   * @return Array List of file activity states
   */
  function getReceiptTypes(){
    $sql = "SELECT * FROM receipt_types";
    try{
      $db = openDBConnection();
      $stmt = $db->prepare( $sql );
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_OBJ );
      closeDBConnection( $db );
    }catch(PDOException $e){
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
  /**
   * Get a list of parishes
   * @return Array List of parishes
   */
  function getParishes(){
    $sql = "SELECT * FROM parishes";
    try{
      $db = openDBConnection();
      $stmt = $db->prepare( $sql );
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_OBJ );
      closeDBConnection( $db );
    }catch(PDOException $e){
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
  /**
   * Get a list of currencies
   * @return Array $result list of currencies
   */
  function getCurrencies(){
    $sql = "SELECT * FROM currencies";
    try{
      $db = openDBConnection();
      $stmt = $db->prepare( $sql );
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_OBJ );
      closeDBConnection( $db );
    }catch(PDOException $e){
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
}
?>
