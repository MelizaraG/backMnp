<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use App\Models\Reception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ReceptionController extends Controller implements HasMiddleware
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
    public function index()
    {
        $receptions = Reception::with('user')->get();
        return response()->json($receptions);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'BonReception' => 'required|max:20|unique:receptions,BonReception', // Validation pour BonReception
            'CodeMateriel' => 'required|exists:materiels,CodeMateriel',
            'QuantiteRecu' => 'required|integer|min:1',
            'DateReception' => 'required|date|before_or_equal:today',
        ], [
            'BonReception.required' => 'Le numéro de bon de réception est obligatoire.',
            'BonReception.max' => 'Le numéro de bon de réception ne doit pas dépasser 20 caractères.',
            'BonReception.unique' => 'Ce numéro de bon de réception existe déjà.',

            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.exists' => 'Le code matériel spécifié n\'existe pas dans la base de données.',

            'QuantiteRecu.required' => 'La quantité reçue est obligatoire.',
            'QuantiteRecu.integer' => 'La quantité reçue doit être un nombre entier.',
            'QuantiteRecu.min' => 'La quantité reçue doit être d\'au moins 1.',

            'DateReception.required' => 'La date de réception est obligatoire.',
            'DateReception.date' => 'La date de réception doit être une date valide.',
            'DateReception.before_or_equal' => 'La date de réception ne peut pas être dans le futur.',
        ]);


        $reception = $request->user()->receptions()->create($fields);

        $materiel = Materiel::find($fields['CodeMateriel']);
        if ($materiel) {
            $materiel->Quantite += $fields['QuantiteRecu']; // Additionner la quantité reçue
            $materiel->save(); // Enregistrer les changements
        }

        return ['reception' => $reception, 'user' => $reception->user];
    }

    /**
     * Display the specified resource.
     */
    public function show(Reception $reception)
    {
        return ['reception' => $reception, 'user' => $reception->user];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reception $reception)
    {
        Gate::authorize('modify', $reception);

        $fields = $request->validate([
            'BonReception' => [
                'required',
                'max:20',
                Rule::unique('receptions')->ignore($reception->BonReception, 'BonReception'), // Ignore le BonReception actuel
            ],
            'CodeMateriel' => 'required|exists:materiels,CodeMateriel',
            'QuantiteRecu' => 'required|integer|min:1',
            'DateReception' => 'required|date|before_or_equal:today',
        ], [
            'BonReception.required' => 'Le bon de réception est obligatoire.',
            'BonReception.max' => 'Le bon de réception ne doit pas dépasser 20 caractères.',
            'BonReception.unique' => 'Ce bon de réception existe déjà.',
            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.exists' => 'Le code matériel spécifié n\'existe pas dans la base de données.',
            'QuantiteRecu.required' => 'La quantité reçue est obligatoire.',
            'QuantiteRecu.integer' => 'La quantité reçue doit être un nombre entier.',
            'QuantiteRecu.min' => 'La quantité reçue doit être supérieure ou égale à 1.',
            'DateReception.required' => 'La date de réception est obligatoire.',
            'DateReception.date' => 'La date de réception doit être une date valide.',
            'DateReception.before_or_equal' => 'La date de réception ne peut pas être supérieure à la date d\'aujourd\'hui.',
        ]);

        $oldQuantiteRecu = $reception->QuantiteRecu;

        $materiel = Materiel::find($fields['CodeMateriel']);
        if ($materiel) {
            $materiel->Quantite -= $oldQuantiteRecu; // Soustraire l'ancienne quantité reçue
            $materiel->Quantite += $fields['QuantiteRecu'];  // Additionner la quantité reçue

            if ($materiel->Quantite < 0) {
                return response()->json([
                    'errors' => [
                        'QuantiteRecu' => ['Le stock ne peut pas devenir négatif.']
                    ]
                ], 400);
            }
            $reception->update($fields);
            $materiel->save(); // Enregistrer les changements
        }

        return ['reception' => $reception, 'user' => $reception->user];
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reception $reception)
    {
        Gate::authorize('modify', $reception);
        $materiel = Materiel::find($reception->CodeMateriel);
        if ($materiel) {
            if (($materiel->Quantite - $reception->QuantiteRecu) < 0) {
                return response()->json([
                    'errors' => [
                        'QuantiteRecu' => ['Le stock ne peut pas devenir négatif.']
                    ]
                ], 400);
            }
            $materiel->Quantite -= $reception->QuantiteRecu;
            $materiel->save();
        }
        $reception->delete();
        return ['message' => 'Réception supprimée avec succès !'];
    }
}
