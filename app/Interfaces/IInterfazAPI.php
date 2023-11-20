<?php

interface IInterfazAPI
{
    public static function CargarUno($request, $response, $args);
    public static function TraerUno($request, $response, $args);
    public static function TraerTodos($request, $response, $args);
    public static function BorrarUno($request, $response, $args);
    public static function ModificarUno($request, $response, $args);
}
?>