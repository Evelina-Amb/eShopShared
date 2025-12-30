<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSeller
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // must be seller
        if ($user->role !== 'seller') {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please complete your seller information.');
        }

        // must have at least one public contact
        if (!$user->business_email && !$user->telefonas) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please add at least one public contact method.');
        }

        // must have address
        if (
            !$user->address ||
            !$user->address->city_id
        ) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please select your city and address.');
        }

        // must have Stripe connected and onboarded
        if (
            !$user->stripe_account_id ||
            !$user->stripe_onboarded
        ) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please finish Stripe onboarding before posting listings.');
        }

        return $next($request);
    }
}
