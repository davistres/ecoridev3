<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfilePhotoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RoleDriverRequest;
use App\Models\Voiture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return redirect()->route('home');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:18'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password
            ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard')->with('success', 'Votre profil a été mis à jour avec succès!');
    }

    /** Changement de role */
    public function newRole(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', Rule::in(['Passager', 'Conducteur', 'Les deux'])],
        ]);

        $user = $request->user();
        $newRole = $request->input('role');

        // Si un chauffeur redevient un simple passager, on doit effacer toutes ses données conducteur
        if (($user->role === 'Conducteur' || $user->role === 'Les deux') && $newRole === 'Passager') {
            DB::transaction(function () use ($user) {
                // Réini les préférences
                $user->pref_smoke = null;
                $user->pref_pet = null;
                $user->pref_libre = null;

                // Et supprimer toutes ses voitures
                Voiture::where('user_id', $user->user_id)->delete();
            });
        }

        $user->role = $newRole;
        $user->save();

        return Redirect::route('dashboard')->with('status', 'role-updated');
    }

    /** Photo de profil */
    public function updatePhoto(ProfilePhotoRequest $request): RedirectResponse
    {
        $user = $request->user();
        $photo = $request->file('profile_photo');

        $user->photo = file_get_contents($photo->getRealPath());
        $user->phototype = $photo->getMimeType();
        $user->save();

        return Redirect::route('dashboard')->with('status', 'photo-updated');
    }

    /** Suppr photo de profil*/
    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->photo = null;
        $user->phototype = null;
        $user->save();

        return Redirect::route('dashboard')->with('status', 'photo-deleted');
    }

    public function updatePassword(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'password-updated']);
        }

        return Redirect::route('dashboard')->with('status', 'password-updated');
    }

    public function driverInfo(RoleDriverRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Sécu en plus mais normalement pas nécessaire
        if ($user->role !== 'Passager') {
            return redirect()->back()->withErrors(['role' => 'Seuls les passagers peuvent devenir conducteurs.']);
        }

        try {
            DB::transaction(function () use ($user, $validated, $request) {
                // Mise à jour du role et des préférences
                $user->pref_smoke = $validated['pref_smoke'];
                $user->pref_pet = $validated['pref_pet'];
                $user->pref_libre = $validated['pref_libre'];
                $user->role = $validated['new_role'];

                // Photo (non obligatoire)
                if ($request->hasFile('profile_photo')) {
                    $photo = $request->file('profile_photo');
                    $user->photo = file_get_contents($photo->getRealPath());
                    $user->phototype = $photo->getMimeType();
                }

                $user->save();

                // Création du premier véhicule (obligatoire pour devenir conducteur)
                Voiture::create([
                    'user_id' => $user->user_id,
                    'immat' => $validated['immat'],
                    'date_first_immat' => $validated['date_first_immat'],
                    'brand' => $validated['brand'],
                    'model' => $validated['model'],
                    'color' => $validated['color'],
                    'n_place' => $validated['n_place'],
                    'energie' => $validated['energie'],
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de votre profil. Veuillez réessayer.')->withInput();
        }

        return Redirect::route('dashboard')->with('status', 'role-updated-to-driver');
    }
}