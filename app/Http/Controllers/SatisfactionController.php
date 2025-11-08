<?php

namespace App\Http\Controllers;

use App\Models\Satisfaction;
use App\Models\Covoiturage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SatisfactionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'satisfaction_id' => 'required|exists:satisfaction,satisfaction_id',
            'feeling' => 'required|boolean',
            'comment' => 'nullable|string',
            'review' => 'nullable|string|max:1200',
            'note' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $satisfaction = Satisfaction::where('satisfaction_id', $request->satisfaction_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$satisfaction) {
            return response()->json([
                'success' => false,
                'message' => 'Formulaire de satisfaction non trouvé.'
            ], 404);
        }

        if ($satisfaction->date !== null && $satisfaction->feeling !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce formulaire a déjà été complété.'
            ], 400);
        }

        if ($request->feeling == 0 && empty($request->comment)) {
            return response()->json([
                'success' => false,
                'message' => 'Le commentaire est obligatoire si vous n\'êtes pas satisfait.'
            ], 422);
        }

        if (!empty($request->review) && empty($request->note)) {
            return response()->json([
                'success' => false,
                'message' => 'La note est obligatoire si vous laissez un avis.'
            ], 422);
        }

        $satisfaction->feeling = $request->feeling;
        $satisfaction->comment = $request->comment;
        $satisfaction->review = $request->review;
        $satisfaction->note = $request->note;
        $satisfaction->date = Carbon::now()->toDateString();
        $satisfaction->save();

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre retour !'
        ]);
    }
}

