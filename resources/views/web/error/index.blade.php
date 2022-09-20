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

@php
    $api = request()->is('api') || request()->is('api/*');

    if (site_setting('maintenance_enabled') && !session()->has('maintenance_password')) {
        if (!$api) {
            exit(header('Location: ' . route('maintenance.index')));
        } else {
            header('Content-Type: application/json');
            exit(json_encode([
                'error' => [
                    'message' => 'Maintenance is currently enabled',
                    'prettyMessage' => 'Maintenance is currently enabled'
                ]
            ]));
        }
    } else if ($api) {
        header('Content-Type: application/json');
        exit(json_encode([
            'error' => [
                'message' => 'Page not found',
                'prettyMessage' => 'Sorry, something went wrong'
            ]
        ]));
    }
@endphp

@extends('layouts.default', [
    'title' => $title
])

@section('content')
    <div style="text-align:center;padding-top:50px;">
        <span style="font-weight:600;font-size:3rem;display:block;">Error {{ $code }}: {{ $title }}</span>
        @if (isset($description))
            <span style="font-weight:500;font-size:2rem;display:block;padding-bottom:20px;">{{ $description }}</span>
        @endif
    </div>
@endsection
