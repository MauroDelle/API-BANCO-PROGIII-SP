<?php
require_once 'Cuenta.php';
require_once './Interfaces/Ipersistencia.php';
require_once './db/DataAccess.php';


class Retiro implements Ipersistencia
{
    public $idRetiro;
    public $fecha;
    public $monto;
    public $moneda;
    public $tipoCuenta;
    public $idCuenta;

    public function __construct(){}




    public static function crear($objeto){}
    public static function obtenerTodos(){}
    public static function obtenerUno($valor){}
    public static function modificar($objeto){}
    public static function borrar($objeto){}
   

}
?>