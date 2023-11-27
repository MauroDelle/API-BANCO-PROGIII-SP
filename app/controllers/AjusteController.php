<?php
require_once './models/Ajuste.php';
require_once './Interfaces/IInterfazAPI.php';


class AjusteController extends Ajuste implements IInterfazAPI
{
    public static function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $tipoTransaccion = $params['tipoTransaccion'];
        $idDepositoRetiro = $params['idDepositoRetiro'];
        $motivo = $params['motivo'];
        $monto = $params['monto'];
    
        $ajuste = new Ajuste();
        $ajuste->tipoTransaccion = $tipoTransaccion;
        $ajuste->idDepositoRetiro = $idDepositoRetiro;
        $ajuste->motivo = $motivo;
        $ajuste->monto = $monto;
    
        Ajuste::crear($ajuste);
    
        $responseBody = json_encode(array("mensaje" => "Ajuste creado con éxito"));
        return $response->withHeader("Content-Type", "application/json")->write($responseBody);
    }
    public static function TraerUno($request, $response, $args)
    {
        $ajusteId = $args['ajuste'];
        $ajuste = Ajuste::obtenerUno($ajusteId);
    
        if ($ajuste) {
            $payload = json_encode($ajuste);
            $response->getBody()->write($payload);
        } else {
            $response->getBody()->write(json_encode(array("mensaje" => "Ajuste no encontrado")));
        }
    
        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function TraerTodos($request, $response, $args)
    {
        $lista = Ajuste::obtenerTodos();
        $payload = json_encode(array("listaAjustes" => $lista));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public static function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        if (Ajuste::obtenerUno($id)) {
            Ajuste::borrar($id);
            $payload = json_encode(array("mensaje" => "Ajuste borrado con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "ID no coincide con un ajuste"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    public static function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];
    
        $ajuste = Ajuste::obtenerUno($id);
    
        if ($ajuste != false) {
            $parametros = $request->getParsedBody();
    
            $actualizado = false;
            if (isset($parametros['tipoTransaccion'])) {
                $actualizado = true;
                $ajuste->setTipoTransaccion($parametros['tipoTransaccion']);
            }
            if (isset($parametros['idDepositoRetiro'])) {
                $actualizado = true;
                $ajuste->setIdDepositoRetiro($parametros['idDepositoRetiro']);
            }
            if (isset($parametros['motivo'])) {
                $actualizado = true;
                $ajuste->setMotivo($parametros['motivo']);
            }
            if (isset($parametros['monto'])) {
                $actualizado = true;
                $ajuste->setMonto($parametros['monto']);
            }
    
            // Agrega el resto de los campos de la clase Ajuste según sea necesario
    
            if ($actualizado) {
                $ajuste->modificar();
                $payload = json_encode(array("mensaje" => "Ajuste modificado con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "Ajuste no modificado por falta de campos"));
            }
        } else {
            $payload = json_encode(array("error" => "Ajuste no existe"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function RealizarAjuste($request,$response,$args)
    {
        $params = $request->getParsedBody();
        $motivo = $params['motivo'];
        $monto = $params['monto'];
        $tipoTransaccion = $params['tipoTransaccion'];
        $idDepositoRetiro = $params['idDepositoRetiro'];

        

        $ajuste = Ajuste::generarAjuste($motivo,$monto,$tipoTransaccion,$idDepositoRetiro);

        if ($idDepositoRetiro) {
            // Realizar el ajuste
            // $ajuste = new Ajuste();
            // $ajuste->setIdDepositoRetiro($numeroExtraccionDeposito);
            // $ajuste->setMotivo($motivo);
    
            // Ajuste::crear($ajuste); // Suponiendo que existe un método crear en la clase Ajuste
    
            // // Actualizar saldo en la cuenta
            // $cuenta = Cuenta::obtenerUno($extraccionDepositoExistente->getIdCuenta());
            // $nuevoSaldo = $cuenta->getSaldo() - $extraccionDepositoExistente->getMonto(); // Ajuste, puedes cambiar la lógica según sea necesario
            // $cuenta->actualizarSaldo($nuevoSaldo);
    
            $payload = json_encode(array("mensaje" => "Ajuste realizado con éxito"));
        } else {
            $payload = json_encode(array("error" => "Número de extracción o depósito no encontrado"));
        }
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');

    }


}


?>