<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApiLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->getPreferredLanguage(['ru', 'en']) ?: 'ru';

        app()->setLocale($locale);

        return $next($request);
    }
}
