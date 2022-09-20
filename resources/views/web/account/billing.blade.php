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
    'title' => 'Membership'
])

@section('js')
    <script src="{{ js_file('account/billing') }}"></script>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top orange">Membership</div>
            <div class="content">
                <div class="center-text small-text">
                    <span>With Membership you get a higher daily allowance and more freedom when trading, buying, and selling items.</span>
                    <span>You're also entered into monthly giveaways and experience bonuses depending on your package.</span>
                    <span>First-time buyers also get a <b>100 buck</b> signing bonus!</span>
                </div>
                <hr>
                <div class="membership-holder" style="margin-bottom:25px;">
                    <div class="membership-header ace">
                        <span class="membership-icon ace-icon absolute"></span>
                        <div class="membership-text white-text">
                            <div class="smedium-text bold">ACE MEMBERSHIP</div>
                            <div class="membership-perks smaller-text">
                                <div>Receive <b>20 daily bucks</b>, make up to <b>10 clans</b>, and create up to <b>10 sets</b>.</div>
                                <div>Create <b>20 buy requests</b>, and have a <b>5% lower tax</b>.</div>
                                <div><b>100 items</b> in trades, <b>unlimited specials</b> onsale.</div>
                                <div>Entered once into <b>monthly giveaway</b>.</div>
                            </div>
                        </div>
                        <img src="{{ asset('images/membership/acebkg.png') }}" class="membership-bkg hide-on-mobile">
                        <img src="{{ asset('images/membership/aceitems.png') }}" class="membership-items hide-on-mobile">
                    </div>
                    <div class="membership-buttons">
                        <button class="membership-button white-text plain flat ace">
                            <span class="bold medium-text">$5.99</span>
                            <span class="membership-month">/month</span>
                        </button>
                        <button class="membership-button white-text plain flat ace">
                            <div class="membership-length">6 MONTHS</div>
                            <span class="bold medium-text">$32.99</span>
                            <div class="membership-average small-text ace">Avg. monthly of $5.49</div>
                        </button>
                        <button class="membership-button white-text plain flat ace">
                            <div class="membership-length">12 MONTHS</div>
                            <span class="bold medium-text">$57.99</span>
                            <div class="membership-average small-text ace">Avg. monthly of $4.83</div>
                        </button>
                    </div>
                </div>
                <div class="membership-holder">
                    <div class="membership-header royal">
                        <span class="membership-icon royal-icon absolute"></span>
                        <div class="membership-text white-text">
                            <div class="smedium-text bold">ROYAL MEMBERSHIP</div>
                            <div class="membership-perks smaller-text">
                                <div>Receive <b>70 daily bucks</b>, make up to <b>20 clans</b>, and create up to <b>20 sets</b>.</div>
                                <div>Create <b>50 buy requests</b>, and have a <b>10% lower tax</b>.</div>
                                <div><b>100 items</b> in trades, <b>unlimited specials</b> onsale.</div>
                                <div>Entered twice into <b>monthly giveaway</b>.</div>
                            </div>
                        </div>
                        <img src="{{ asset('images/membership/royalbkg.png') }}" class="membership-bkg hide-on-mobile">
                        <img src="{{ asset('images/membership/royalitems.png') }}" class="membership-items hide-on-mobile">
                    </div>
                    <div class="membership-buttons">
                        <button class="membership-button white-text plain flat royal">
                            <span class="bold medium-text">$12.99</span>
                            <span class="membership-month">/month</span>
                        </button>
                        <button class="membership-button white-text plain flat royal">
                            <div class="membership-length">6 MONTHS</div>
                            <span class="bold medium-text">$68.99</span>
                            <div class="membership-average small-text royal">Avg. monthly of $11.50</div>
                        </button>
                        <button class="membership-button white-text plain flat royal">
                            <div class="membership-length">12 MONTHS</div>
                            <span class="bold medium-text">$126.99</span>
                            <div class="membership-average small-text royal">Avg. monthly of $10.58</div>
                        </button>
                    </div>
                </div>
                <div class="center-text">
                    {{-- <a href="/membership/giveaway"> --}}
                        <button class="lottery-button white-text plain flat">
                            <div class="smedium-text">VIEW GIVEAWAY</div>
                        </button>
                    {{-- </a> --}}
                </div>
            </div>
        </div>
        <div class="card">
            <div class="top green">Bucks</div>
            <div class="content membership-buttons">
                <div class="center-text small-text">Purchase virtual currency to get more items in the shop as well as other perks around the site. Depending on how much you've spent you're also able to get exclusive items and awards!</div>
                <hr>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">100 BUCKS</div>
                    <span class="bucks-price">$0.99</span>
                </button>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">500 BUCKS</div>
                    <span class="bucks-price">$4.89</span>
                </button>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">1000 BUCKS</div>
                    <span class="bucks-price">$9.59</span>
                </button>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">2000 BUCKS</div>
                    <span class="bucks-price">$18.99</span>
                </button>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">5000 BUCKS</div>
                    <span class="bucks-price">$45.99</span>
                </button>
                <button class="membership-button bucks white-text plain flat" style="margin-bottom:5px;">
                    <div class="bucks-amount medium-text bold">10000 BUCKS</div>
                    <span class="bucks-price">$88.99</span>
                </button>
            </div>
        </div>
    </div>
@endsection
