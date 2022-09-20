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

@extends('layouts.admin', [
    'title' => 'Reports'
])

@section('content')
    <div class="row mb-2">
        @forelse ($reports as $report)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3"><strong>Reporter:</strong></div>
                            <div class="col-md-9"><a href="{{ route('users.profile', $report->reporter->id) }}" target="_blank">{{ $report->reporter->username }}</a></div>
                            @if (in_array($report->type, ['message', 'comment', 'status']))
                                <div class="col-md-3"><strong>Creator:</strong></div>
                                <div class="col-md-9"><a href="{{ route('users.profile', $report->content->creator->id ?? $report->content->sender->id) }}" target="_blank">{{ $report->content->creator->username ?? $report->content->sender->username }}</a></div>
                            @endif
                            <div class="col-md-3"><strong>Type:</strong></div>
                            <div class="col-md-9">
                                <span>{{ $report->type() }}</span>
                                @if ($report->url())
                                    <a href="{{ $report->url() }}" target="_blank">[{{ ($report->type != 'status') ? 'Click to view' : 'Click to view poster' }}]</a>
                                @endif
                            </div>
                            <div class="col-md-3"><strong>Category:</strong></div>
                            <div class="col-md-9">{{ $report->category }}</div>
                            <div class="col-md-12">
                                <div class="mb-2 hide-sm"></div>
                                <strong>Comment:</strong>
                                <div>{!! (!empty($report->comment)) ? nl2br(e($report->comment)) : '<div class="text-muted">This report does not have a comment.</div>' !!}</div>
                            </div>
                            @if (!$report->url() || $report->type == 'comment')
                                <div class="col-md-12">
                                    <div class="mb-2 hide-sm"></div>
                                    <strong>Content:</strong>
                                    <div>{{ $report->content->body ?? $report->content->message ?? 'UNKNOWN: ALERT DEVELOPER.' }}</div>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <form action="{{ route('admin.reports.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $report->id }}">
                            <div class="row">
                                <div class="col">
                                    <button class="green w-100" type="submit"><i class="fas fa-eye"></i></button>
                                </div>
                                @if (staffUser()->staff('can_ban_users'))
                                    <div class="col">
                                        <a href="{{ route('admin.users.ban.index', $report->reported_user_id) }}" class="button red w-100" target="_blank"><i class="fas fa-gavel"></i></a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">There are currently no pending reports.</div>
        @endforelse
    </div>
    <div class="pages">{{ $reports->onEachSide(1) }}</div>
@endsection
