<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

@extends('layouts.default', [
    'title' => 'Register'
])

@section('css')
    <style>
        h3 {
            margin: 2px 0;
        }

        h6 {
            margin: 2px 0;
            font-size: 12px;
        }

        input:not([type='radio']) {
            display: block;
            margin-bottom: 5px;
        }

        input[type='radio' i] {
            margin: 3px 3px 0 0;
        }

        label {
            margin-right: 5px;
        }
    </style>
@endsection

@section('js')
    @if (config('app.env') === 'production' && site_setting('registration_enabled'))
        {!! NoCaptcha::renderJs() !!}
    @endif
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top blue">Register</div>
            <div class="content">
                @if (!site_setting('registration_enabled'))
                    <span>Account creation is currently disabled.</span>
                @else
                    <div class="col-8-12">
                        <form action="{{ route('auth.register.authenticate') }}" method="POST">
                            @csrf
                            <h3 class="dark-gray-text">Username</h3>
                            <h6 class="light-gray-text">How will people recognize you?</h6>
                            <input type="username" name="username" placeholder="Username" required>
                            <h3 class="dark-gray-text">Password</h3>
                            <h6 class="light-gray-text">Only you will know this!</h6>
                            <input type="password" name="password" placeholder="Password" required>
                            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                            <h3 class="dark-gray-text">Email</h3>
                            <h6 class="light-gray-text">This must be valid so we can contact you!</h6>
                            <input type="email" name="email" placeholder="Email (optional)">
                            @if (config('app.env') === 'production')
                                <div class="col-1-1" style="margin-top:5px;">{!! NoCaptcha::display() !!}</div>
                            @endif
                            <div class="col-1-1">
                                <div style="padding-top:5px;"></div>
                                <button class="blue" type="submit">Register</button>
                            </div>
                            <div class="col-1-1">
                                <span class="gray-text" style="font-size:14px;">By signing up to {{ config('app.name') }}, you confirm that you have read and agree to the <a href="{{ route('info.terms') }}" class="dark-gray-text bold" target="_blank">Terms of Service</a>, as well as our <a href="{{ route('info.privacy') }}" class="dark-gray-text bold" target="_blank">Privacy Policy</a>.</span>
                            </div>
                        </form>
                    </div>
                    <div class="col-4-12" style="position:relative;min-height:310px;">
                        <div class="col-12-12" style="position:absolute;top:5px;right:5px;">
                            <div style="border-radius:5px;border:1px solid #D9D9D9;padding:5px;">
                                <h3 class="dark-gray-text">Already have an account?</h3>
                                <span class="light-gray-text" style="font-size:15px;">
                                    If you've forgotten your password go to <a href="{{ route('auth.forgot_password.index') }}" class="dark-gray-text bold">forgot password</a>.
                                    <br><br>
                                    To login, go to <a href="{{ route('auth.login.index') }}" class="dark-gray-text bold">login</a>.
                                    <br><br>
                                    Can't play? Go to <a href="{{ route('games.download') }}" class="dark-gray-text bold">download</a> and install the client!</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
