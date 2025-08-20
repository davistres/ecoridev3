<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechargeRequest;
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

        return view('dashboard', [
            'user' => $user,
            'voitures' => $voitures,
        ]);
    }

    public function recharge(RechargeRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->n_credit += (int) $request->input('amount');
        $user->save();

        return response()->json(['new_credit_balance' => $user->n_credit]);
    }
}
