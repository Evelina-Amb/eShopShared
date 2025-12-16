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

        if ($user->role !== 'seller') {
            abort(403, 'Only sellers can connect Stripe');
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

            $user->update([
                'stripe_account_id' => $account->id,
                'stripe_onboarded' => false,
            ]);
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
        return redirect()->route('stripe.connect')
            ->with('error', 'Please finish Stripe onboarding to sell.');
    }

    public function return(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Account::retrieve($request->user()->stripe_account_id);

        $request->user()->update([
            'stripe_onboarded' => $account->charges_enabled && $account->payouts_enabled,
        ]);

        return redirect()->route('profile.edit')
            ->with(
                $account->charges_enabled && $account->payouts_enabled
                    ? 'success'
                    : 'warning',
                $account->charges_enabled && $account->payouts_enabled
                    ? 'Stripe connected! You can now sell.'
                    : 'Stripe setup incomplete. Please finish onboarding.'
            );
    }
}
