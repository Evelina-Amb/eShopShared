<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

public function store(Request $request)
{
    $request->validate([
        'el_pastas' => ['required', 'email'],
        'password'  => ['required'],
    ]);

    $credentials = [
        'el_pastas' => strtolower($request->el_pastas),
        'password'  => $request->password,
    ];

    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()->withErrors([
            'el_pastas' => __('Neteisingi prisijungimo duomenys.'),
        ]);
    }

    $request->session()->regenerate();

    $user = Auth::user();

    if ($user->role === 'admin') {
        return redirect()->to('/admin/shipments');
    }

    return redirect()->intended('/');
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
