<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once './models/Usuario.php';
require_once './Interfaces/IInterfazAPI.php';
require_once './middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IInterfazAPI
{
  public static function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $mail = $parametros['mail'];
    $tipo = $parametros['tipo'];
    $clave = $parametros['clave'];

    $user = new Usuario();
    $user->mail = $mail;
    $user->tipo = $tipo;
    $user->clave = $clave;

    Usuario::crear($user);
    $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerUno($request, $response, $args)
  {

    $id = $args['id'];

    $usuario = Usuario::obtenerUnoPorID($id);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function TraerTodos($request, $response, $args)
  {

    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function ModificarUno($request, $response, $args)
  {
    $id = $args['id'];

    $usuario = Usuario::obtenerUnoPorID($id);

    if ($usuario != false) {
      $parametros = $request->getParsedBody();

      $actualizado = false;
      if (isset($parametros['mail'])) {
        $actualizado = true;
        $usuario->mail = $parametros['mail'];
      }
      if (isset($parametros['clave'])) {
        $actualizado = true;
        $usuario->clave = password_hash($parametros['clave'], PASSWORD_DEFAULT);
      }
      if (isset($parametros['tipo'])) {
        $actualizado = true;
        $usuario->tipo = $parametros['tipo'];
      }

      if ($actualizado) {
        Usuario::modificar($usuario);
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
      } else {
        $payload = json_encode(array("mensaje" => "Usuario no modificar por falta de campos"));
      }

    } else {
      $payload = json_encode(array("error" => "Usuario no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    if (Usuario::obtenerUnoPorID($usuarioId)) {

      Usuario::borrar($usuarioId);
      $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
    } else {

      $payload = json_encode(array("mensaje" => "ID no coincide con un usuario"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public static function LogIn($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    $user = $parametros['mail'];

    $usuario = Usuario::obtenerUno($user);

    $data = array('id' => $usuario->id, 'mail' => $usuario->mail, 'tipo' => $usuario->tipo, 'clave' => $usuario->clave);
    $creacion = AutentificadorJWT::CrearToken($data);

    $response = $response->withHeader('Set-Cookie', 'token=' . $creacion['jwt']);

    $payload = json_encode(array("mensaje" => "Usuario logeado, cookie entregada", "token" => $creacion['jwt']));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

}