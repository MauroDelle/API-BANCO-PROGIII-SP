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
require_once './controllers/AccesosController.php';
require_once './middlewares/Logger.php';
require_once './controllers/DepositoController.php';
require_once './controllers/AjusteController.php';
require_once './controllers/RetiroController.php';
require_once './middlewares/Autentificador.php';


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

$app->group('/logout', function (RouteCollectorProxy $group) {
  $group->post('[/]', \UsuarioController::class . '::Logout');
});

$app->group('/cuenta', function (RouteCollectorProxy $group) {
    $group->post('[/]', \CuentaController::class . '::CargarUno')->add(\Autentificador::class . '::ValidarAdmin');
    $group->put('/{id}', \CuentaController::class . '::ModificarUno')->add(\Autentificador::class . '::ValidarAdmin');
    $group->delete('/{id}', \CuentaController::class . '::BorrarUno')->add(\Autentificador::class . '::ValidarAdmin');
    $group->get('[/]', \CuentaController::class . '::TraerTodos')->add(\Autentificador::class . '::ValidarOperador');
    $group->get('/{cuenta}', \CuentaController::class . '::TraerUno')->add(\Autentificador::class . '::ValidarOperador');
    $group->get('/csv/', \AccesoController::class . ':ExportarOperaciones')->add(\Autentificador::class . '::ValidarAdmin');
  });

  $app->group('/exportarPDF', function (RouteCollectorProxy $group) {
    $group->get('/{orden}', \AccesoController::class . ':ExportarOperacionesPDF')->add(\Autentificador::class . '::ValidarAdmin');
  });

  $app->group('/deposito', function (RouteCollectorProxy $group) {
    $group->post('[/]', \DepositoController::class . '::Depositar')->add(\Autentificador::class . '::ValidarCajero');
    // $group->get('/porTipo/{tipoCuenta}', \DepositoController::class . '::BuscarTipoCuenta')->add(\Autentificador::class . '::ValidarCajero');
    $group->get('/consulta/[/]', \DepositoController::class . '::TraerTodos')->add(\Autentificador::class . '::ValidarCajero');
    $group->get('/consulta/{deposito}', \DepositoController::class . '::TraerUno')->add(\Autentificador::class . '::ValidarCajero');
  });

  $app->group('/retiro', function (RouteCollectorProxy $group) {
    $group->post('[/]', \RetiroController::class . '::Retirar')->add(\Autentificador::class . '::ValidarCajero');
    $group->get('/consulta/[/]', \RetiroController::class . '::TraerTodos')->add(\Autentificador::class . '::ValidarCajero');
    $group->get('/consulta/{retiro}', \RetiroController::class . '::TraerUno')->add(\Autentificador::class . '::ValidarCajero');
  });

  $app->group('/ajuste', function (RouteCollectorProxy $group) {
    $group->post('[/]', \AjusteController::class . '::RealizarAjuste')->add(\Autentificador::class . '::ValidarAdmin');
    $group->get('/consulta/[/]', \AjusteController::class . '::TraerTodos')->add(\Autentificador::class . '::ValidarOperador');
    $group->get('/consulta/{ajuste}', \AjusteController::class . '::TraerUno')->add(\Autentificador::class . '::ValidarOperador');
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