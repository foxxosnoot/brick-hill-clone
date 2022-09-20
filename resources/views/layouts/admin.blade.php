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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($title) ? "{$title} - " . config('site.name') . ' Administration' : config('site.name') . ' Administration' }}</title>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">

    <!-- Meta -->
    <link rel="shortcut icon" href="{{ config('site.icon') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <!-- Fonts -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.3/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;1,500;1,600;1,700&amp;display=swap">
    @yield('fonts')

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css?v=6') }}">
    @yield('css')
</head>
<body>
    @if (request()->route()->getName() == 'admin.login.index')
        <div class="container" style="margin-top:50px;">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    @if (count($errors) > 0)
                        <div class="alert bg-danger text-white">
                            @foreach ($errors->all() as $error)
                                <div>{!! $error !!}</div>
                            @endforeach
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    @else
        <nav class="navbar navbar-expand">
            <div class="container">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a href="{{ route('home.dashboard') }}" class="nav-link"><i class="fas fa-arrow-left"></i></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.index') }}" class="nav-link">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.info') }}" class="nav-link">Info</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle username" data-toggle="dropdown">
                            <div class="username-holder">{{ staffUser()->username }}</div>
                            <i class="arrow-down img-white"></i>
                        </a>
                        <div class="dropdown-menu logout-dropdown">
                            <div class="dropdown-arrow"></div>
                            <a href="{{ route('admin.logout') }}" class="dropdown-item">Logout</a>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="main-holder container">
            @if (count($errors) > 0)
                <div class="alert bg-danger text-white">
                    @foreach ($errors->all() as $error)
                        <div>{!! $error !!}</div>
                    @endforeach
                </div>
            @endif

            @if (session()->has('success_message'))
                <div class="alert bg-success text-white">
                    {!! session()->get('success_message') !!}
                </div>
            @endif

            @hasSection('header')
                <div class="text-right mb-2">
                    @yield('header')
                </div>
            @endif

            @yield('content')
        </div>

        <div class="footer-push"></div>
        <footer>
            <div>© {{ date('Y') }} {{ config('site.name') }}. All rights reserved.</div>
            <div style="font-size:14px;">Sharing photos and videos of this panel is strictly prohibited and doing so will cause you to lose your administrative privileges.</div>
        </footer>
    @endif

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    @yield('js')
</body>
</html>
