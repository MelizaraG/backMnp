<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reception;
use App\Models\Sortie;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Recherche dans Materiels
        $materiels = Materiel::where('CodeMateriel', 'LIKE', "%{$query}%")
            ->orWhere('Designation', 'LIKE', "%{$query}%")
            ->orWhere('Type', 'LIKE', "%{$query}%")
            ->orWhere('Quantite', 'LIKE', "%{$query}%")
            ->orWhere('PrixUnitaire', 'LIKE', "%{$query}%")
            ->get();

        // Recherche dans Receptions
        $receptions = Reception::where('BonReception', 'LIKE', "%{$query}%")
            ->orWhere('QuantiteRecu', 'LIKE', "%{$query}%")
            ->orWhere('DateReception', 'LIKE', "%{$query}%")
            ->get();

        // Recherche dans Sorties
        $sorties = Sortie::where('BonSortie', 'LIKE', "%{$query}%")
            ->orWhere('QuantiteSortant', 'LIKE', "%{$query}%")
            ->orWhere('Destinataire', 'LIKE', "%{$query}%")
            ->orWhere('DateSortie', 'LIKE', "%{$query}%")
            ->get();

        return response()->json([
            'materiels' => $materiels,
            'receptions' => $receptions,
            'sorties' => $sorties,
        ]);
    }
}


