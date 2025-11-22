<?php
// step 3
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // step 4
        if ($locale = $request->route('locale')) {
            // return redirect()->route('setlocale', ['locale' => $locale]);
        } else {
            $locale = $request->session()->get('app_locale');
        }

        if ($locale) {
            App::setlocale($locale);
            URL::defaults(['locale' => $locale]);
        }
        return $next($request);
    }
}
