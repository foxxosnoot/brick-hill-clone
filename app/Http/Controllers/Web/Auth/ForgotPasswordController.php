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

namespace App\Http\Controllers\Web\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordReset as PasswordResetMail;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('web.auth.forgot_password.index');
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', '=', $request->email);

        if ($user->exists() && $user->first()->hasVerifiedEmail()) {
            $token = Str::random(50);
            $passwordReset = PasswordReset::where('email', '=', $request->email)->orderBy('created_at', 'DESC');

            if ($passwordReset->exists() && (strtotime($passwordReset->first()->created_at) + 600) > time())
                return back()->withErrors(['You have already sent an email in the last 5 minutes.']);

            try {
                $passwordReset = new PasswordReset;
                $passwordReset->email = $request->email;
                $passwordReset->token = $token;
                $passwordReset->save();

                Mail::to($request->email)->send(new PasswordResetMail($token));
            } catch (\Exception $err) {
                return back()->withErrors(['Unable to send password reset email.']);
            }
        }

        return back()->with('success_message', 'If the email is valid you will receive further instructions.');
    }

    public function change($token)
    {
        $passwordReset = PasswordReset::where('token', '=', $token);

        if (!$passwordReset->exists() || (strtotime($passwordReset->first()->created_at) + 3600) < time())
            return redirect()->route('auth.forgot_password.index')->withErrors(["This token has expired or doesn't exist."]);

        $passwordReset = $passwordReset->first();
        $user = User::where('email', '=', $passwordReset->email)->first();

        return view('web.auth.forgot_password.change')->with([
            'passwordReset' => $passwordReset,
            'user' => $user
        ]);
    }

    public function finish(Request $request)
    {
        $passwordReset = PasswordReset::where('token', '=', $request->token);

        if (!$passwordReset->exists() || (strtotime($passwordReset->first()->created_at) + 3600) < time())
            return redirect()->route('auth.forgot_password.index')->withErrors(["This token has expired or doesn't exist."]);

        $this->validate($request, [
            'password' => ['required', 'confirmed', 'min:6', 'max:255']
        ]);

        $passwordReset = $passwordReset->first();

        $user = User::where('email', '=', $passwordReset->email)->first();
        $user->timestamps = false;
        $user->password = bcrypt($request->password);
        $user->save();

        $passwordReset->delete();

        return redirect()->route('auth.login.index')->with('success_message', 'Account password changed.');
    }
}
