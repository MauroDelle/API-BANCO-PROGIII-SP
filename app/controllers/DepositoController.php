<?php
use Slim\Http\Request;
use Slim\Http\Response;

require_once './models/Deposito.php';
require_once './Interfaces/IInterfazAPI.php';
require_once './models/Cuenta.php';

class DepositoController extends Deposito implements IInterfazAPI
{

    #region CRUD
    public static function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $numeroCuenta = $params['numeroCuenta'];
        $tipoCuenta = $params['tipoCuenta'];
        $importe = $params['importe'];
        $moneda = $params['moneda'];

        $deposito = new Deposito();
        $deposito->numeroCuenta = $numeroCuenta;
        $deposito->tipoCuenta = $tipoCuenta;
        $deposito->importe = $importe;
        $deposito->setFechaDeposito(date('Y-m-d H:i:s'));
        $deposito->moneda = $moneda;
        Deposito::crear($deposito);

        $responseBody = json_encode(array("mensaje" => "Depósito creado con éxito"));
        return $response->withHeader("Content-Type", "application/json")->write($responseBody);
    }
    public static function TraerUno($request, $response, $args)
    {
        $cuentaId = $args['cuenta'];
        $cuenta = Cuenta::obtenerUno($cuentaId);
    
        if ($cuenta) {
            $payload = json_encode($cuenta);
            $response->getBody()->write($payload);
        } else {
            $response->getBody()->write(json_encode(array("mensaje" => "Cuenta no encontrada")));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function TraerTodos($request, $response, $args)
    {
        $lista = Deposito::obtenerTodos();
        $payload = json_encode(array("listaDepositos" => $lista));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        if (Deposito::obtenerUno($id)) {
            Deposito::borrar($id);
            $payload = json_encode(array("mensaje" => "Depósito borrado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "ID no coincide con un depósito"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        $deposito = Deposito::obtenerUno($id);
    
        if ($deposito != false) {
            $parametros = $request->getParsedBody();
    
            $actualizado = false;
            if (isset($parametros['numeroCuenta'])) {
                $actualizado = true;
                $deposito->setNumeroCuenta($parametros['numeroCuenta']);
            }
            if (isset($parametros['tipoCuenta'])) {
                $actualizado = true;
                $deposito->setTipoCuenta($parametros['tipoCuenta']);
            }
            if (isset($parametros['importe'])) {
                $actualizado = true;
                $deposito->setImporte($parametros['importe']);
            }
            // Agrega el resto de los campos de la clase Deposito según sea necesario
    
            if ($actualizado) {
                $deposito->modificarEnBD();
                $payload = json_encode(array("mensaje" => "Depósito modificado con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "Depósito no modificado por falta de campos"));
            }
        } else {
            $payload = json_encode(array("error" => "Depósito no existe"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    #endregion

    public static function Depositar($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $numeroCuenta = $params['numeroCuenta'];
        $importe = $params['importe'];
        $moneda = $params['moneda'];
        $tipoCuenta = $params['tipoCuenta'];

        // Verificar si la cuenta existe
        $cuenta = Cuenta::obtenerUno($numeroCuenta);
        // var_dump($cuenta);

        if ($cuenta && $cuenta->estado == true) {
            // Realizar el depósito y actualizar el saldo
            $deposito = new Deposito();
            $deposito->numeroCuenta = $numeroCuenta;
            $deposito->importe = $importe;
            $deposito->tipoCuenta = $tipoCuenta;
            $deposito->moneda = $moneda;
            $deposito->setFechaDeposito(date('Y-m-d H:i:s'));
            Deposito::crear($deposito);
            $contadorDepositos = 0;
            $contadorDepositos++;
            

            $targetPath = './ImagenesDepositos2023/' . $cuenta->id . '-' . $contadorDepositos . '.jpg';
            $uploadedFiles['foto']->moveTo($targetPath);

            // Actualizar saldo en la cuenta
            $cuenta->actualizarSaldo($cuenta,$importe);
    
            $payload = json_encode(array("mensaje" => "Deposito realizado con exito"));
        } else {
            // La cuenta no existe, informar el error
            $payload = json_encode(array("mensaje" => "Deposito no modificado por que no esta activo"));
        }
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function BuscarTipoCuenta($request,$response,$args)
    {
        $tipoCuenta = $args['tipoCuenta'];

        $depositos = Deposito::obtenerPorTipoCuenta($tipoCuenta);

        $payload = json_encode($depositos);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function BuscarMoneda($request,$response,$args)
    {
        $moneda = $args['moneda'];

        $depositos = Deposito::obtenerPorMoneda($moneda);

        $payload = json_encode($depositos);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }



}
?>