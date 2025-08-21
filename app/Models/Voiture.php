<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voiture extends Model
{
    use HasFactory;
    protected $table = 'voiture';

    protected $primaryKey = 'voiture_id';

    /** ERREUR!!!! Je dois indiquer cela pour que les timestamps ne soient gérés */
    public $timestamps = false;

    /** Les attributs utilisés */
    protected $fillable = [
        'user_id',
        'immat',
        'date_first_immat',
        'brand',
        'model',
        'color',
        'n_place',
        'energie',
    ];

    /** Relation Eloquent dans un modèle Laravel => lie le modèle Voiture à l'utilisateur */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /** Récupérer les covoit liés à la voiture*/
    public function covoiturages()
    {
        return $this->hasMany(Covoiturage::class, 'voiture_id', 'voiture_id');
    }
}