<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sortie extends Model
{
    use HasFactory;

    protected $fillable = [
        'BonSortie',
        'CodeMateriel',
        'QuantiteSortant',
        'Destinataire',
        'DateSortie',
    ];

    // Indiquer que le modèle utilise une clé primaire différente
    protected $primaryKey = 'BonSortie';
    
    // Indiquer que la clé primaire n'est pas incrémentale
    public $incrementing = false;

    // Indiquer le type de la clé primaire (si ce n'est pas un entier)
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRouteKeyName()
    {
        return 'BonSortie'; // Utilisez la colonne CodeMateriel pour la liaison
    }
}

