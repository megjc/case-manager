<?php
require 'vendor/autoload.php';
require 'routes/routes.php';

$config['displayErrorDetails'] = true;

$app = new \Slim\Slim(["settings" => $config]);
/**
* Slim group defining version of api
*/
$app->group('/v1', function() use ($app){

}); //end of group
/**
 * Run the Slim application
 */
$app->run();

?>
