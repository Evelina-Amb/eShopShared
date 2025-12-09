<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle($request, Closure $next)
{
    // temp: use ?admin_id=1 in Postman
$user = \App\Models\User::find($request->admin_id);

if (!$user || $user->role !== 'admin') {
    return response()->json([
        'status' => 'error',
        'message' => 'Only admin can perform this action.'
    ], 403);
}

    return $next($request);
}

}
