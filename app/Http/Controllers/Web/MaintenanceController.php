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

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    public function index()
    {
        if (session()->has('maintenance_password'))
            return redirect()->route('home.index');

        $jackpot = false;
        $users = ['aeo', 2, 3, 9, 5, 6];
        $randomUsers = [];
        $thumbnails = [];
        $i = 0;

        foreach ($users as $user) {
            $i++;

            if ($i > 4)
                continue;

            shuffle($users);
            $randomUsers[] = $users[0];
        }

        foreach ($randomUsers as $id) {
            if ($id == 'aeo') {
                $thumbnails[] = asset('images/aeo.png');
                continue;
            }

            $user = User::where('id', '=', $id);
            $thumbnails[] = ($user->exists()) ? $user->first()->thumbnail() : config('site.storage_url') . '/error/png_error.png';
        }

        if (Auth::check() && ($randomUsers[0] == 'aeo' && $randomUsers[1] == 'aeo' && $randomUsers[2] == 'aeo' && $randomUsers[3] == 'aeo')) {
            $jackpot = true;

            if (!Auth::user()->ownsAward(2))
                Auth::user()->giveAward(2);
        }

        return view('web.maintenance.index')->with([
            'thumbnails' => $thumbnails,
            'jackpot' => $jackpot
        ]);
    }

    public function authenticate(Request $request)
    {
        $maintenancePasswords = config('site.maintenance_passwords');

        if (session()->has('maintenance_password'))
            return back()->withErrors(['Already authenticated.']);

        if (!$request->key)
            return back()->withErrors(['Please provide a key.']);

        if (!in_array($request->key, $maintenancePasswords))
            return back()->withErrors(['Invalid key.']);

        session()->put('maintenance_password', $request->key);
        session()->save();

        return redirect()->route('home.index');
    }

    public function exit()
    {
        session()->forget('maintenance_password');

        return redirect()->route('maintenance.index');
    }
}
