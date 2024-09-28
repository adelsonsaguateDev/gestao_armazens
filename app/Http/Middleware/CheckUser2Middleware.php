<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class CheckUser2Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'admin' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'gestor' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'fiel de armazem' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'encomenda' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'inventariante' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) == 'gestor_vendedor' ) {
            return $next($request);
        } elseif (implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'admin' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'gestor' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'fiel de armazem' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'encomenda' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'inventariante' || implode(' | ', auth()->user()->permissoes()->pluck('nome')->toArray()) != 'gestor_vendedor') {
            return response(view('errors.403'));
        }
    }
}
