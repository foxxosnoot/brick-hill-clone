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
    'title' => 'Friends'
])

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top blue">Friends</div>
            <div class="content text-center">
                @if ($friendRequests->count() == 0)
                    <span>You don't have any friend requests</span>
                @else
                    <ul class="friends-list">
                        @foreach ($friendRequests as $friendRequest)
                            <li class="col-1-5 mobile-col-1-1">
                                <div class="friend-card">
                                    <a href="{{ route('users.profile', $friendRequest->sender->id) }}">
                                        <img src="{{ $friendRequest->sender->thumbnail() }}">
                                        <div class="ellipsis">{{ $friendRequest->sender->username }}</div>
                                    </a>
                                    <form action="{{ route('account.friends.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $friendRequest->sender->id }}">
                                        <button class="button small green inline" style="left:10px;font-size:10px;" name="action" value="accept">ACCEPT</button>
                                        <button class="button small red inline" style="left:10px;font-size:10px;" name="action" value="decline">DECLINE</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <div class="pages">{{ $friendRequests->onEachSide(1) }}</div>
    </div>
@endsection
