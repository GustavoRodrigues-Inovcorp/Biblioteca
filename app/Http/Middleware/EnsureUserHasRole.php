<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Utilizador não autenticado.');
        }

        if ($roles === []) {
            abort(403, 'Nenhum role permitido foi definido.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Não tem permissão para aceder a este recurso.');
        }

        return $next($request);
    }
}
