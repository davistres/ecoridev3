<?php

namespace App\Http\Controllers;

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
        return view('dashboard', [
            'user' => $request->user(),
        ]);
    }

    public function recharge(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', Rule::in([10, 20, 50, 100, 200])],
        ]);

        $user = Auth::user();
        $user->n_credit += (int) $request->input('amount');
        $user->save();

        return response()->json(['new_credit_balance' => $user->n_credit]);
    }
}
