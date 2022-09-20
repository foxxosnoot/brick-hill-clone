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

@extends('layouts.admin', [
    'title' => 'Info'
])

@section('content')
    <div class="card">
        <div class="card-header">Site Statistics</div>
        <div class="card-body text-center">
            <div class="row">
                @foreach ($siteData as $title => $value)
                    <div class="col-6 col-md-3">
                        <h4>{{ $value }}</h4>
                        <h5 class="text-muted">{{ $title }}</h5>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Server Information</div>
        <div class="card-body text-center">
            <div class="row">
                @foreach ($serverData as $title => $value)
                    <div class="col-6 col-md-3">
                        <h4>{{ $value }}</h4>
                        <h5 class="text-muted">{{ $title }}</h5>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
