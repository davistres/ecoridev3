<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satisfaction extends Model
{
    use HasFactory;

    protected $table = 'satisfaction';
    protected $primaryKey = 'satisfaction_id';
    public $timestamps = false; // Utilise 'date' au lieu de created_at/updated_at

    protected $fillable = [
        'user_id',
        'covoit_id',
        'feeling',
        'comment',
        'review',
        'note',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'feeling' => 'boolean',
    ];

    /**
     * Relation avec le covoiturage évalué
     */
    public function covoiturage()
    {
        return $this->belongsTo(Covoiturage::class, 'covoit_id', 'covoit_id');
    }

    /**
     * Relation avec l'utilisateur qui a donné la note (passager)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
