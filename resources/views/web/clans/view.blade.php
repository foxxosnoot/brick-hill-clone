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
    'title' => $clan->name,
    'image' => $clan->thumbnail()
])

@section('meta')
    <meta
        name="clan-info"
        data-id="{{ $clan->id }}"
    >
@endsection

@section('js')
    <script src="{{ js_file('clans/view') }}"></script>
@endsection

@section('content')
    <div class="col-10-12 push-1-12">
        <div class="card">
            <div class="top" style="position:relative;">
                <span class="clan-title">{{ $clan->name }}</span>
                <b>[{{ $clan->tag }}]</b>
                @if (Auth::check() && $clan->owner->id != Auth::user()->id && !$clan->owner->isStaff())
                    <a href="{{ route('report.index', ['clan', $clan->id]) }}" class="red-text" style="float:right;">
                        <i class="far fa-flag"></i>
                        <span style="font-size: 0.9rem;">Report</span>
                    </a>
                @endif
            </div>
            <div class="content" style="position:relative;">
                <div class="col-3-12">
                    <div class="clan-img-holder mb1">
                        <img class="width-100" src="{{ $clan->thumbnail() }}">
                    </div>
                    <div class="dark-gray-text bold">
                        <div>
                            <span>Owned by</span>
                            <b><a href="{{ route('users.profile', $clan->owner->id) }}" class="black-text">{{ $clan->owner->username }}</a></b>
                        </div>
                        <div>{{ number_format($clan->members()->count()) }} {{ ($clan->members()->count() == 1) ? 'Member' : 'Members' }}</div>
                    </div>
                    @auth
                        @if (!Auth::user()->isInClan($clan->id))
                            <form action="{{ route('clans.membership') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $clan->id }}">
                                <button class="green" style="font-size:12px;" type="submit">JOIN</button>
                            </form>
                        @else
                            @if (Auth::user()->id != $clan->owner->id)
                                <form action="{{ route('clans.membership') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $clan->id }}">
                                    <button class="red" style="font-size:12px;" type="submit">LEAVE</button>
                                </form>
                            @endif

                            <form action="{{ route('clans.primary') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="id" value="{{ $clan->id }}">
                                @if (Auth::user()->primary_clan_id == $clan->id)
                                    <button class="red" style="font-size:12px;width:130px;padding-left:5px;padding-right:5px;" type="submit">REMOVE PRIMARY</button>
                                @else
                                    <button class="green" style="font-size:12px;width:120px;padding-left:5px;padding-right:5px;" type="submit">MAKE PRIMARY</button>
                                @endif
                            </form>
                        @endif

                        @if (Auth::user()->id == $clan->owner->id)
                            <a href="{{ route('clans.edit', $clan->id) }}" class="button blue" style="font-size:12px;">EDIT</a>
                        @endif
                    @endauth
                </div>
                <div class="col-9-12">
                    <div class="clan-description darkest-gray-text bold">{!! nl2br(e($clan->description)) !!}</div>
                </div>
            </div>
        </div>
        <div class="col-1-1 tab-buttons">
            <button class="tab-button w600 blue" data-tab="members">MEMBERS</button>
            <button class="tab-button w600 transparent" data-tab="relations">RELATIONS</button>
        </div>
        <div class="col-1-1">
            <div class="card" data-tab-section="members">
                <div class="top blue">Members</div>
                <div class="content" style="min-height:250px;">
                    <div class="mb1 overflow-auto">
                        @if (Auth::check() && Auth::user()->isInClan($clan->id))
                            <span class="dark-gray-text">Your rank: <b class="black-text">{{ Auth::user()->rankInClan($clan->id)->name }}</b></span>
                        @endif
                        <div style="width:150px;float:right;">
                            <select class="push-right" id="rank">
                                @foreach ($clan->ranks() as $rank)
                                    <option value="{{ $rank->rank }}">{{ $rank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-center overflow-auto unselectable" id="members"></div>
                </div>
            </div>
            <div class="card" style="display:none;" data-tab-section="relations">
                <div class="top">Relations</div>
                <div class="content">
                    <fieldset class="fieldset green mb1">
                        <legend>Allies</legend>
                        <div class="p1 overflow-auto">
                            <div class="text-center bold agray-text">This clan has no allies</div>
                        </div>
                    </fieldset>
                    <fieldset class="fieldset red mb1">
                        <legend>Enemies</legend>
                        <div class="p1 overflow-auto">
                            <div class="text-center bold agray-text">This clan has no enemies</div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="card" style="display:none;" data-tab-section="store">
                <div class="top red">Store</div>
                <div class="content">Feature coming soon.</div>
            </div>
        </div>
    </div>
@endsection
