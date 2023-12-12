<?php

require_once './models/Accesos.php';
require_once './Interfaces/IInterfazAPI.php';
require_once './models/CSV.php';

class AccesoController extends Acceso implements IInterfazAPI
{

    public static function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idUsuario = $params['idUsuario'];
        $tipoTransaccion = $params['tipoTransaccion'];
    
        // Obtener la fecha y hora actual
        $fechaHora = date('Y-m-d H:i:s');
    
        $transaccion = new Acceso();
        $transaccion->idUsuario = $idUsuario;
        $transaccion->fechaHora = $fechaHora;
        $transaccion->tipoTransaccion = $tipoTransaccion;
    
        Acceso::crear($transaccion);
    
        $responseBody = json_encode(array("mensaje" => "Transacción creada con éxito"));
        return $response->withHeader("Content-Type", "application/json")->write($responseBody);
    }

    public static function TraerUno($request, $response, $args)
    {
        $transaccionId = $args['transaccion'];
        $transaccion = Acceso::obtenerUno($transaccionId);

        if ($transaccion) {
            $payload = json_encode($transaccion);
            $response->getBody()->write($payload);
        } else {
            $response->getBody()->write(json_encode(array("mensaje" => "Transacción no encontrada")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
    public static function TraerTodos($request, $response, $args){}
    public static function BorrarUno($request, $response, $args){}
    public static function ModificarUno($request, $response, $args){}

    public function ExportarOperaciones($request, $response, $args)
    {
        try
        {
            $archivo = CSV::ExportarCSV("operaciones.csv");
            if(file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("Archivo creado:" => $archivo));
            }
            else
            {
                $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
            }
            $response->getBody()->write($payload);
        }
        catch(Exception $e)
        {
            echo $e;
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }    
    }

    public function ExportarOperacionesPDF($request, $response, $args)
    {
        
        $orden = $args['orden'];

        try
        {
            $archivo = CSV::ExportarPDF("operaciones.pdf", $orden);
            if(file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("Archivo creado:" => $archivo));
            }
            else
            {
                $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
            }
            $response->getBody()->write($payload);
        }
        catch(Exception $e)
        {
            echo $e;
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }    
    }




}


?>