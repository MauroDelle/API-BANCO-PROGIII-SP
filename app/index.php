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
require_once './controllers/DepositoController.php';
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
    $group->get('/{tipoCuenta}/{nroCuenta}', \CuentaController::class . '::obtenerMonedaYSaldo');
    $group->delete('/{id}', \CuentaController::class . '::BorrarUno');
    $group->get('[/]', \CuentaController::class . '::TraerTodos');
    $group->get('/{cuenta}', \CuentaController::class . '::TraerUno');
  });

  $app->group('/deposito', function (RouteCollectorProxy $group) {
    $group->post('[/]', \DepositoController::class . '::Depositar');
    // $group->put('/{id}', \CuentaController::class . '::ModificarUno');
    // $group->get('/{tipoCuenta}/{nroCuenta}', \CuentaController::class . '::obtenerMonedaYSaldo');
    // $group->delete('/{id}', \CuentaController::class . '::BorrarUno');
    $group->get('[/]', \DepositoController::class . '::TraerTodos');
    $group->get('/{cuenta}', \DepositoController::class . '::TraerUno');
  });


  $app->group('/consultas', function (RouteCollectorProxy $group) {
    // $group->put('/{id}', \CuentaController::class . '::ModificarUno');
    // $group->get('/{tipoCuenta}/{nroCuenta}', \CuentaController::class . '::obtenerMonedaYSaldo');
    // $group->delete('/{id}', \CuentaController::class . '::BorrarUno');
    $group->get('/{cuenta}', \DepositoController::class . '::TraerUno');
    $group->get('/tipo/{tipoCuenta}', \DepositoController::class . '::BuscarTipoCuenta');
    $group->get('/moneda/{moneda}', \DepositoController::class . '::BuscarMoneda');
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