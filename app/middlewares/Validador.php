<?php
require_once "./models/Usuario.php";
require_once "./models/Criptomoneda.php";
require_once './middlewares/AutentificadorJWT.php';

class Validador
{
    public static function ValidarNuevoUsuario($request, $handler)
    {
        $parametros = $request->getParsedBody();

        $mail = $parametros['mail'];
        $tipo = $parametros['tipo'];
        if (Usuario::ValidarTipo($tipo) && Usuario::ValidarMail($mail) == null) {
            return $handler->handle($request);
        }

        throw new Exception("Error en la creacion del Usuario");
    }
   
}
?>