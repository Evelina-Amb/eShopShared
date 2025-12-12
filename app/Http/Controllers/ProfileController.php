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

    /**
     * Update the user's profile information.
     */
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
        $user = $request->user()->load('address.city');

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

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'vardas'     => ['nullable', 'string', 'max:255'],
            'pavarde'    => ['nullable', 'string', 'max:255'],

            'el_pastas' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'el_pastas')->ignore($user->id),
            ],

            'telefonas'       => ['nullable', 'string', 'max:50'],
            'business_email' => ['nullable', 'email', 'max:255'],

            'role' => ['nullable', 'string'],

            'city_id' => ['nullable', 'exists:city,id'],
            'gatve'   => ['nullable', 'string', 'max:255'],
            'namo_nr' => ['nullable', 'string', 'max:50'],
            'buto_nr' => ['nullable', 'string', 'max:50'],
        ]);

        // Prevent disabling seller if listings exist
        if ($user->listings()->exists() && !$request->has('role')) {
            return back()->withErrors([
                'role' => 'You cannot disable seller mode because you have active listings.',
            ]);
        }

        // Seller requirements
        if ($request->has('role')) {
            $request->validate([
                'country_id'     => 'required',
                'city_id'        => 'required',
                'business_email' => 'required_without:telefonas',
                'telefonas'      => 'required_without:business_email',
            ], [
                'country_id.required' => 'Country is required to become a seller.',
                'city_id.required'    => 'City is required to become a seller.',
                'business_email.required_without' => 'Provide at least one contact method (email or phone).',
                'telefonas.required_without'      => 'Provide at least one contact method (email or phone).',
            ]);
        }

        /**
         *  SAVE NON-EMAIL USER DATA
         */
        $user->update([
            'vardas'         => $validated['vardas'] ?? $user->vardas,
            'pavarde'        => $validated['pavarde'] ?? $user->pavarde,
            'telefonas'      => $validated['telefonas'] ?? $user->telefonas,
            'business_email' => $validated['business_email'] ?? $user->business_email,
            'role'           => $request->has('role') ? 'seller' : 'buyer',
        ]);

        /**
         *  ADDRESS (create or update)
         */
        if (
            !empty($validated['city_id']) ||
            !empty($validated['gatve']) ||
            !empty($validated['namo_nr']) ||
            !empty($validated['buto_nr'])
        ) {
            $address = $user->address ?? new Address();

            $address->fill([
                'city_id' => $validated['city_id'] ?? $address->city_id,
                'gatve'   => $validated['gatve'] ?? $address->gatve,
                'namo_nr' => $validated['namo_nr'] ?? $address->namo_nr,
                'buto_nr' => $validated['buto_nr'] ?? $address->buto_nr,
            ]);

            $address->save();

            if (!$user->address_id) {
                $user->address_id = $address->id;
                $user->save();
            }
        }

        /**
         *  EMAIL CHANGE 
         */
        if ($validated['el_pastas'] !== $user->el_pastas) {
            $user->pending_email = $validated['el_pastas'];
            $user->pending_email_token = Str::random(60);
            $user->save();

            Mail::to($user->pending_email)->send(
                new \App\Mail\VerifyNewEmail($user)
            );

            return back()->with('status', 'Email verification sent.');
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

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::defaults()
            ],
        ]);

        $request->user()->update([
            'slaptazodis' => $request->password,
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Verify new email address.
     */
    public function verifyNewEmail($token)
    {
        $user = User::where('pending_email_token', $token)->firstOrFail();

        $user->el_pastas = $user->pending_email;
        $user->pending_email = null;
        $user->pending_email_token = null;
        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('status', 'Your email has been updated and verified.');
    }
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

        return back()->with('status', 'password-updated');
    }

    public function verifyNewEmail($token)
    {
        $user = User::where('pending_email_token', $token)->first();

        if (!$user) {
            abort(404, 'Invalid verification link.');
        }

        // Update actual email
        $user->el_pastas = $user->pending_email;

        // Clear pending fields
        $user->pending_email = null;
        $user->pending_email_token = null;

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'Your email has been updated and verified.');
    }
}
