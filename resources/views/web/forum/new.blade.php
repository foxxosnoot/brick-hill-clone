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
    'title' => $title
])

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="col-8-12">
            @include('web.forum._header')
        </div>
    </div>
    <div class="col-10-12 push-1-12">
        <div class="forum-bar weight600" style="padding:10px 5px 10px 0;">
            <a href="{{ route('forum.index') }}">Forum</a>
            <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
            <a href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a>
            @if ($thread)
                <i class="fa fa-angle-double-right" style="font-size:1rem;" aria-hidden="true"></i>
                <a href="{{ route('forum.thread', $thread->id) }}">
                    <span class="weight700 bold">{{ $thread->title }}</span>
                </a>
            @endif
        </div>
        <div class="card">
            <div class="top {{ $topic->color() }}">{{ $title }}</div>
            <div class="content">
                <form action="{{ route('forum.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">
                    <input type="hidden" name="type" value="{{ $type }}">

                    @if ($quote)
                        <blockquote class="{{ $topic->color() }}">
                            <em>Quote from <a href="{{ route('users.profile', $quote->creator->id) }}" style="color:#444">{{ $quote->creator->username }}</a>, {{ $quote->created_at->format('h:i A d/m/Y') }}</em>
                            <br>
                            {!! nl2br(e($quote->body)) !!}
                        </blockquote>
                    @endif

                    @if ($type == 'thread')
                        <input style="width:100%;font-size:16px;box-sizing:border-box;" type="text" name="title" placeholder="Title (max 60 characters)" required>
                    @endif

                    <textarea style="width:100%;min-height:200px;font-size:16px;box-sizing:border-box;margin-top:10px;" name="body" placeholder="Body (max 3,000 characters)" required></textarea>
                    <div style="text-align:center;">
                        <button type="submit" class="button smaller-text {{ $topic->color() }}">Create {{ ucfirst($type) }}</button>
                        {{-- <button type="button" id="draft" class="button smaller-text {{ $topic->color() }}">Save as Draft</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
