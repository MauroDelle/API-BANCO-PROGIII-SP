<?php

require_once "./models/Usuario.php";
require_once './middlewares/AutentificadorJWT.php';


class Autentificador
{
    public static function ValidarAdmin($request, $handler)
    {
        $cookies = $request->getCookieParams();
        $token = $cookies['token'];

        AutentificadorJWT::VerificarToken($token);
        $payload = AutentificadorJWT::ObtenerData($token);

        if ($payload->tipo == 'admin') {
            return $handler->handle($request);
        }

        throw new Exception("Token no valido");
    }

    public static function ValidarCliente($request, $handler)
    {
        $cookies = $request->getCookieParams();
        $token = $cookies['token'];

        AutentificadorJWT::VerificarToken($token);
        $payload = AutentificadorJWT::ObtenerData($token);


        if ($payload->tipo == 'admin' || $payload->tipo == 'cliente') {
            return $handler->handle($request);
        }

        throw new Exception("Token no valido");
    }

}




?>