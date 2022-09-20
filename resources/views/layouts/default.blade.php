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

<!DOCTYPE html>
<html lang="en" class="theme-{{ Auth::user()->setting->theme ?? 'default' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($title) ? "{$title} - " . config('site.name') : config('site.name') }}</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">

    <!-- Meta -->
    <meta name="theme-color" content="{{ config('site.theme_color') }}">
    <link rel="shortcut icon" href="{{ config('site.icon') }}">
    <meta name="author" content="{{ config('site.name') }}">
    <meta name="description" content="Brick building, brick build together part piece construct make create set.">
    <meta name="keywords" content="{{ strtolower(config('site.name')) }}, {{ strtolower(str_replace(' ', '', config('site.name'))) }}, brick building, brick, build together, part, piece, construct, make, create, set">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <!-- OpenGraph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('site.name') }}">
    <meta property="og:title" content="{{ $title ?? config('site.name') }}">
    <meta property="og:description" content="Brick building, brick build together part piece construct make create set.">
    <meta property="og:image" content="{{ !isset($image) ? config('site.icon') : $image }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;1,500;1,600;1,700&display=swap">
    @yield('fonts')

    <!-- CSS -->
    <link rel="stylesheet" href="{{ theme_file() }}">
    <link rel="stylesheet" href="{{ asset('css/newtheme.css') }}">
    <style>#globalError:empty, .dropdown-content:not(.active) { display: none; }</style>
    @yield('css')
</head>
<body>
    @if (!request()->isMaintenancePage)
        <nav>
            <div class="primary">
                <div class="grid">
                    <div class="push-left">
                        <ul>
                            @if (request()->isMaintenancePage)
                                <li><a style="cursor:pointer;border-color:transparent!important;">{{ config('site.name') }}</a></li>
                            @else
                                <li><a href="{{ route('games.index') }}">Play</a></li>
                                <li><a href="{{ route('shop.index') }}">Shop</a></li>
                                <li><a href="{{ route('clans.index') }}">Clans</a></li>
                                <li><a href="{{ route('users.index', '') }}">Users</a></li>
                                <li><a href="{{ route('forum.index') }}">Forum</a></li>
                                <li><a href="{{ route('account.billing.index') }}">Membership</a></li>
                                @if (Auth::check() && Auth::user()->isStaff())
                                    <li>
                                        <a href="{{ route('admin.index') }}" target="_blank">
                                            Admin
                                            @if (pendingAssetsCount() > 0 || pendingReportsCount() > 0)
                                                <span class="nav-notif">
                                                    @if (pendingAssetsCount() > 0)
                                                        <span>(A: {{ number_format(pendingAssetsCount()) }})</span>
                                                    @endif

                                                    @if (pendingReportsCount() > 0)
                                                        <span>(R: {{ number_format(pendingReportsCount()) }})</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <div class="nav-user push-right" id="info">
                        @guest
                            <div class="username login-buttons">
                                <a href="{{ route('auth.login.index') }}" class="login-button">Login</a>
                                <a href="{{ route('auth.register.index') }}" class="register-button">Register</a>
                            </div>
                        @else
                            @if (!request()->isMaintenancePage)
                                <div class="info">
                                    <a href="{{ route('account.currency.index', '') }}" class="header-data" title="{{ number_format(Auth::user()->currency_bucks) }}">
                                        <span class="bucks-icon img-white"></span> {{ shorten_number(Auth::user()->currency_bucks) }}
                                    </a>
                                    <a href="{{ route('account.currency.index', '') }}" class="header-data" title="{{ number_format(Auth::user()->currency_bits) }}">
                                        <span class="bits-icon img-white"></span> {{ shorten_number(Auth::user()->currency_bits) }}
                                    </a>
                                    <a href="{{ route('account.inbox.index', '') }}" class="header-data">
                                        <span class="messages-icon img-white"></span> {{ number_format(Auth::user()->messageCount()) }}
                                    </a>
                                    <a href="{{ route('account.friends.index') }}" class="header-data">
                                        <span class="friends-icon img-white"></span> {{ number_format(Auth::user()->friendRequestCount()) }}
                                    </a>
                                </div>
                            @endif
                            <div class="username ellipsis" data-dropdown-open="logout">
                                <div class="username-holder ellipsis inline unselectable">{{ Auth::user()->username }}</div>
                                <i class="arrow-down img-white"></i>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
            @auth
                @if (!request()->isMaintenancePage)
                    <div class="secondary">
                        <div class="grid">
                            <div class="bottom-bar">
                                <ul>
                                    <li><a href="{{ route('home.dashboard') }}" id="pHome">Home</a></li>
                                    <li><a href="{{ route('account.settings.index') }}" id="pSettings">Settings</a></li>
                                    <li><a href="{{ route('account.character.index') }}" id="pAvatar">Avatar</a></li>
                                    <li><a href="{{ route('users.profile', Auth::user()->id) }}" id="pProfile">Profile</a></li>
                                    <li><a href="{{ route('games.download') }}" id="pDownload">Download</a></li>
                                    <li>
                                        <a href="{{ route('account.trades.index') }}" id="pTrades">
                                            <span>Trades</span>
                                            @if (Auth::user()->tradeCount() > 0)
                                                <span class="nav-notif">{{ number_format(Auth::user()->tradeCount()) }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li><a href="{{ route('games.creations') }}" id="pSets">Sets</a></li>
                                    <li><a href="{{ route('account.currency.index', '') }}" id="pCurrency">Currency</a></li>
                                    <li><a href="https://blog.hill-of-bricks.com" id="pBlog">Blog</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </nav>
    @endif

    @yield('before_content')

    <div class="main-holder grid">
        @if (!request()->isMaintenancePage && site_setting('alert_enabled') && site_setting('alert_message'))
            <div class="col-10-12 push-1-12">
                <div class="alert" style="background:{{ site_setting('alert_background_color') }};color:{{ site_setting('alert_text_color') }};">
                    {!! site_setting('alert_message') !!}
                </div>
            </div>
        @endif

        @if (request()->isMaintenanceEnabled && session()->has('maintenance_password'))
            <div class="col-10-12 push-1-12">
                <div class="alert error">
                    You are currently in maintenance mode. <a href="{{ route('maintenance.exit') }}"><b>[Exit]</b></a>
                </div>
            </div>
        @endif

        @if (!site_setting('item_purchases_enabled') && Str::startsWith(request()->route()->getName(), 'shop.'))
            <div class="col-10-12 push-1-12">
                <div class="alert warning">
                    Item purchases are temporarily unavailable. Items may be browsed but are unable to be purchased.
                </div>
            </div>
        @endif

        @if (session()->has('success_message'))
            <div class="col-10-12 push-1-12">
                <div class="alert success">
                    {!! session()->get('success_message') !!}
                </div>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="col-10-12 push-1-12">
                <div class="alert error">
                    @foreach ($errors->all() as $error)
                        <div>{!! $error !!}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="col-10-12 push-1-12">
            <div class="alert error" id="globalError"></div>
        </div>

        @if (Auth::check() && !Auth::user()->hasVerifiedEmail() && !session()->has('no_thanks'))
            <div class="col-10-12 push-1-12">
                @if (!Auth::user()->email)
                    <div class="alert error">
                        You don't have an email attached to your account!
                        <a href="{{ route('account.settings.index') }}" class="button small green" style="margin-right:15px;margin-left:10px;">Add One</a>
                        <a href="{{ route('account.verify.cancel') }}" class="button small red">No thanks</a>
                    </div>
                @else
                    <div class="alert success">
                        @if (!Auth::user()->hasSentEmail())
                            You need to verify your email!
                            <a href="{{ route('account.verify.send') }}" class="button small red" style="margin-right:15px;margin-left:10px;">Send Email</a>
                            <a href="{{ route('account.verify.cancel') }}" class="button small red">No thanks</a>
                        @else
                            Verify your email {{ preg_replace('/[^@]+@([^\s]+)/', substr(Auth::user()->email, 0, 3) . '********@$1', Auth::user()->email) }}
                            <a href="{{ route('account.settings.index') }}" class="button small red">Change Email</a>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </div>

    @if (!request()->isMaintenancePage)
        <footer>
            <div>© {{ date('Y') }} {{ config('site.name') }}. All rights reserved.</div>
            <a href="{{ route('info.terms') }}">Terms of Service</a>
            <span>|</span>
            <a href="{{ route('info.privacy') }}">Privacy Policy</a>
            <span>|</span>
            <a href="{{ route('info.staff') }}">Staff</a>
        </footer>
    @endif

    @auth
        <div class="dropdown-content logout-dropdown" data-dropdown="logout">
            <div class="dropdown-arrow"></div>
            <ul>
                <li>
                    <a onclick="$('#logoutForm').submit()">Logout</a>
                </li>
            </ul>
            <form method="POST" action="{{ route('auth.logout') }}" id="logoutForm">@csrf</form>
        </div>
    @endauth

    <!-- JS -->
    <script src="{{ js_file('bundle') }}"></script>
    <script src="{{ js_file('app') }}"></script>
    @yield('js')
</body>
</html>
