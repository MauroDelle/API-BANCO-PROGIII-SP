<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
require_once "./models/Usuario.php";
require_once './middlewares/AutentificadorJWT.php';
require_once './models/Accesos.php';
require_once './controllers/AccesosController.php';

class Autentificador
{
    public static function ValidarAdmin(Request $request,RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

            try {
                //AutentificadorJWT::VerificarToken($token);
                $payload = AutentificadorJWT::ObtenerData($token);
                if ($payload->tipo == 'admin') {
                    return $handler->handle($request);
                }
                else{
                    $response->getBody()->write(json_encode(array('Error' => "ACCION NO PERMITIDA, SOLAMENTE PARA ADMINS")));
                }
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(array("Error" => $e->getMessage())));
            }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarCajero(Request $request,RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

            try {
                //AutentificadorJWT::VerificarToken($token);
                $payload = AutentificadorJWT::ObtenerData($token);
                if ($payload->tipo == 'cajero') {
                    return $handler->handle($request);
                }
                else{
                    $response->getBody()->write(json_encode(array('Error' => "ACCION NO PERMITIDA, SOLAMENTE PARA CAJEROS")));
                }
            } catch (Exception $e) {

                $response->getBody()->write(json_encode(array("Error" => $e->getMessage())));
            }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarOperador(Request $request,RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

            try {
                //AutentificadorJWT::VerificarToken($token);
                $payload = AutentificadorJWT::ObtenerData($token);
                if ($payload->tipo == 'operador') {
                    return $handler->handle($request);
                }
                else{
                    $response->getBody()->write(json_encode(array('Error' => "ACCION NO PERMITIDA, SOLAMENTE PARA OPERADORES")));
                }
            } catch (Exception $e) {

                $response->getBody()->write(json_encode(array("Error" => $e->getMessage())));
            }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ValidarCliente($request, $handler)
    {
        $cookies = $request->getCookieParams();
        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);

        AutentificadorJWT::VerificarToken($token);
        $payload = AutentificadorJWT::ObtenerData($token);


        if ($payload->tipo == 'admin' || $payload->tipo == 'cliente') {
            return $handler->handle($request);
        }

        throw new Exception("Token no valido");
    }

}




?>