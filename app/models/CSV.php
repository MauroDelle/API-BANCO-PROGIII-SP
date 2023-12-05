<?php

class CSV
{
    public static function ExportarCSV($path)
    {
        $listaTransacciones = Acceso::obtenerTodos();
        $file = fopen($path, "w");
        foreach($listaTransacciones as $transaccion)
        {
            $separado= implode(",", (array)$transaccion);  
            if($file)
            {
                fwrite($file, $separado.",\r\n"); 
            }                           
        }
        fclose($file);  
        return $path;     
    }
}
?>