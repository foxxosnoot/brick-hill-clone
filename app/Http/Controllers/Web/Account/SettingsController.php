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

namespace App\Http\Controllers\Web\Account;

use Illuminate\Http\Request;
use App\Models\UsernameHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $email = preg_replace('/[^@]+@([^\s]+)/', substr(Auth::user()->email, 0, 3) . '********@$1', Auth::user()->email);
        $themes = config('themes.list');

        return view('web.account.settings')->with([
            'email' => $email,
            'themes' => $themes
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $setting = $user->setting;

        switch ($request->type) {
            case 'username':
                if ($request->username == $user->username)
                    return back()->withErrors(['This already is your username.']);

                $price = config('site.username_change_price');
                $usernameHistory = UsernameHistory::where('username', '=', $request->username);

                $this->validate($request, [
                    'username' => ['min:3', 'max:26', 'regex:/\\A[a-z\\d]+(?:[.-][a-z\\d]+)*\\z/i', 'unique:users'],
                ], [
                    'username.unique' => 'Username has already been taken.'
                ]);

                if ($usernameHistory->exists() && $usernameHistory->first()->user_id != $user->id)
                    return back()->withErrors(['Username has already been taken.']);

                if ($user->currency_bucks < $price)
                    return back()->withErrors(["You need at least {$price} bucks to change your username."]);

                $usernameHistory = new UsernameHistory;
                $usernameHistory->user_id = $user->id;
                $usernameHistory->ip = $user->lastIP();
                $usernameHistory->username = $user->username;
                $usernameHistory->save();

                $user->username = $request->username;
                $user->currency_bucks -= $price;
                $user->save();

                return back()->with('success_message', 'Username has been changed.');
            case 'password':
                $this->validate($request, [
                    'current_password' => ['required'],
                    'new_password' => ['required', 'confirmed', 'min:6', 'max:255']
                ]);

                if (!password_verify($request->current_password, $user->password))
                    return back()->withErrors(['Current password is incorrect.']);

                $user->password = bcrypt($request->new_password);
                $user->save();

                return back()->with('success_message', 'Password has been changed.');
            case 'email':
                $request->email = $request->new_email;
                $emailDomain = substr(strrchr($request->email, '@'), 1);

                $this->validate($request, [
                    'email' => ['email', 'max:255', 'unique:users'],
                    'password' => ['required']
                ]);

                if ($user->email && strtolower($request->current_email ?? '') != strtolower($user->email))
                    return back()->withErrors(['Current email is invalid.']);

                if (!password_verify($request->password, $user->password))
                    return back()->withErrors(['Password is incorrect.']);

                if (!in_array($emailDomain, config('email_whitelist')))
                    return back()->withErrors(['Sorry, this email cannot be used. Please try another.']);

                $user->email = $request->email;
                $user->email_verified_at = null;
                $user->save();

                return back()->with('success_message', 'Check your email to verify the change.');
            case 'theme':
                $themes = config('themes.list');

                if (!array_key_exists($request->theme, $themes))
                    return back()->withErrors(['Invalid theme.']);

                if (!$themes[$request->theme]['available'] && $user->setting->theme != $request->theme)
                    return back()->withErrors(['This theme is no longer available.']);

                $settings = $user->setting;
                $settings->theme = $request->theme;
                $settings->save();

                return back()->with('success_message', 'Theme has been updated.');
            case 'description':
                $this->validate($request, [
                    'description' => ['max:6000']
                ]);

                $user->description = $request->description;
                $user->save();

                return back()->with('success_message', 'Description has been updated.');
            default:
                abort(404);
        }
    }
}
