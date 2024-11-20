<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materiel extends Model
{
    use HasFactory;

    protected $fillable = [
        'CodeMateriel',
        'Designation',
        'Type',
        'Quantite',
        'PrixUnitaire',
    ];

    // Indiquer que le modèle utilise une clé primaire différente
    protected $primaryKey = 'CodeMateriel';
    
    // Indiquer que la clé primaire n'est pas incrémentale
    public $incrementing = false;

    // Indiquer le type de la clé primaire (si ce n'est pas un entier)
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

        // Dans le modèle Materiel.php
    public function sorties()
    {
        return $this->hasMany(Sortie::class, 'CodeMateriel', 'CodeMateriel'); // Assure-toi que les clés sont correctes
    }
    public function receptions()
    {
        return $this->hasMany(Reception::class, 'CodeMateriel', 'CodeMateriel'); // Assure-toi que les clés sont correctes
    }


    public function getRouteKeyName()
    {
        return 'CodeMateriel'; // Utilisez la colonne CodeMateriel pour la liaison
    }
}

