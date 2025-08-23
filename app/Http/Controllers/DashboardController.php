<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechargeRequest;
use App\Models\Covoiturage;
use App\Models\Voiture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /** Dashboard utilisateur */
    public function index(Request $request): View
    {
        $user = $request->user();
        $voitures = Voiture::where('user_id', $user->user_id)->get();
        $covoiturages = Covoiturage::where('user_id', $user->user_id)
            ->where('trip_completed', 0)
            ->where('cancelled', 0)
            ->orderBy('departure_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'voitures' => $voitures,
            'covoiturages' => $covoiturages,
        ]);
    }

    public function recharge(RechargeRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->n_credit += (int) $request->input('amount');
        $user->save();

        return response()->json([
            'success' => true,
            'new_balance' => $user->n_credit
        ]);
    }
}
