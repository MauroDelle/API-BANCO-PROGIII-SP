<?php

use Fpdf\Fpdf;

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


public static function ExportarPDF($path, $orden)
{
    // Obtén todos los logs
    $listaTransacciones = Acceso::obtenerTodos();

    // Ordena los logs si se especificó un orden
    if ($orden == 'ascendente') {
        sort($listaTransacciones);
    } elseif ($orden == 'descendente') {
        rsort($listaTransacciones);
    }

    // Crea un nuevo objeto FPDF
    $pdf = new FPDF();

    // Añade una página al PDF
    $pdf->AddPage();

    // Define el contenido del PDF
    foreach ($listaTransacciones as $transaccion) {
        $pdf->SetFont('helvetica', '', 12);
        $contenido = implode(", ", (array)$transaccion);
        $pdf->Cell(0, 10, $contenido, 0, 1);
    }

    // Guarda el PDF en el servidor
    $pdf->Output('F', $path, 'I');

    return $path;
}







}
?>