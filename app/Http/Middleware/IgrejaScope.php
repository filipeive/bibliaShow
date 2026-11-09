<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IgrejaScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && method_exists($user, 'igreja_id')) {
            // Scope queries to user's church for models that support it
            $request->merge(['igreja_id' => $user->igreja_id]);
        }

        return $next($request);
    }
}
