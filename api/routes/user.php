<?php
include_once '../api/lib/functions.php';
/**
 * Catch all function handling all user related api requests
 * @param  [type] $app [description]
 * @return [type]      [description]
 */
function routeUserRequests($app){
  $app->get('/', function() use($app){
    $params = $app->request->get();
    $isTypeRequest = false;
    if(!empty($params)) {
      foreach ($params as $key => $value) {
        if($key === "type"){
            if($value === "") $type = 0;
            else $type = $value;
        }
        $isTypeRequest = true;
      }
    }
    if($isTypeRequest) {
      $result = getUsersByType($type);
    }else{
      $result = getAllUsers();
    }
    setResponseHeader($app);
    echo json_encode($result);
  });
  /**
   * Get users by type
   * @param  [type] $type Integer indicating
   * @return [type]       [description]
   */
  function getUsersByType($type){
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
   * Get all users from the database
   * @return Array
   */
  function getAllUsers(){
    $sql = "SELECT * FROM users";
    try {
        $db = openDBConnection();
        $stmt = $db->query( $sql );
        $result = $stmt->fetchAll( PDO::FETCH_OBJ );
        closeDBConnection( $db );
    } catch (PDOException $e) {
      $result = '{"error":{"text":' .$e->getMessage(). '}}';
    }
    return $result;
  }
}
?>
