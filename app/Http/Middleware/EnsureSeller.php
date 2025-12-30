<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
  public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Must be seller
        if ($user->role !== 'seller') {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please complete your seller information before posting listings.');
        }

        // Must have at least one public contact method
        if (!$user->business_email && !$user->telefonas) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please add at least one public contact method.');
        }

        // Must have address country
       if (!$user->address || !$user->address->city_id) {
    return redirect()
        ->route('profile.edit')
        ->with('error', 'Please select your city.');
}

        if (!$user->stripe_account_id) {
            return redirect()
                ->route('stripe.connect')
                ->with('error', 'You must connect Stripe before posting listings.');
        }

        return $next($request);
    }

}
