<?php

#region DEPENDENCIAS
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/CuentaController.php';
require_once './db/DataAccess.php';
require_once './controllers/UsuarioController.php';
require_once './middlewares/Logger.php';
// Carga el archivo .env con la configuracion de la BD.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/banco');
// $app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

#endregion

#region app->group

// LOG IN 
$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . '::LogIn')->add(\Logger::class . '::ValidarLogin');
});


$app->group('/cuenta', function (RouteCollectorProxy $group) {
    $group->post('[/]', \CuentaController::class . '::CargarUno');
    $group->put('/{id}', \CuentaController::class . '::ModificarUno');
    $group->delete('/{id}', \CuentaController::class . '::BorrarUno');
    $group->get('[/]', \CuentaController::class . '::TraerTodos');
    $group->get('/{cuenta}', \CuentaController::class . '::TraerUno');
  });


  $app->group('/admin', function (RouteCollectorProxy $group) {
    $group->get('[/]', function ($request, $response, $args) {
      $user = new Usuario();
      $user->mail = "admin";
      $user->clave = "admin";
      $user->tipo = "admin";
  
      Usuario::crear($user);
      $payload = json_encode(array("mensaje" => "Admin creado con exito"));
  
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    });
  }); 


#endregion

$app->run();

?>