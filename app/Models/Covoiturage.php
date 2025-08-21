<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Covoiturage extends Model
{
    use HasFactory;

    protected $table = 'covoiturage';

    protected $primaryKey = 'covoiturage_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'voiture_id',
        'departure_address',
        'arrival_address',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
        'available_seats',
        'price',
        'eco_travel',
        'trip_started',
        'trip_completed',
        'cancelled',
    ];

    /** Pour récupérer la voiture associé à ce covoit*/
    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id', 'voiture_id');
    }

    /** Récupérer le conducteur associé à ce covoit*/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}