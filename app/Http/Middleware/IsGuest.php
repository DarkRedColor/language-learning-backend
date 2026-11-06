<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsGuest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if ((Auth::check() && Auth::user()->hasRole(['admin','user','qa','project','content','developer','methodologist'])) || $request->route()->getName() === 'ask-permissions')
                return $next($request);
            else
                return redirect('permissions');
        } else {
            return abort(404);
        }
    }
}
