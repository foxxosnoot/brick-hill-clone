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
    'title' => 'Customize'
])

@section('meta')
    <meta
        name="routes"
        data-regen="{{ route('account.character.regenerate') }}"
        data-inventory="{{ route('account.character.inventory') }}"
        data-wearing="{{ route('account.character.wearing') }}"
        data-update="{{ route('account.character.update') }}"
    >
@endsection

@section('js')
    <script src="{{ js_file('account/character') }}"></script>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="col-5-12">
            <div class="card">
                <div class="top blue">Avatar</div>
                <div class="content customize-content" style="position:relative;min-height:405.5px;">
                    <img id="avatar" src="{{ Auth::user()->thumbnail() }}" style="width:100%;">
                    <div class="loader" id="loader" style="display:none;"></div>
                </div>
            </div>
        </div>
        <div class="col-7-12" style="padding-right:0;">
            <div class="card">
                <div class="top blue">Crate</div>
                <div class="content">
                    <div class="search-bar">
                        <input class="search rigid width-100" id="search" style="margin-right:-30px;padding:7px;margin-bottom:5px;" type="text" placeholder="Search crate">
                    </div>
                    <div class="item-types">
                        <a class="active" data-tab="hats">Hats</a>
                        <span>|</span>
                        <a data-tab="faces">Faces</a>
                        <span>|</span>
                        <a data-tab="tools">Tools</a>
                        <span>|</span>
                        <a data-tab="heads">Heads</a>
                        <span>|</span>
                        <a data-tab="shirts">Shirts</a>
                        <span>|</span>
                        <a data-tab="t-shirts">T-Shirts</a>
                        <span>|</span>
                        <a data-tab="pants">Pants</a>
                    </div>
                    <div id="inventory"></div>
                </div>
            </div>
            <div class="card" style="position:relative;">
                <div class="top blue">Wearing</div>
                <div class="content">
                    <div id="wearing"></div>
                </div>
            </div>
        </div>
        <div class="col-10-12" style="margin-top:8px;"></div>
        <div class="col-6-12">
            <div class="card">
                <div class="top blue">Color Pallete</div>
                <div class="content" style="font-size:18px;padding:15px;">
                    <strong>Currently Editing:</strong>
                    <span id="currentlyEditing">Head</span>
                    <div style="margin-left:10px;margin-top:5px;">
                        @foreach ($colors as $hex)
                            <button class="avatar-body-color" style="background:{{ $hex }};width:48px;height:48px;cursor:pointer;display:inline-block;margin-bottom:5px;" data-color="{{ $hex }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6-12">
            <div class="card">
                <div class="top blue">Avatar Colors</div>
                <div class="content center-text">
                    <div style="margin-bottom:2.5px;">
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_head }};padding:25px;margin-top:-1px;" data-part="head"></button>
                    </div>
                    <div style="margin-bottom:2.5px;">
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_left_arm }};padding:50px;padding-right:0px;" data-part="left_arm"></button>
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_torso }};padding:50px;" data-part="torso"></button>
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_right_arm }};padding:50px;padding-right:0px;" data-part="right_arm"></button>
                    </div>
                    <div>
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_left_leg }};padding:50px;padding-right:0px;padding-left:47px;" data-part="left_leg"></button>
                        <button style="pointer:cursor;background-color:{{ Auth::user()->avatar()->color_right_leg }};padding:50px;padding-right:0px;padding-left:47px;" data-part="right_leg"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
