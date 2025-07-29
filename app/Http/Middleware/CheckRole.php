<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verificar si el usuario tiene el rol requerido
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // Verificar si el usuario tiene el rol requerido
        if (auth()->user()->role !== $role) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}