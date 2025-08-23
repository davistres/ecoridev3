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
        'add_dep_address',
        'postal_code_dep',
        'city_dep',
        'arrival_address',
        'add_arr_address',
        'postal_code_arr',
        'city_arr',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
        'max_travel_time',
        'n_tickets',
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