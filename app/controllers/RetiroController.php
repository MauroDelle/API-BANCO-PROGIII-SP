<?php
require_once './models/Retiro.php';
require_once './Interfaces/IInterfazAPI.php';
require_once './models/Cuenta.php';

class RetiroController extends Retiro implements IInterfazAPI
{

    public static function CargarUno($request, $response, $args){

        $params = $request->getParsedBody();
        $idCuenta = $params['idCuenta'];
        $monto = $params['monto'];
        $tipoCuenta = $params['tipoCuenta'];
    
        $retiro = new Retiro();
        $retiro->idCuenta = $idCuenta;
        $retiro->monto = $monto;
        $retiro->setFecha(date('Y-m-d H:i:s'));
        $retiro->tipoCuenta = $tipoCuenta;
    
        Retiro::crear($retiro);
    
        $responseBody = json_encode(array("mensaje" => "Retiro creado con éxito"));
        return $response->withHeader("Content-Type", "application/json")->write($responseBody);
    }
    public static function TraerUno($request, $response, $args)
    {
        $idRetiro = $args['retiro'];
        $retiro = Retiro::obtenerUno($idRetiro);

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT :: ObtenerData($token);

        
        $acceso = new Acceso();
        $acceso->idUsuario = $data->id;
        $acceso->fechaHora = date('Y-m-d H:i:s');
        $acceso->tipoTransaccion = "Consulta-GET-Retiros";
        Acceso::crear($acceso);
    
        if ($retiro) {
            $payload = json_encode($retiro);
            $response->getBody()->write($payload);
        } else {
            $response->getBody()->write(json_encode(array("mensaje" => "Retiro no encontrado")));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function TraerTodos($request, $response, $args)
    {
        $lista = Retiro::obtenerTodos();
        $payload = json_encode(array("listaRetiros" => $lista));

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $data = AutentificadorJWT :: ObtenerData($token);
        
        $acceso = new Acceso();
        $acceso->idUsuario = $data->id;
        $acceso->fechaHora = date('Y-m-d H:i:s');
        $acceso->tipoTransaccion = "Consulta-GET-Retiros";
        Acceso::crear($acceso);
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    
    public static function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        if (Retiro::obtenerUno($id)) {
            Retiro::borrar($id);
            $payload = json_encode(array("mensaje" => "Retiro borrado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "ID no coincide con un retiro"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public static function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        $retiro = Retiro::obtenerUno($id);
    
        if ($retiro != false) {
            $parametros = $request->getParsedBody();
    
            $actualizado = false;
            if (isset($parametros['numeroCuenta'])) {
                $actualizado = true;
                $retiro->setNumeroCuenta($parametros['numeroCuenta']);
            }
            if (isset($parametros['tipoCuenta'])) {
                $actualizado = true;
                $retiro->setTipoCuenta($parametros['tipoCuenta']);
            }
            if (isset($parametros['importe'])) {
                $actualizado = true;
                $retiro->setImporte($parametros['importe']);
            }
   
            if ($actualizado) {
                $retiro->modificarEnBD();
                $payload = json_encode(array("mensaje" => "Retiro modificado con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "Retiro no modificado por falta de campos"));
            }
        } else {
            $payload = json_encode(array("error" => "Retiro no existe"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function Retirar($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idCuenta = $params['idCuenta'];
        $monto = $params['monto'];
        $tipoCuenta = $params['tipoCuenta'];
    
        // Verificar si la cuenta existe
        $cuenta = Cuenta::obtenerUno($idCuenta);
    
        if ($cuenta && $cuenta->estado == true) {
            // Verificar si el monto del retiro es mayor que el saldo de la cuenta
            if ($monto > $cuenta->saldoInicial) {
                $payload = json_encode(array("mensaje" => "El monto del retiro es mayor que el saldo de la cuenta"));
            } else {
                // Realizar el retiro y actualizar el saldo
                $retiro = new Retiro();
                $retiro->idCuenta = $idCuenta;
                $retiro->monto = $monto;
                 $retiro->tipoCuenta = $tipoCuenta;
                $retiro->setFecha(date('Y-m-d H:i:s'));
                Retiro::crear($retiro);
    
                $header = $request->getHeaderLine(("Authorization"));
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT :: ObtenerData($token);
        
                
                $acceso = new Acceso();
                $acceso->idUsuario = $data->id;
                $acceso->fechaHora = date('Y-m-d H:i:s');
                $acceso->tipoTransaccion = "RETIRO";
                Acceso::crear($acceso);
                
                // Actualizar saldo en la cuenta
                $retiro->actualizarSaldoRetiro($cuenta,$monto);
        
                $payload = json_encode(array("mensaje" => "Retiro realizado con exito"));
            }
        } else {
            // La cuenta no existe, informar el error
            $payload = json_encode(array("mensaje" => "Retiro no modificado porque la cuenta no está activa"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function BuscarTipoCuenta($request,$response,$args)
    {
        $tipoCuenta = $args['tipoCuenta'];

        $retiros = Retiro::obtenerPorTipoCuenta($tipoCuenta);

        $payload = json_encode($retiros);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

}

?>