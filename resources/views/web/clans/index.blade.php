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
    'title' => 'Clans'
])

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top blue">Popular Clans</div>
            <div class="content">
                <div class="mb2 overflow-auto">
                    <form action="{{ route('clans.index') }}" method="GET">
                        <div class="col-9-12">
                            <input class="width-100 rigid" style="height:41px;" type="text" name="search" placeholder="Search">
                        </div>
                        <div class="col-3-12">
                            <div class="acc-1-2 np">
                                <button class="blue width-100" type="submit">Search</button>
                            </div>
                            @auth
                                <div class="acc-1-2 np">
                                    <a href="{{ route('clans.create') }}" class="button green width-100">Create</a>
                                </div>
                            @endauth
                        </div>
                    </form>
                </div>
                <div class="col-1-1" style="padding-right:0;">
                    @forelse ($clans as $clan)
                        <a href="{{ route('clans.view', $clan->id) }}">
                            <div class="hover-card clan">
                                <div class="clan-logo">
                                    <img class="width-100" src="{{ $clan->thumbnail() }}">
                                </div>
                                <div class="data ellipsis">
                                    <span class="clan-name bold mobile-col-1-2 ellipsis">{{ $clan->name }}</span>
                                    <span class="push-right">{{ number_format($clan->member_count) }} {{ ($clan->member_count == 1) ? 'Member' : 'Members' }}</span>
                                </div>
                                <div class="clan-description">{!! nl2br(e($clan->description)) !!}</div>
                            </div>
                        </a>
                        <hr>
                    @empty
                        <div style="text-align:center">
                            <span>No clans found</span>
                        </div>
                    @endforelse
                </div>
                <div class="pages">{{ $clans->onEachSide(1) }}</div>
            </div>
        </div>
    </div>
@endsection
