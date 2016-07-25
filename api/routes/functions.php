<?php
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
/**
 * Opens database connection
 * @return [type] [description]
 */
// TODO: implement try catch block for db connection
function openDBConnection(){
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $host = getenv('DB_HOST');
    $pdo = new PDO('mysql:host='.$host.';port=3306;dbname=case_manager', $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
/**
 * Closes database connection
 * @param  [type] $connection [description]
 * @return [type]             [description]
 */
function closeDBConnection($connection){
  $connection = null;
}
/**
 * Sets the response header
 * @param [type] $app [description]
 */
function setResponseHeader($app){
  $app->response()->header("Content-Type", "application/json");
}
/**
 * Sets the http response
 * @param [type] $app    [description]
 * @param [type] $status [description]
 */
function setHTTPStatus($app, $status){
  $app->response()->setStatus($status);
}

/**
 * Handles api responses
 * @param  [type] $error
 * @param  [type] $response
 * @param  [type] $app
 * @return [type]
 */
function apiResponse($response, $app, $error, $status){
  if($response === false) $status = 404;

  if(empty($response) === true) $status = 204;

  sendResponse($app, $status, $response);
}
/**
 * Sends api response
 * @param  [type] $app      [description]
 * @param  [type] $status   [description]
 * @param  [type] $response [description]
 * @return [type]           [description]
 */
function sendResponse($app, $status, $response){
    setResponseHeader($app);
    setHTTPStatus($app, $status);
    echo json_encode($response);
}
/**
 * Determines if parameters for search exists
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
function paramCheck($params){
    $checked = true;
    if(empty($params)){
      $checked = false;
    }else{
      if($params['q'] === "") $checked = false;
    }
    return $checked;
}
?>
