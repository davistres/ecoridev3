<?php

// TABLE `flux` (
// `flux_id` bigint UNSIGNED NOT NULL,
// `conf_id` bigint UNSIGNED DEFAULT NULL,
// `user_id` bigint UNSIGNED NOT NULL,
// `montant_init` int NOT NULL,
// `montant` int NOT NULL,
// `result` int NOT NULL,
// `type` enum('reservation','part_plateforme','part_conducteur','paiement','bonus_inscription','remboursement','achat_crédit') NOT NULL,
// `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flux extends Model
{
    use HasFactory;

    protected $table = 'flux';

    protected $primaryKey = 'flux_id';

    public $timestamps = false;

    protected $fillable = [
        'conf_id',
        'user_id',
        'montant_init',
        'montant',
        'result',
        'type',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    // Confirmation (nullable)
    public function confirmation()
    {
        return $this->belongsTo(Confirmation::class, 'conf_id', 'conf_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Flux de type 'bonus_inscription' => à l'inscription d'un nouvel utilisateur
    public static function createBonusInscription($userId, $montant = 20)
    {
        return self::create([
            'conf_id' => null,
            'user_id' => $userId,
            'montant_init' => 0,
            'montant' => $montant,
            'result' => $montant,
            'type' => 'bonus_inscription',
            'date' => now(),
        ]);
    }

    // Flux de type 'achat_crédit' => recharge de crédits
    public static function createAchatCredit($userId, $montantInit, $montant)
    {
        return self::create([
            'conf_id' => null,
            'user_id' => $userId,
            'montant_init' => $montantInit,
            'montant' => $montant,
            'result' => $montantInit + $montant,
            'type' => 'achat_crédit',
            'date' => now(),
        ]);
    }

    // Flux de type 'reservation' et ses flux enfants => résa d'un covoit
    public static function createReservation($confId, $userId, $montantInit, $totalCost, $nSeats)
    {
        // 1. Flux parent 'reservation'
        $reservation = self::create([
            'conf_id' => $confId,
            'user_id' => $userId,
            'montant_init' => $montantInit,
            'montant' => $totalCost,
            'result' => $montantInit - $totalCost,
            'type' => 'reservation',
            'date' => now(),
        ]);

        // 2. Flux enfant 'part_plateforme' (calcul)
        $partPlateforme = self::create([
            'conf_id' => $confId,
            'user_id' => $userId,
            'montant_init' => $nSeats,
            'montant' => 2, // Toujours 2 crédits par place
            'result' => $nSeats * 2,
            'type' => 'part_plateforme',
            'date' => now(),
        ]);

        // 3. Flux enfant 'part_conducteur' (calcul)
        $partConducteur = self::create([
            'conf_id' => $confId,
            'user_id' => $userId,
            'montant_init' => $totalCost,
            'montant' => $partPlateforme->result,
            'result' => $totalCost - $partPlateforme->result,
            'type' => 'part_conducteur',
            'date' => now(),
        ]);

        // 4. Flux enfant 'paiement' pour l'admin (= plateforme)
        $adminCredits = self::getAdminCredits();
        self::create([
            'conf_id' => $confId,
            'user_id' => 15, // user_id pour l'admin => A CHANGER si je le met en ligne!!!!!!!! Mettre 1 est mieux!!! Donc, je dois supprimer le user_id 1.
            'montant_init' => $adminCredits, // Crédits actuels de l'admin
            'montant' => $partPlateforme->result,
            'result' => $adminCredits + $partPlateforme->result,
            'type' => 'paiement',
            'date' => now(),
        ]);

        return [
            'reservation' => $reservation,
            'part_plateforme' => $partPlateforme,
            'part_conducteur' => $partConducteur,
        ];
    }

    // On récupére adminCredits (le montant actuel des crédits de l'admin) => calcule la somme de tous les flux de type 'paiement' pour l'admin (pour le moment, user_id = 15)
    private static function getAdminCredits()
    {
        return self::where('user_id', 15)
            ->where('type', 'paiement')
            ->sum('montant');
    }
}