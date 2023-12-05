<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = '$SPPR0GR43$';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app' => "SPPROGRA3"
        );

        $retorno = array('token' => $payload, 'jwt' => JWT::encode($payload, self::$claveSecreta));
        return $retorno;
    }

    public static function VerificarToken($token)
    {
        if($token == "" || empty($token))
        {
            throw new Exception("El token esta vacio!");
        }
        try
        {
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
        }
        catch (Exception $excepcion)
        {
            throw $excepcion;
        }
        if($payload->aud !== self::Aud())
        {
            throw new Exception("Usuario o contraseña no validos!");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }
}