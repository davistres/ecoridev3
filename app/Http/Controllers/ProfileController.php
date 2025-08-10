<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
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
            'role' => ['required', 'string', 'in:Passager,Conducteur,Les deux'],
        ]);

        $user = $request->user();
        $user->role = $request->input('role');
        $user->save();

        return Redirect::route('dashboard')->with('status', 'role-updated');
    }

    /** Photo de profil */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

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
}