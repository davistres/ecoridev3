<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContactController extends Controller
{
    public function store(ContactFormRequest $request)
    {
        $validated = $request->validated();

        Contact::create([
            'nom' => $validated['nom'],
            'mail' => $validated['email'],
            'sujet' => $validated['sujet'],
            'message' => $validated['message'],
            'date_envoi' => Carbon::now(),
        ]);

        return redirect()->route('contact')->with('success', 'Votre message a bien été envoyé !');
    }
}
