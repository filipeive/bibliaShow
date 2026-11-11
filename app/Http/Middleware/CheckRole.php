<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Não autenticado.'], 401);
            }
            return redirect()->route('login')->with('error', 'Inicie sessão para aceder a esta funcionalidade.');
        }

        // admin_geral tem acesso global
        if ($user->papel === 'admin_geral') {
            return $next($request);
        }

        if (!in_array($user->papel, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Acesso negado para o seu perfil.'], 403);
            }
            abort(403, 'Acesso não autorizado para o papel: ' . $user->papel);
        }

        return $next($request);
    }
}
