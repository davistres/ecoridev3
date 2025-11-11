<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSatisfactionRequest;
use App\Models\Satisfaction;
use App\Models\Covoiturage;
use App\Models\Litige;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SatisfactionController extends Controller
{
    public function store(StoreSatisfactionRequest $request): JsonResponse
    {
        // La validation est maintenant gérée automatiquement par StoreSatisfactionRequest.

        $validated = $request->validated();

        $satisfaction = Satisfaction::where('satisfaction_id', $validated['satisfaction_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$satisfaction) {
            return response()->json([
                'success' => false,
                'message' => 'Formulaire de satisfaction non trouvé.'
            ], 404);
        }

        if (!($satisfaction->feeling == 0 && $satisfaction->comment === null)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce formulaire a déjà été complété.'
            ], 400);
        }

        $satisfaction->feeling = $validated['feeling'];
        $satisfaction->comment = $validated['comment'] ?? null;
        $satisfaction->review = $validated['review'] ?? null;
        $satisfaction->note = $validated['note'] ?? null;
        $satisfaction->date = Carbon::now()->toDateString();

        // Dans le formulaire de satisfaction, si l'utilisateur indique "feeling" = 0 (insatisfait) => création d'un litige
        if ($validated['feeling'] == 0) {
            Litige::create([
                'satisfaction_id' => $satisfaction->satisfaction_id,
                'date_Create' => now(),
                'conversation' => [
                    [
                        'auteur_id' => Auth::id(),
                        'auteur_role' => Auth::user()->role,
                        'message' => $validated['comment'],
                        'date' => now(),
                    ]
                ],
                'statut_litige' => 'En cours',
                'date_end' => null,
            ]);
        }

        $satisfaction->save();

        // Check le paiement du conducteur
        \App\Models\Flux::processDriverPaymentForCarpool($satisfaction->covoiturage);

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre retour !'
        ]);
    }
}