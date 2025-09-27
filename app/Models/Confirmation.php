<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;

    protected $table = 'confirmation';

    protected $primaryKey = 'conf_id';

    public $timestamps = false;

    protected $fillable = [
        'covoit_id',
        'user_id',
        'statut',
        'n_conf',
    ];

    /** Relation table conf -> covoit */
    public function covoiturage()
    {
        return $this->belongsTo(Covoiturage::class, 'covoit_id', 'covoit_id');
    }

    /** Relation table conf -> user */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}