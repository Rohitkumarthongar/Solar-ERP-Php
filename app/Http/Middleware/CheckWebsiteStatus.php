<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckWebsiteStatus
{
    public function handle(Request $request, Closure $next)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $status = $settings['website_status'] ?? 'enabled';
        
        if ($status === 'disabled') {
            return response()->view('web.disabled', compact('settings'));
        }

        return $next($request);
    }
}
