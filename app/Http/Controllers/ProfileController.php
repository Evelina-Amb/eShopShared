<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user()->load('Address.City');

        return view('profile.edit', [
            'user'      => $user,
            'countries' => \App\Models\Country::select('id', 'pavadinimas')
                            ->orderBy('pavadinimas')
                            ->get(),
            'cities'    => \App\Models\City::select('id', 'pavadinimas', 'country_id')
                            ->orderBy('pavadinimas')
                            ->get(),
        ]);
    }

    
   public function update(Request $request)
{
    $user = auth()->user();
    $wasSeller = $user->role === 'seller';

    $validated = $request->validate([
        'vardas'     => ['nullable', 'string', 'max:255'],
        'pavarde'    => ['nullable', 'string', 'max:255'],
        'el_pastas'  => [
            'required',
            'email',
            Rule::unique('users', 'el_pastas')->ignore($user->id),
        ],
        'telefonas'       => ['nullable', 'string', 'max:50'],
        'business_email'  => ['nullable', 'email'],
        'role'            => ['nullable', 'string'],
        'city_id'         => ['nullable', 'exists:city,id'],
        'gatve'           => ['nullable', 'string'],
        'namo_nr'         => ['nullable', 'string'],
        'buto_nr'         => ['nullable', 'string'],
    ]);

    // Seller rules
    if ($request->has('role')) {
        $request->validate([
            'city_id'        => 'required',
            'business_email' => 'required_without:telefonas',
            'telefonas'      => 'required_without:business_email',
        ]);
    }

    $emailChanged = $request->filled('el_pastas')
    && $request->el_pastas !== $user->el_pastas;

 $user->update([
    'vardas'         => $validated['vardas'] ?? $user->vardas,
    'pavarde'        => $validated['pavarde'] ?? $user->pavarde,
   'telefonas'      => $request->filled('telefonas')
                            ? $validated['telefonas']
                            : null,

    'business_email' => $request->filled('business_email')
                            ? $validated['business_email']
                            : null,
    'el_pastas'      => $emailChanged ? $user->el_pastas : $validated['el_pastas'],
]);
    
    if ($request->has('role')) {
    $user->role = 'seller';
    $user->save();
}
    // Address
    if ($validated['city_id'] ?? false) {
        $address = $user->address ?? new Address();
        $address->fill($validated);
        $address->save();

        if (!$user->address_id) {
            $user->address_id = $address->id;
            $user->save();
        }
    }

if ($emailChanged) {
    $user->pending_email = $validated['el_pastas'];
    $user->pending_email_token = Str::random(60);
    $user->save();

    Mail::to($validated['el_pastas'])
        ->send(new \App\Mail\VerifyNewEmail($user));

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with(
        'status',
        'Išsiuntėme patvirtinimo nuorodą į jūsų naują el. pašto adresą. Patvirtinkite jį, kad galėtumėte tęsti.'
    );
}
    return back()->with('status', 'profile-updated');
}
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = $request->user();

        $user->update([
            'slaptazodis' => $request->password,
        ]);

           Auth::logout();
        $request->session()->invalidate();
    $request->session()->regenerateToken();

         return redirect()->route('login')->with(
        'status',
        'Jūsų slaptažodis sėkmingai pakeistas. Prašome prisijungti iš naujo'
    );
    }

    public function verifyNewEmail($token)
    {
        $user = User::where('pending_email_token', $token)->first();

        if (!$user) {
            abort(404, 'Neteisinga patvirtinimo nuoroda.');
        }

        // Update actual email
        $user->el_pastas = $user->pending_email;

        // Clear pending fields
        $user->pending_email = null;
        $user->pending_email_token = null;

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Jūsų el. pašto adresas buvo atnaujintas ir patvirtintas.');
    }
}
