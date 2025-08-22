<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Voiture;

class User extends Authenticatable
{
    /** Ici aussi, faire les DocBlock!!!!!!!!!!!!!!!!!!!!!!!!!!!! Suivre le modèle de ce qui a été fait automatiqquement par Laravel!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** Par défaut, la clé primaire est id. Donc je dois spécifier que la mienne est user_id. */
    protected $primaryKey = 'user_id';

    /** Dans ma base de données, la table users n'a pas de colonnes created_at et updated_at. Donc, je dois mettre:*/
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'n_credit',
        'photo',
        'phototype',
        'pref_smoke',
        'pref_pet',
        'pref_libre',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /** Dans le header j'ai déjà implémenté isAdmin() pour afficher ADMIN à la place de name... Je dois donc ajouter cette fonction.*/
    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }
    /** TODO: si c'est un employé qui se connecte!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!*/

    /**Récupère les voitures de l'utilisateur*/
    public function voitures()
    {
        return $this->hasMany(Voiture::class, 'user_id', 'user_id');
    }
}