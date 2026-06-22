<?php

namespace App\Http\Controllers;

// Importamos la clse PDF de la libreria dompdf
use PDF;
use App\Models\Fotografia;

class PDFController extends Controller
{
    // Esta el la funcion que se encargara de pasar nuestra vista a un pdf
    public function generarPDF($id)
    {
        // Cargar la fotografía con sus likes y comentarios
        $fotografia = Fotografia::with(['likes', 'comentarios'])->findOrFail($id);

        // Pasamos la fotografía, los likes y los comentarios a la vista
        $pdf = PDF::loadView('pdf', compact('fotografia'));

        // Abrimos en una nueva ventana el documento PDF
        return $pdf->stream('fotografia.pdf');
    }
}
