<?php
namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminBootstrapController extends Controller
{
    public function promote(Request $request, User $user)
    {
        // Must be logged in
        if (!auth()->check()) {
            abort(403);
        }

        // Token check (server-side secret)
        if ($request->query('token') !== config('app.admin_bootstrap_token')) {
            abort(403);
        }

        // Promote
        $user->update(['role' => 'admin']);

        return response()->json([
            'ok' => true,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);
    }
}
