<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reception;
use App\Models\Sortie;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class RapportController extends Controller
{
    public function getYears()
    {
        // Récupérer les années distinctes de la colonne DateReception dans la table Reception
        $receptionYears = Reception::selectRaw('YEAR(DateReception) as year')
            ->distinct()
            ->pluck('year');

        // Récupérer les années distinctes de la colonne DateSortie dans la table Sortie
        $sortieYears = Sortie::selectRaw('YEAR(DateSortie) as year')
            ->distinct()
            ->pluck('year');

        // Fusionner les deux ensembles d'années et éliminer les doublons
        $years = $receptionYears->merge($sortieYears)->unique();

        return response()->json($years);
    }

    public function getMaterialTypes()
    {
        $types = Materiel::distinct()->pluck('Type'); // Assume que tu as un modèle Material
        return response()->json($types);
    }

    public function generatePDF(Request $request)
{
    $year = $request->input('year');
    $materialType = $request->input('materialType');

    // Récupérer les matériels correspondant au type
    $materiels = Materiel::where('Type', $materialType)
        ->get(['CodeMateriel', 'Designation', 'PrixUnitaire']); // Inclure CodeMateriel pour les requêtes suivantes

    // Calculer la quantité pour chaque matériel pour l'année spécifiée
    $materiels = $materiels->map(function ($materiel) use ($year) {
        // Récupérer la quantité reçue durant l'année
        $quantityReceived = Reception::where('CodeMateriel', $materiel->CodeMateriel)
            ->whereYear('DateReception', $year)
            ->sum('QuantiteRecu');

        // Récupérer la quantité sortie durant l'année
        $quantityExited = Sortie::where('CodeMateriel', $materiel->CodeMateriel)
            ->whereYear('DateSortie', $year)
            ->sum('QuantiteSortant');

        // Calculer la quantité totale pour l'année
        $materiel->Quantite = $quantityReceived - $quantityExited;
        $materiel->Valeur = $materiel->PrixUnitaire * $materiel->Quantite;

        return $materiel;
    });

    // Charger le template Blade avec les données
    $html = View::make('pdf.report', compact('year', 'materialType', 'materiels'))->render();

    // Initialiser Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Retourner le PDF en tant que fichier téléchargeable
    return response($dompdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="rapport.pdf"');
}

}
