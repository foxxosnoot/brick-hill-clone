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
    'title' => 'Redeem Promo Code'
])

@section('meta')
    <meta name="routes" data-redeem="{{ route('account.promocodes.redeem') }}">
@endsection

@section('css')
    <style>
        @media handheld, only screen and (min-width:900px) {
            body {
                background-repeat: no-repeat;
                background-position: bottom;
                background-size: cover;
                background-position-y: 100px
            }
        }

        .carousel .col-1-5 {
            padding-left: 10px;
            padding-right: 10px
        }

        .soon-text {
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
            padding: 5px;
            text-align: center;
            background-color: rgba(64, 63, 63, .48);
            color: #fff
        }

        .item {
            position: relative;
            padding: 10px;
            border-radius: 2px;
            transition: border-color 150ms
        }

        .theme-default .item {
            background-color: #d6d6db
        }

        .theme-dark .item {
            background-color: #29292b
        }

        .theme-halloween .item {
            background-color: #0b1628
        }

        .item.filled {
            border: 1px solid #fff
        }

        .theme-default .item.filled {
            background: radial-gradient(circle, #BCA285 0%, #D6D6DB 75%)
        }

        .theme-dark .item.filled {
            background: radial-gradient(circle, #41382E 0%, #29292B 75%)
        }

        .theme-halloween .item.filled {
            background: radial-gradient(circle, #413b2e 0%, #0b1628 75%)
        }

        .item:hover {
            border-color: #00a9fe
        }

        .item:not(.filled) img {
            height: 0;
            padding-bottom: 100%
        }

        .item img {
            width: 100%
        }

        .lower-text {
            height: 15px;
            margin: 5px;
            padding-bottom: 50px
        }
    </style>
@endsection

@section('js')
    <script src="{{ js_file('account/promocodes') }}"></script>
@endsection

@section('content')
    <div class="new-theme">
        <div class="col-5-12">
            <div class="large-text bold" style="margin-bottom:20px;">REDEEM PROMO CODE</div>
            <div style="margin-bottom:5px;">Enter code here:</div>
            <form id="codeForm" style="display:block;">
                <input type="text" name="code">
                <button class="blue" type="submit">REDEEM</button>
            </form>
            <div class="smaller-text lower-text" id="message"></div>
            <div style="padding-bottom: 50px;">
                <span>Promo codes can be obtained through official {{ config('site.name') }} promotions or through events hosted by us.</span>
                <span>As well as this, promocodes may be included in products or merchandise produced by us.</span>
                <br><br>
                <span>All available items that are a part of current promotions can be seen below.</span>
            </div>
        </div>
        <div class="col-1-1">
            <div class="large-text bold" style="margin-bottom:20px;">AVAILABLE ITEMS</div>
            <div class="carousel">
                @forelse ($items as $item)
                    <div class="col-1-5 mobile-col-1-2">
                        <div class="item filled">
                            <a href="{{ route('shop.item', $item->id) }}">
                                <img src="{{ $item->thumbnail() }}">
                            </a>
                            @if ($item->coming_soon || $item->leaving_soon)
                                <div class="soon-text">{{ ($item->coming_soon) ? 'Coming' : 'Leaving' }} Soon</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-1-1">There are currently no available items.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
