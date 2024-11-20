<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reception;
use App\Models\Sortie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SortieController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return Sortie::all();
    // }
    public function index()
    {
        $receptions = Sortie::with('user')->get();
        return response()->json($receptions);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Récupérer la dernière DateReception pour le CodeMateriel
        $dateReception = Reception::where('CodeMateriel', $request->CodeMateriel)
            ->orderBy('DateReception', 'desc')
            ->value('DateReception');

        // Validation des données
        $fields = $request->validate([
            'BonSortie' => 'required|max:20|unique:sorties,BonSortie',
            'CodeMateriel' => 'required|exists:materiels,CodeMateriel',
            'QuantiteSortant' => 'required|integer|min:1',
            'Destinataire' => 'required|max:150',
            'DateSortie' => [
                'required',
                'date',
                'after_or_equal:' . ($dateReception ?? 'now'),
            ],
        ], [
            'BonSortie.required' => 'Le numéro de bon de sortie est obligatoire.',
            'BonSortie.max' => 'Le numéro de bon de sortie ne doit pas dépasser 20 caractères.',
            'BonSortie.unique' => 'Ce numéro de bon de sortie existe déjà.',

            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.exists' => 'Le code matériel spécifié n\'existe pas dans la base de données.',

            'QuantiteSortant.required' => 'La quantité sortante est obligatoire.',
            'QuantiteSortant.integer' => 'La quantité sortante doit être un nombre entier.',
            'QuantiteSortant.min' => 'La quantité sortante doit être d\'au moins 1.',

            'Destinataire.required' => 'Le destinataire est obligatoire.',
            'Destinataire.max' => 'Le destinataire ne doit pas dépasser 150 caractères.',

            'DateSortie.required' => 'La date de sortie est obligatoire.',
            'DateSortie.date' => 'La date de sortie doit être une date valide.',
            'DateSortie.after_or_equal' => 'La date de sortie ne peut pas être antérieure à la date de réception.',
        ]);

        // Vérifier si le matériel existe
        $materiel = Materiel::find($fields['CodeMateriel']);
        if ($materiel) {
            // Vérifier si la quantité sortante dépasse le stock disponible
            if ($materiel->Quantite < $fields['QuantiteSortant']) {
                return response()->json(['message' => 'Stock insuffisant, quantité demandée supérieure au stock disponible.'], 400);
            }

            // Soustraire la quantité sortante du stock
            $materiel->Quantite -= $fields['QuantiteSortant'];
            $materiel->save();

            // Créer la sortie
            $sortie = $request->user()->sorties()->create($fields);

            return ['sortie' => $sortie, 'user' => $sortie->user];
        }

        return response()->json(['message' => 'Matériel non trouvé.'], 404);
    }




    /**
     * Display the specified resource.
     */

    public function show($BonSortie)
    {
        $sortie = Sortie::with('user')->where('BonSortie', $BonSortie)->first();
        if ($sortie) {
            return response()->json(['sortie' => $sortie]);
        } else {
            return response()->json(['error' => 'Sortie non trouvée'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $BonSortie)
    {
        $sortie = Sortie::where('BonSortie', $BonSortie)->first();

        if (!$sortie) {
            return response()->json(['error' => 'Sortie non trouvée'], 404);
        }

        if (!Gate::allows('update-sortie', $sortie)) {
            return response()->json(['error' => 'Vous n\'avez pas la permission de mettre à jour cette sortie'], 403);
        }

        $fields = $request->validate([
            'BonSortie' => [
                'required',
                'max:20',
                Rule::unique('sorties')->ignore($sortie->BonSortie, 'BonSortie'), // Ignore le BonSortie actuel
            ],
            'CodeMateriel' => 'required|exists:materiels,CodeMateriel',
            'QuantiteSortant' => 'required|integer|min:1',
            'Destinataire' => 'required|max:150',
            'DateSortie' => 'required|date',
        ], [
            'BonSortie.required' => 'Le bon de sortie est obligatoire.',
            'BonSortie.max' => 'Le bon de sortie ne doit pas dépasser 20 caractères.',
            'BonSortie.unique' => 'Ce bon de sortie existe déjà.',

            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.exists' => 'Le code matériel spécifié n\'existe pas dans la base de données.',

            'QuantiteSortant.required' => 'La quantité sortante est obligatoire.',
            'QuantiteSortant.integer' => 'La quantité sortante doit être un nombre entier.',
            'QuantiteSortant.min' => 'La quantité sortante doit être supérieure ou égale à 1.',

            'Destinataire.required' => 'Le destinataire est obligatoire.',
            'Destinataire.max' => 'Le destinataire ne doit pas dépasser 150 caractères.',

            'DateSortie.required' => 'La date de sortie est obligatoire.',
            'DateSortie.date' => 'La date de sortie doit être une date valide.',
        ]);

        $oldQuantiteSortant = $sortie->QuantiteSortant;

        $materiel = Materiel::find($fields['CodeMateriel']);
        if ($materiel) {
            $materiel->Quantite += $oldQuantiteSortant; // Soustraire l'ancienne quantité
            $materiel->Quantite -= $fields['QuantiteSortant']; // Ajouter la nouvelle quantité

            // Vérifier si le stock devient négatif
            if ($materiel->Quantite < 0) {
                return response()->json(['message' => 'Le stock est insuffisant pour cette sortie.'], 400);
            }

            $materiel->save(); // Enregistrer les changements
            $sortie->update($fields); // Mettre à jour la sortie
        }

        return response()->json(['message' => 'Sortie mise à jour avec succès', 'sortie' => $sortie], 200);
    }




    public function destroy($BonSortie)
    {
        $sortie = Sortie::where('BonSortie', $BonSortie)->first();

        if ($sortie) {
            if (Gate::allows('delete-sortie', $sortie)) {
                // Récupérer le matériel associé à cette sortie
                $materiel = Materiel::find($sortie->CodeMateriel);

                if ($materiel) {
                    // Ajouter la quantité sortante au stock
                    $materiel->Quantite += $sortie->QuantiteSortant; // Ajouter la quantité sortante au stock du matériel
                    $materiel->save(); // Enregistrer les changements
                }

                // Supprimer la sortie
                $sortie->delete();
                return response()->json(['message' => 'Sortie supprimée avec succès'], 200);
            } else {
                return response()->json(['error' => 'Vous n\'avez pas la permission de supprimer cette sortie'], 403);
            }
        } else {
            return response()->json(['error' => 'Sortie non trouvée'], 404);
        }
    }
}
