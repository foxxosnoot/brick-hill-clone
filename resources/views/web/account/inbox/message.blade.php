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
    'title' => $message->title
])

@section('js')
    <script src="{{ js_file('account/inbox/message') }}"></script>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top blue">{{ $message->title }}</div>
            <div class="content" style="position:relative;">
                <div class="user-info" style="width:250px;overflow:hidden;display:inline-block;float:left;">
                    <a href="{{ route('users.profile', $message->sender->id) }}">
                        <img src="{{ $message->sender->thumbnail() }}" style="width:200px;display:block;">
                        <span style="white-space:nowrap;">{{ $message->sender->username }}</span>
                    </a>
                </div>
                <div style="padding-left:250px;padding-bottom:10px;">{!! nl2br(e($message->body)) !!}</div>
                @if ($message->sender->id != Auth::user()->id && !$message->sender->isStaff() && $message->receiver->id == Auth::user()->id)
                    <div class="admin-forum-options" style="position:absolute;bottom:0;right:2px;padding-bottom:5px;">
                        <a href="{{ route('report.index', ['message', $message->id]) }}" class="dark-gray-text cap-text">Report</a>
                    </div>
                @endif
            </div>
        </div>
        @if ($message->sender->id != Auth::user()->id)
            <div class="card reply-card" id="replyCard" style="display:none;">
                <div class="content" style="padding:15px;">
                    <form action="{{ route('account.inbox.create') }}" method="POST"">
                        @csrf
                        <input type="hidden" name="id" value="{{ $message->id }}">
                        <input type="hidden" name="type" value="reply">
                        <textarea style="width:100%;height:250px;box-sizing:border-box;" name="body"></textarea>
                        <button class="forum-button blue" style="margin:10px auto 10px auto;display:block;" type="submit">SEND</button>
                    </form>
                </div>
            </div>
            <div class="center-text">
                <a class="button blue inline" id="replyButton" style="margin:10px auto 10px auto;">REPLY</a>
            </div>
        @endif
    </div>
@endsection
