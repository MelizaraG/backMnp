<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reception;
use App\Models\Sortie;
use Illuminate\Support\Facades\DB;

class HistogramController extends Controller
{
    public function getStockHistogram()
    {
        // Définir l'année actuelle
        $currentYear = date('Y');

        // Récupérer les données des réceptions pour l'année actuelle
        $receptions = DB::table('receptions')
            ->selectRaw('MONTH(DateReception) as month, SUM(QuantiteRecu) as total_reception')
            ->whereYear('DateReception', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Récupérer les données des sorties pour l'année actuelle
        $sorties = DB::table('sorties')
            ->selectRaw('MONTH(DateSortie) as month, SUM(QuantiteSortant) as total_sortie')
            ->whereYear('DateSortie', $currentYear)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Retourner les données au format JSON
        return response()->json([
            'receptions' => $receptions,
            'sorties' => $sorties,
        ]);
    }

    public function getDashboardData()
    {
        // Calcul des données
        $totalMateriels = Materiel::count();
        $valeurTotale = Materiel::sum(DB::raw('Quantite * PrixUnitaire'));
        $depenseTotale = Reception::join('materiels', 'receptions.CodeMateriel', '=', 'materiels.CodeMateriel')
            ->sum(DB::raw('receptions.QuantiteRecu * materiels.PrixUnitaire'));

        $receptionsRecents = Reception::latest()->take(5)->get();
        $sortiesRecents = Sortie::latest()->take(5)->get();

        return response()->json([
            'totalMateriels' => $totalMateriels,
            'valeurTotale' => $valeurTotale,
            'depenseTotale' => $depenseTotale,
            'receptionsRecents' => $receptionsRecents,
            'sortiesRecents' => $sortiesRecents
        ]);
    }
}
