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
    'title' => 'Create Clan'
])

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card" style="margin-bottom:20px;">
            <div class="top green">Create Clan</div>
            <div class="content">
                <div style="width:100%;">
                    <form action="{{ route('clans.purchase') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input class="upload-input" style="width:50px;margin-right:5px;display:inline-block;" type="text" name="tag" placeholder="Tag" required>
                        <input class="upload-input" style="display:inline-block;" type="text" name="name" placeholder="Title" required>
                        <input class="upload-input" style="border:0;" type="file" name="image" required>
                        <textarea class="upload-input" style="width:320px;height:100px;" name="description" placeholder="Description"></textarea>
                        <span style="color:green;display:block;">This will cost <span class="bucks-icon"></span>{{ config('site.clan_creation_price') }}</span>
                        <button class="green" type="submit">PURCHASE CLAN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
