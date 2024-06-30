<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasRetailSession
{
    public function handle(Request $request, Closure $next)
    {
        $session = retail_session();

        if (blank($session)) {
            return redirect(route('retail.session.create'));
        }

        if (session()->missing('retail_session_id')) {
            session(['retail_session_id' => $session->id]);
        }

        return $next($request);
    }
}
