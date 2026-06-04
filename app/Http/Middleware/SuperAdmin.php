<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (
            $user &&
            $user->role == 1 &&
            $user->email === config('admin.superadmin_email')
        ) {
            return $next($request);
        }

        abort(403, 'Unauthorized Call Developer');
    }
}