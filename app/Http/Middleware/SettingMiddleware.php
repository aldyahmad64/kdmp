<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Symfony\Component\HttpFoundation\Response;

class SettingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Filament::getPanel('admin')->brandName(Setting::getWebName());
        Filament::getPanel('admin')->spa(Setting::getSpa());
        Filament::getPanel('admin')->brandLogo(function () {
            $data = [
                'web_name' => Setting::getWebName(),
                'web_logo' => 'storage/' . Setting::getLogo()
            ];
            return view('filament.admin.logo', $data);
        });
        Filament::getPanel('admin')->favicon('storage/' . Setting::getLogo());
        Filament::getPanel('admin')->colors(Setting::getColor());

        return $next($request);
    }
}
