<?php
#region dependencias
use Slim\Http\Request;
use Slim\Http\Response;

require_once './models/Cuenta.php';
require_once './Interfaces/IInterfazAPI.php';
#endregion

class CuentaController extends Cuenta implements IInterfazAPI
{

    #region CRUD
    public static function CargarUno($request, $response, $args)
    {

        $params = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $nombre = $params['nombre'];
        $apellido = $params['apellido'];
        $tipoDocumento = $params['tipoDocumento'];
        $nroDocumento = $params['nroDocumento'];
        $email = $params['email'];
        $tipoCuenta = $params['tipoCuenta'];
        $saldoInicial = $params['saldoInicial'];
        $estado = $params['estado'];
        $idAleatorio = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $idCuenta = $idAleatorio . $tipoCuenta;
        
        $targetPath = './ImagenesCuentas2023/' . $idCuenta . '.jpg';
        $uploadedFiles['foto']->moveTo($targetPath);

        $cuenta = new Cuenta();
        $cuenta->setId($idCuenta);
        $cuenta->setNombre($nombre);
        $cuenta->setApellido($apellido);
        $cuenta->setTipoDocumento($tipoDocumento);
        $cuenta->setNroDocumento($nroDocumento);
        $cuenta->setEmail($email);
        $cuenta->setTipoCuenta($tipoCuenta);
        $cuenta->setSaldoInicial($saldoInicial);
        $cuenta->setEstado($estado);

        Cuenta::crear($cuenta);

        $guardadojson = json_encode(array("mensaje" => "Cuenta creada con exito"));

        $response->getBody()->write($guardadojson);
        return $response->withHeader("Content-Type", "application/json");
    }

    public static function TraerUno($request, $response, $args)
    {
        $cuentaId = $args['cuenta'];

        $cuenta = Cuenta::obtenerUno($cuentaId);
        $payload = json_encode($cuenta);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerTodos($request, $response, $args)
    {
        $lista = Cuenta::obtenerTodos();
        $payload = json_encode(array("listaCuentas" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];

        if (Cuenta::obtenerUno($id)) {
            Cuenta::borrar($id);
            $payload = json_encode(array("mensaje" => "Cuenta borrada con éxito"));
        } else {
            $payload = json_encode(array("mensaje" => "ID no coincide con una cuenta"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function ModificarUno($request, $response, $args)
    {
        $id = $args['id'];

        $cuenta = Cuenta::obtenerUno($id);

        if ($cuenta != false) {
            $parametros = $request->getParsedBody();

            $actualizado = false;
            if (isset($parametros['nombre'])) {
                $actualizado = true;
                $cuenta->setNombre($parametros['nombre']);
            }
            if (isset($parametros['apellido'])) {
                $actualizado = true;
                $cuenta->setApellido($parametros['apellido']);
            }
            if (isset($parametros['tipoDocumento'])) {
                $actualizado = true;
                $cuenta->setTipoDocumento($parametros['tipoDocumento']);
            }
            if (isset($parametros['nroDocumento'])) {
                $actualizado = true;
                $cuenta->setNroDocumento($parametros['nroDocumento']);
            }
            if (isset($parametros['email'])) {
                $actualizado = true;
                $cuenta->setEmail($parametros['email']);
            }
            if (isset($parametros['tipoCuenta'])) {
                $actualizado = true;
                $cuenta->setTipoCuenta($parametros['tipoCuenta']);
            }
            if (isset($parametros['saldoInicial'])) {
                $actualizado = true;
                $cuenta->setSaldoInicial($parametros['saldoInicial']);
            }
            if (isset($parametros['estado'])) {
                $actualizado = true;
                $cuenta->setEstado($parametros['estado']);
            }

            if ($actualizado) {
                $cuenta->modificarEnBD();
                $payload = json_encode(array("mensaje" => "Cuenta modificada con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "Cuenta no modificada por falta de campos"));
            }
        } else {
            $payload = json_encode(array("error" => "Cuenta no existe"));
        }

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    #endregion


}
