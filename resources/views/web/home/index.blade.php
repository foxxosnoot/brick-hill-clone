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

@extends('layouts.default')

@section('css')
    <style>
        .index-top-bar {
            background-color: #00062C;
            margin-top: -20px;
            text-align: center;
            color: #FFF;
        }

        .index-top-bar img {
            max-width: 80%;
            width: 550px;
            margin-top: 25px;
        }

        .index-top-bar .bar-1 {
            margin-top: 10px;
        }

        .index-top-bar .bar-1 span {
            font-size: 20px;
            margin-right: 75px;
        }

        .index-top-bar .bar-2 {
            margin-top: 30px;
            padding-bottom: 25px;
        }

        .col-1-2 {
            padding-right: 0;
        }

        .mt20 {
            margin-top: 20px;
        }
    </style>
@endsection

@section('before_content')
    <div class="index-top-bar">
        <img src="{{ config('site.logo') }}">
        <div class="bar-1">
            <span>Look Around</span>
            <button class="orange no-click" style="font-size:20px;">Play the Game</button>
        </div>
        <div class="bar-2">
            <span class="block">Join a thriving community of endless creativity today</span>
            <span>Be a part of <b title="{{ number_format($totalUsers) }}">{{ shorten_number($totalUsers) }}</b> users!</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="mt20" style="min-height:360px;">
        <div class="col-1-2">
            <div class="card" style="border-radius:5px 0 0 5px;">
                <div class="top blue">The Limits of Imagination</div>
                <div class="content darkest-grey-text" style="height:320px;">
                    {{ config('site.name') }} is the ultimate building block game, allowing you to push the limits of digital bricks and make your own games.
                    <br><br>
                    You're able to compete, build, and play with other users in an environment of your choosing, with the extensive games made by our users. Check out some of the gameplay now!
                </div>
            </div>
        </div>
        <div class="col-1-2 no-mobile" style="height:360px;">
            <iframe style="box-shadow:0 2px 5px rgba(0,0,0,.2);height:100%;width:100%;" type="text/html" src="https://www.youtube.com/embed/WmNy5rH8U7A" frameborder="0"></iframe>
        </div>
    </div>
    <div class="mt20" style="text-align:center;min-height:300px;">
        <div class="col-1-2">
            <img style="height:300px;" src="{{ asset('images/homepage/homepage_avatars.png') }}">
        </div>
        <div class="col-1-2 darkest-grey-text" style="padding:8.75% 0;">
            Customise your avatar in seemingly limitless ways! Choose from a <a href="{{ route('shop.index') }}" style="color:dodgerblue;">catalog</a> full of items and clothing created by us and the community!
            <br><br>
            With a modern and easy-to-use interface, you'll be able to make your character look exactly how you want it to.
            <br><br>
            If you're stumped, you can always see what other users have been buying in their <a href="{{ route('users.profile', 2) }}#tabs" style="color:dodgerblue;">crate</a>.
        </div>
    </div>
    <div class="mt20">
        <div class="col-1-2">
            <div class="card" style="border-radius:5px 0 0 5px;">
                <div class="top orange">Explore the Workshop</div>
                <div class="content darkest-grey-text" style="height:254.18px;">
                    A game built for imagination, this tailor-made workshop allows you to put practically anything you envision into reality.<br>
                    The simple layout and user-friendly interface ensures that you will be able to make what you want, no matter your experience.
                    <br><br>
                    If you're ever stuck, we've got a friendly <a href="{{ route('forum.topic', [5, 'support']) }}" style="color:dodgerblue;">support section</a> on the forum where the staff and users can help you with whatever you need!
                    <br><br>
                    Download the <a href="{{ route('games.download') }}" style="color:dodgerblue;">Workshop</a>.
                </div>
            </div>
        </div>
        <div class="col-1-2">
            <img style="box-shadow:0 2px 5px rgba(0,0,0,.2);width:100%;" src="{{ asset('images/homepage/workshop.png') }}">
        </div>
    </div>
@endsection
