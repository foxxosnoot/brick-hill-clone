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
    'title' => 'Maintenance'
])

@section('content')
    @if (!Auth::check())
        <div class="alert error">
            You can't win the aeo roll unless you login
        </div>
    @endif
    <div style="text-align:center;padding-top:50px;">
        <span style="font-weight:600;font-size:3rem;display:block;">Welcome to the new {{ config('site.name') }}</span>
        <span style="font-weight:500;font-size:2rem;">We are currently under maintenance.</span>
        <div style="text-align:center;margin:20px;">
            @foreach ($thumbnails as $thumbnail)
                <img style="width:20%;" src="{{ $thumbnail }}">
            @endforeach
        </div>
        <form action="{{ route('maintenance.authenticate') }}" method="POST" style="margin-top:20px;">
            @csrf
            <input type="password" name="key" placeholder="Maintenance Key">
            <div style="padding:2.5px;"></div>
            <button class="blue" type="submit">SUBMIT</button>
        </form>
    </div>
@endsection
