<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\LoginLink;

class StripeConnectController extends Controller
{
    public function connect(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
            abort(403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        if (!$user->stripe_account_id) {
            $account = Account::create([
                'type' => 'express',
                'country' => 'LT',
                'email' => $user->el_pastas,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);

            $user->forceFill([
                'stripe_account_id' => $account->id,
                'stripe_onboarded' => false,
            ])->save();
        }

        $link = AccountLink::create([
            'account' => $user->stripe_account_id,
            'refresh_url' => route('stripe.refresh'),
            'return_url' => route('stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect()->away($link->url);
    }

    public function refresh()
    {
        return redirect()->route('profile.edit')
            ->with('error', 'Please finish Stripe onboarding.');
    }

    public function return(Request $request)
    {
        $user = $request->user();

        if (!$user->stripe_account_id) {
            abort(400, 'Stripe account missing.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Account::retrieve($user->stripe_account_id);

        if ($account->charges_enabled) {
            $user->forceFill([
                'stripe_onboarded' => true,
            ])->save();
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Stripe connected successfully.');
    }

public function dashboard(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'seller') {
        abort(403);
    }

    if (!$user->stripe_account_id || !$user->stripe_onboarded) {
        return redirect()->route('profile.edit')
            ->with('error', 'Please finish Stripe onboarding first.');
    }

    Stripe::setApiKey(config('services.stripe.secret'));

    $loginLink = Account::createLoginLink($user->stripe_account_id);

    return redirect()->away($loginLink->url);
}

}
