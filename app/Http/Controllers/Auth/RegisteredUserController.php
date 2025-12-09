<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\PendingRegistration;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailMail; 

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

   public function store(Request $request)
{
    PendingRegistration::where('expires_at', '<', now())->delete();
    $request->validate([
        'vardas'       => ['required', 'string', 'max:50'],
        'pavarde'      => ['required', 'string', 'max:50'],
        'el_pastas'    => ['required', 'string', 'email', 'max:100', 'unique:pending_registrations,el_pastas', 'unique:users,el_pastas'],
        'slaptazodis'  => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $token = bin2hex(random_bytes(32));

    PendingRegistration::create([
        'vardas'      => $request->vardas,
        'pavarde'     => $request->pavarde,
        'el_pastas'   => strtolower($request->el_pastas),
        'slaptazodis' => Hash::make($request->slaptazodis),
        'token'       => $token,
        'expires_at'  => now()->addMinutes(30),
    ]);

     session(['pending_email' => $request->el_pastas]);
    Mail::to($request->el_pastas)->send(new VerifyEmailMail($token));

    return redirect()->route('verify.notice');
}

 public function resend(Request $request)
    {
        $pending = PendingRegistration::where('el_pastas', session('pending_email'))->first();

        if (!$pending) {
            return redirect()->route('register')
                ->withErrors(['el_pastas' => 'No pending registration found.']);
        }

        $newToken = bin2hex(random_bytes(32));

        $pending->update([
            'token' => $newToken,
            'expires_at' => now()->addMinutes(30),
        ]);

        Mail::to($pending->el_pastas)->send(new VerifyEmailMail($newToken));

        return back()->with('status', 'link-sent');
    }

public function verify($token)
{
    $pending = PendingRegistration::where('token', $token)->first();

    if (!$pending || $pending->expires_at < now()) {
        if ($pending) {
        $pending->delete();
    }
        return redirect()->route('register')->withErrors([
            'el_pastas' => 'Verification link expired or invalid'
        ]);
    }

    $user = User::create([
        'vardas'      => $pending->vardas,
        'pavarde'     => $pending->pavarde,
        'el_pastas'   => $pending->el_pastas,
        'slaptazodis' => $pending->slaptazodis,
        'role'        => 'pirkejas',
    ]);

    $pending->delete();
     return redirect()->route('login')
        ->with('status', 'Your email has been verified. Please log in.');
}

}
