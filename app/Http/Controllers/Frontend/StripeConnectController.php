<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;

class StripeConnectController extends Controller
{
    public function connect(Request $request)
    {
        $user = $request->user();

        // Must be seller
        if ($user->role !== 'seller') {
            abort(403, 'Only sellers can connect Stripe');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create Stripe account once
        if (!$user->stripe_account_id) {
           $account = Account::create([
    'type' => 'standard',
    'country' => 'LT',
    'email' => $user->el_pastas,

    // REQUIRED for marketplaces
    'capabilities' => [
        'card_payments' => ['requested' => true],
        'transfers'     => ['requested' => true],
    ],
]);


            $user->update([
                'stripe_account_id' => $account->id,
                'stripe_onboarded' => false,
            ]);
        }

        // Create onboarding link
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
        return redirect()->route('stripe.connect')
            ->with('error', 'Please finish Stripe onboarding to sell.');
    }

    public function return(Request $request)
    {
        $user = $request->user();

        $user->update([
            'stripe_onboarded' => true,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Stripe connected! You can now sell.');
    }
}
