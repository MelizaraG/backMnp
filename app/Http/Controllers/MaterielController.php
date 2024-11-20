<?php

namespace App\Http\Controllers;

use App\Models\Materiel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;



class MaterielController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum' , except:['index' , 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */

    // public function index()
    // {
    //     return Materiel::all();
    // }
    
    public function index()
    {
        $receptions = Materiel::with('user')->get();
        return response()->json($receptions);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'CodeMateriel' => 'required|max:20|unique:materiels,CodeMateriel',
            'Designation' => 'required',
            'Type' => 'required',
            'Quantite' => 'required|integer|in:0',
            'PrixUnitaire' => 'required|numeric|min:0.01'
        ], [
            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.max' => 'Le code matériel ne doit pas dépasser 20 caractères.',
            'CodeMateriel.unique' => 'Ce code matériel existe déjà dans la base de données.',
            
            'Designation.required' => 'La désignation est obligatoire.',
            
            'Type.required' => 'Le type est obligatoire.',
            
            'Quantite.required' => 'La quantité est obligatoire.',
            'Quantite.integer' => 'La quantité doit être un nombre entier.',
            'Quantite.in' => 'La quantité ne peut être que 0.',
            
            'PrixUnitaire.required' => 'Le prix unitaire est obligatoire.',
            'PrixUnitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'PrixUnitaire.min' => 'Le prix unitaire doit être supérieur à 0.01.',
        ]);
        

        // Initialiser Quantite à 0 avant de créer le matériel
        // $fields['Quantite'] = 0;   

        $materiel = $request->user()->materiels()->create($fields);  

        return ['materiel' => $materiel , 'user' => $materiel -> user];
    }

    /**
     * Display the specified resource.
     */
    public function show(Materiel $materiel)
    {
        return ['materiel' => $materiel , 'user' => $materiel -> user];
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Materiel $materiel)
    {
        Gate::authorize('modify', $materiel);

        $fields = $request->validate([
            'CodeMateriel' => [
                'required',
                'max:20',
                Rule::unique('materiels')->ignore($materiel->CodeMateriel, 'CodeMateriel'), // Ignore le CodeMateriel actuel
            ],
            'Designation' => 'required',
            'Type' => 'required',
            'Quantite' => 'required|integer',
            'PrixUnitaire' => 'required|numeric|min:0.01'
        ], [
            'CodeMateriel.required' => 'Le code matériel est obligatoire.',
            'CodeMateriel.max' => 'Le code matériel ne doit pas dépasser 20 caractères.',
            'CodeMateriel.unique' => 'Ce code matériel est déjà utilisé.',
            
            'Designation.required' => 'La désignation est obligatoire.',
            
            'Type.required' => 'Le type est obligatoire.',
            
            'Quantite.required' => 'La quantité est obligatoire.',
            'Quantite.integer' => 'La quantité doit être un nombre entier.',
            
            'PrixUnitaire.required' => 'Le prix unitaire est obligatoire.',
            'PrixUnitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'PrixUnitaire.min' => 'Le prix unitaire doit être supérieur à 0.',
        ]);
        

        $materiel->update($fields);

        return response()->json(['materiel' => $materiel, 'user' => $materiel->user]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materiel $materiel)
    {
        Gate::authorize('modify' , $materiel);
        
        $materiel -> delete();

        return ['message' => 'Materiel supprimée !!'];
    }
}
