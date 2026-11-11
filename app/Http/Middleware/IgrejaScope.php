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

        if ($user && isset($user->igreja_id)) {
            // Injeta o igreja_id no request apenas se o usuário for membro/admin da igreja (evita impersonação)
            if ($user->papel !== 'admin_geral' || !$request->has('igreja_id')) {
                $request->merge(['igreja_id' => $user->igreja_id]);
            }
        }

        return $next($request);
    }
}
