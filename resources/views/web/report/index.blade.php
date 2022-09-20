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
    'title' => 'Report'
])

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top blue">Report</div>
            <div class="content">
                <span class="darker-grey-text bold block">Tell us how you think this {{ $type }} is breaking the {{ config('site.name') }} rules.</span>
                <form action="{{ route('report.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <select name="category">
                        @foreach ($categories as $name => $value)
                            <option value="{{ $value }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <textarea style="width:100%;box-sizing:border-box;margin-top:10px;height:100px;" name="comment" placeholder="Explain more (optional)"></textarea>
                    <button class="blue" type="submit">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>
@endsection
