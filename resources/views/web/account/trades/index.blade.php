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
    'title' => 'Trades'
])

@section('meta')
    <meta
        name="routes"
        data-process="{{ route('account.trades.process') }}"
    >
    <meta
        name="user-info"
        data-id="{{ Auth::user()->id }}"
    >
@endsection

@section('js')
    <script src="{{ js_file('account/trades/index') }}"></script>
@endsection

@section('content')
    <div class="col-1-3">
        <select class="width-100" id="category">
            <option value="inbound">Inbound</option>
            <option value="outbound">Outbound</option>
            <option value="history">History</option>
        </select>
        <div id="trades" style="max-height:650px;overflow-y:auto;"></div>
    </div>
    <div class="col-2-3">
        <div id="trade"></div>
    </div>
    <div class="modal text-center" style="display:none;" data-modal="accept">
        <div class="modal-content">
            <span class="close" data-modal-close="accept">×</span>
            <span>Accept Trade</span>
            <hr>
            <span>Are you sure you want to accept this trade?</span>
            <div class="modal-buttons">
                <form action="{{ route('account.trades.process') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" id="acceptModalId">
                    <button class="button green" style="margin-right:10px;" name="action" value="accept">Accept</button>
                </form>
                <button type="button" class="cancel-button" data-modal-close="accept">Cancel</button>
            </div>
        </div>
    </div>
@endsection
