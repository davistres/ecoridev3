<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

// Je dois aussi essayer de faire des DocBlocks dans les autres fichiers de MODELS.

class Litige extends Model
{
    /**
     * Litige doit utiliser la connexion MongoDB.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * Dans MongoDB, une collection est équivalente à une table dans MySQL.
     *
     * Le modèle Litige utilise la collection 'litiges'.
     *
     * @var string
     */
    protected $collection = 'litiges';

    /**
     * La clé primaire pour la collection Litige est '_id'.
     *
     * @var string
     */
    protected $primaryKey = '_id';

    /**
     * La propriété $fillable définie les attributs authorisés. C'est une sécurisé pour empêcher les utilisateurs malveillants
     *
     * Ici, les champs autorisés pour la création ou la mise à jour d'un Litige sont :
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'satisfaction_id',
        'date_Create',
        'conversation',
        'statut_litige',
        'date_end',
    ];

    /**
     * $casts permet de convertir automatiquement les données de la base de données dans des types de données PHP appropriés.
     *
     * Les attributs qui doivent être convertis sont :
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_Create' => 'datetime',
        'date_end' => 'datetime',
        'conversation' => 'array',
    ];
}
