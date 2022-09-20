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
    'title' => 'New Trade'
])

@section('meta')
    <meta
        name="routes"
        data-process="{{ route('account.trades.process') }}"
    >
    <meta
        name="trade-info"
        data-receiver="{{ $user->id }}"
    >
@endsection

@section('js')
    <script src="{{ js_file('account/trades/send') }}"></script>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top">Send trade to {{ $user->username }}</div>
            <div class="content">
                <div class="col-1-2">
                    <div class="tabs trade-items">
                        <div class="tab active col-1-1">Sending</div>
                        <div class="tab-holder">
                            <div class="tab-body active"">
                                @if ($giving->count() == 0)
                                    <div class="text-center">You do not have any specials.</div>
                                @else
                                    <ul class="tile-holder" style="max-height:650px;overflow-y:auto;">
                                        @foreach ($giving as $item)
                                            <li class="item-card-tile inline no-border" id="item_{{ $item->id }}" onclick="addItem({{ $item->id }}, 'giving');">
                                                <div class="item-card-container">
                                                    <div class="item-card-image">
                                                        <span class="trade-serial">#{{ $item->serial }}</span>
                                                        <img src="{{ $item->thumbnail() }}">
                                                    </div>
                                                    <div class="item-card-name gray-text ellipsis">{{ $item->name }}</div>
                                                    <div class="item-card-data">
                                                        <span class="light-gray-text" style="font-size: 12px;">Avg.</span>
                                                        <div class="inline">
                                                            <span class="bucks-icon"></span>
                                                            <span style="color:#009624;font-size:12px;">{{ shorten_number($item->recent_average_price) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="trade-bucks" style="margin-top:15px;">
                                        <span class="block" style="color:#666666;font-size:25px;">
                                            <span>+</span>
                                            <span class="bold bucks">
                                                <span class="bucks-icon" style="transform:scale(1.5);margin:4px 5px 5px 15px;"></span>
                                                <input class="bucks-text" id="givingCurrency" style="vertical-align: top; width: 75px;" type="number" min="0" value="0" placeholder="Currency">
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1-2">
                    <div class="tabs trade-items">
                        <div class="tab active col-1-1">Receiving</div>
                        <div class="tab-holder">
                            <div class="tab-body active">
                                @if ($receiving->count() == 0)
                                    <div class="text-center">This user does not have any specials.</div>
                                @else
                                    <ul class="tile-holder" style="max-height:650px;overflow-y:auto;">
                                        @foreach ($receiving as $item)
                                            <li class="item-card-tile inline no-border" id="item_{{ $item->id }}" onclick="addItem({{ $item->id }}, 'receiving');">
                                                <div class="item-card-container">
                                                    <div class="item-card-image">
                                                        <span class="trade-serial">#{{ $item->serial }}</span>
                                                        <img src="{{ $item->thumbnail() }}">
                                                    </div>
                                                    <div class="item-card-name gray-text ellipsis">{{ $item->name }}</div>
                                                    <div class="item-card-data">
                                                        <span class="light-gray-text" style="font-size: 12px;">Avg.</span>
                                                        <div class="inline">
                                                            <span class="bucks-icon"></span>
                                                            <span style="color:#009624;font-size:12px;">{{ shorten_number($item->recent_average_price) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="trade-bucks" style="margin-top:15px;">
                                        <span class="block" style="color:#666666;font-size:25px;">
                                            <span>+</span>
                                            <span class="bold bucks">
                                                <span class="bucks-icon" style="transform:scale(1.5);margin:4px 5px 5px 15px;"></span>
                                                <input class="bucks-text" id="receivingCurrency" style="vertical-align: top; width: 75px;" type="number" min="0" value="0" placeholder="Currency">
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @if ($giving->count() > 0 && $receiving->count() > 0)
                    <div class="col-1-1" style="text-align:center;">
                        <button class="green" id="sendButton">Send Trade</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
