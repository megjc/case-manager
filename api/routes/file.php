<?php
include_once 'functions.php';
/**
 * Catch all function handling all file related api requests
 * @param  [type] $app [description]
 * @return [type]      [description]
 */
function routeFileRequests($app){
  $app->post('/', function() use($app){
      $new_file = json_decode($app->request->getBody());
      $response = createFile($new_file);
      echo json_encode($response);
  });

  $app->get('/connection', function() use($app){
      openDBConnection();
      echo 'End';
  });
}

function createFile($file){
    return $file;
}
?>
