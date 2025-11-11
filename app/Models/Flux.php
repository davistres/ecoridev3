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
            'user_id' => 1,
            'montant_init' => $adminCredits,
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

    private static function getAdminCredits()
    {
        return self::where('user_id', 1)
            ->where('type', 'paiement')
            ->sum('montant');
    }

    // Le paiement du conducteur peut être effectué?
    public static function processDriverPaymentForCarpool(Covoiturage $covoiturage)
    {
        // 1. On récupére la liste des passagers et leurs fomulaire de satisfaction
        $confirmedConfirmations = $covoiturage->confirmations()->where('statut', 'En cours')->get();
        $passengerIds = $confirmedConfirmations->pluck('user_id')->unique();

        if ($passengerIds->isEmpty()) {
            return; // Pas de passagers = pas de paiement à traiter.
        }

        $satisfactions = $covoiturage->satisfactions()->whereIn('user_id', $passengerIds)->get();

        // 2. Check si toutes les formulaires de satisfaction sont remplies et validées
        $allSurveysSubmitted = $satisfactions->count() >= $passengerIds->count();
        $allSurveysPositive = $satisfactions->where('feeling', 0)->isEmpty();

        $canProceedToPayment = false;

        if ($allSurveysSubmitted && $allSurveysPositive) {
            // Si dans les formulaires, feeling = 1, on peut payer directement
            $canProceedToPayment = true;
        } else if ($allSurveysSubmitted && !$allSurveysPositive) {
            // Sinon, si au moins un formulaire a un feeling = 0, il faut vérifier le statut de la table litige associée

            $negativeSatisfactionIds = $satisfactions->where('feeling', 0)->pluck('satisfaction_id');

            if ($negativeSatisfactionIds->isEmpty()) {
                // J'ajoute cela, juste au cas où il y ait des problèmes (par exemple: echec du process pour collecter ou analyser les formulaires de satisfaction)... C'est donc pour que le système soit plus robuste.
                $canProceedToPayment = false;
            } else {
                // Récupére les litiges associés à ces satisfactions négatives
                $litiges = \App\Models\Litige::whereIn('satisfaction_id', $negativeSatisfactionIds)->get();

                // Check si un litige a été créé pour chaque satisfaction négative
                if ($litiges->count() !== $negativeSatisfactionIds->count()) {
                    // Si il manque des litiges pour certaines satisfactions négatives => paiement bloqué.
                    $canProceedToPayment = false;
                } else {
                    // Tous les litiges sont résolus?
                    $allLitigesResolved = $litiges->where('statut_litige', 'Résolu')->count() === $litiges->count();

                    if (!$allLitigesResolved) {
                        // Tous les litiges ne sont pas résolus => paiement bloqué.
                        $canProceedToPayment = false;
                    } else {
                        // Tous les litiges sont résolus =>paiement ok!
                        $canProceedToPayment = true;
                    }
                }
            }
        } else {
            // Tous les formulaires de satisfaction ne sont pas soumis => paiement bloqué.
            $canProceedToPayment = false;
        }

        if (!$canProceedToPayment) {
            return; // Si toutesles conditions ne sont pas remplies => paiement bloqué.
        }

        // 3. Check les parts du conducteur non encore payées
        $driver = $covoiturage->user;
        $allConfIds = $confirmedConfirmations->pluck('conf_id');

        $paidConfIds = self::whereIn('conf_id', $allConfIds)
            ->where('type', 'paiement')
            ->where('user_id', $driver->user_id)
            ->pluck('conf_id');

        $unpaidParts = self::whereIn('conf_id', $allConfIds)
            ->where('type', 'part_conducteur')
            ->whereNotIn('conf_id', $paidConfIds)
            ->get();

        if ($unpaidParts->isEmpty()) {
            return; // Rien à payer.
        }

        // 4. Calcule le montant total et effectuer le paiement dans une transaction pour le conducteur
        \DB::transaction(function () use ($driver, $unpaidParts) {
            $totalAmountToPay = $unpaidParts->sum('result');
            $currentBalance = $driver->n_credit;

            // Maj le solde de crédit du conducteur
            $driver->n_credit += $totalAmountToPay;
            $driver->save();

            // Ici, on crée un flux 'paiement' pour chaque part non payée au conducteur... A quoi ça sert? C'est pour garder une trace détaillée de chaque paiement effectué au conducteur, lié à chaque confirmation spécifique. Tout cela afin de suivre son solde et de préparer son futur paiement.
            foreach ($unpaidParts as $part) {
                self::create([
                    'conf_id' => $part->conf_id,
                    'user_id' => $driver->user_id,
                    'montant_init' => $currentBalance,
                    'montant' => $part->result,
                    'result' => $currentBalance + $part->result,
                    'type' => 'paiement',
                    'date' => now(),
                ]);
                // Maj du solde initial pour les prochains enregistrement dans la table flux
                $currentBalance += $part->result;
            }
        });
    }
}
