<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function generatePDF(Request $request, $codeMateriel)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Récupérer le matériel spécifique
        $materiel = Materiel::with('sorties', 'receptions')->where('CodeMateriel', $codeMateriel)->firstOrFail();

        // Récupère la vue que tu souhaites utiliser
        $html = view('pdf_view', ['materiel' => $materiel])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->stream(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="document.pdf"',
            ]
        );
    }
}




