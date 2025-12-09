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
    
        if ($user->role !== 'seller') {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Please complete your seller information before posting listings.');
        }

        return $next($request);
    }

}
