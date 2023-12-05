<?php

require_once "./models/Usuario.php";
require_once './models/Accesos.php';

class Logger
{
    public static function ValidarLogin($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $mail = $parametros['mail'];
        $clave = $parametros['clave'];
        $usuario = Usuario::obtenerUno($mail);
    
        if ($usuario != false && password_verify($clave, $usuario->clave)) {
            $acceso = new Acceso();
            $acceso->idUsuario = $usuario->id;
            $acceso->fechaHora = date('Y-m-d H:i:s');
            // Aquí establecemos el tipo de transacción basado en el tipo de usuario
            $acceso->tipoTransaccion = "Login-" . ucfirst($usuario->tipo);
            Acceso::crear($acceso);
            return $handler->handle($request);
        }
        throw new Exception("Usuario y/o clave erroneos");
    }
}