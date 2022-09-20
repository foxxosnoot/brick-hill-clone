<?php
/**
 * MIT License
 *
 * Copyright (c) 2021-2022 FoxxoSnoot
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SiteSettingChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route()->getName();
        $middleware = $request->route()->middleware();
        $api = $request->is('api') || $request->is('api/*');
        $isPOST = $request->isMethod('post');
        $maintenancePasswords = config('site.maintenance_passwords');
        $requireAuthCode = config('site.require_auth_code');
        $isMaintenanceEnabled = site_setting('maintenance_enabled');
        $isMaintenancePage = true;

        if (!$isMaintenanceEnabled || ($isMaintenanceEnabled && session()->has('maintenance_password')))
            $isMaintenancePage = false;

        if (($isMaintenanceEnabled && !in_array(session('maintenance_password'), $maintenancePasswords)) || !$isMaintenanceEnabled && session()->has('maintenance_password'))
            session()->forget('maintenance_code');

        if (!$requireAuthCode || session()->has('auth_code')) {
            if ($isMaintenanceEnabled && !session()->has('maintenance_password') && $api)
                return response()->json([
                    'error' => [
                        'message' => 'Maintenance is currently enabled',
                        'prettyMessage' => 'Maintenance is currently enabled'
                    ]
                ]);

            if ($isMaintenanceEnabled && !session()->has('maintenance_password') && !Str::startsWith($route, ['maintenance.', /*'auth.'*/]))
                return redirect()->route('maintenance.index');
        } else {
            if ($isMaintenanceEnabled && $api)
                return response()->json([
                    'error' => [
                        'message' => 'Maintenance is currently enabled',
                        'prettyMessage' => 'Maintenance is currently enabled'
                    ]
                ]);
        }

        if (!$isMaintenanceEnabled && Str::startsWith($route, 'maintenance.'))
            return $this->disabled('Maintenance', $middleware, $isPOST);

        if (!site_setting('item_purchases_enabled') && $route == 'shop.purchase')
            return $this->disabled('Shop', $middleware, $isPOST);

        if (!site_setting('forum_enabled') && Str::startsWith($route, 'forum.'))
            return $this->disabled('Forum', $middleware, $isPOST);

        if (!site_setting('item_creation_enabled') && Str::startsWith($route, 'shop.create.'))
            return $this->disabled('Create', $middleware, $isPOST);

        if (!site_setting('avatar_editor_enabled') && Str::startsWith($route, 'account.character.'))
            return $this->disabled('Customize', $middleware, $isPOST);

        if (!site_setting('trading_enabled') && Str::startsWith($route, 'account.trades.'))
            return $this->disabled('Trades', $middleware, $isPOST);

        if (!site_setting('clans_enabled') && (Str::startsWith($route, 'clans.')))
            return $this->disabled('Clans', $middleware, $isPOST);

        if (!site_setting('settings_enabled') && Str::startsWith($route, 'account.settings.'))
            return $this->disabled('Settings', $middleware, $isPOST);

        if (!site_setting('registration_enabled') && $route == 'auth.register.authenticate')
            return $this->disabled('Register', $middleware, $isPOST);

        $request->merge(compact('isMaintenanceEnabled', 'isMaintenancePage'));

        return $next($request);
    }

    public function disabled($feature, $middleware, $isPOST)
    {
        if ($feature == 'Maintenance')
            abort(403);
        else if (!Auth::check() && in_array('auth', $middleware))
            return redirect()->route('auth.login.index');
        else if (Auth::check() && in_array('guest', $middleware))
            return redirect()->route('home.dashboard');
        else if ($isPOST || ((!Auth::check() || (Auth::check() && !Auth::user()->isStaff())) && in_array('staff', $middleware)))
            return abort(404);

        return response()->view('errors.feature_disabled', [
            'title' => $feature
        ]);
    }
}
