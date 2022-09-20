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

<div class="thread-row" style="position:relative;{{ ($post->is_deleted) ? 'opacity:.5;' : '' }}">
    <div class="overflow-auto">
        <div class="col-3-12 center-text ellipsis">
            <a href="{{ route('users.profile', $post->creator->id) }}">
                <img src="{{ $post->creator->thumbnail() }}" style="width:150px;">
            </a>
            <br>
            @if ($post->creator->hasPrimaryClan())
                <a href="{{ route('clans.view', $post->creator->primaryClan->id) }}" class="light-gray-text">[{{ $post->creator->primaryClan->tag }}]</a>
            @endif
            <a href="{{ route('users.profile', $post->creator->id) }}">{{ $post->creator->username }}</a>
            <br>
            <span class="light-gray-text">Joined {{ $post->creator->created_at->format('d/m/Y') }}</span>
            <br>
            <span class="light-gray-text">Posts {{ number_format($post->creator->forumPostCount()) }}</span>
            @if ($post->creator->isStaff())
                <div class="red-text"><i class="fas fa-gavel"></i>Administrator</div>
            @endif
        </div>
        <div class="col-9-12">
            <div class="weight600 light-gray-text text-right mobile-center-text" style="text-align:right;">{{ $post->created_at->format('h:i A d/m/Y') }}</div>
            <div class="p post_css_overwrite">
                @if ($isReply && $post->quote_id && (!$post->quote->is_deleted || (Auth::check() && Auth::user()->isStaff())))
                    <blockquote class="{{ (!$isReply) ? $post->topic->color() : $post->thread->topic->color() }}">
                        <em>Quote from <a href="{{ route('users.profile', $post->quote->creator->id) }}" style="color:#444">{{ $post->quote->creator->username }}</a>, {{ $post->quote->created_at->format('h:i A d/m/Y') }}</em>
                        <br>
                        {!! (!$post->quote->is_html) ? nl2br(e($post->quote->body)) : nl2br($post->quote->body) !!}
                    </blockquote>
                @endif

                {!! (!$post->is_html) ? nl2br(e($post->body)) : nl2br($post->body) !!}
            </div>
        </div>
    </div>
    <div class="col-1-1 weight600 dark-grey-text forum-options" style="text-align:right;">
        @auth
            @if (!$post->is_locked || (Auth::user()->isStaff() && $post->is_locked))
                @if ($isReply)
                    <a class="forum-quote mr4" href="{{ route('forum.new', ['quote', $post->id]) }}">QUOTE</a>
                @endif

                <a class="forum-reply {{ ($post->creator->id != Auth::user()->id && !$post->creator->isStaff()) ? 'mr4' : '' }}" href="{{ route('forum.new', ['reply', $thread->id]) }}">REPLY</a>
            @endif

            @if (!$thread->is_deleted && !$post->is_deleted && $post->creator->id != Auth::user()->id && !$post->creator->isStaff())
                <a class="report" href="{{ route('report.index', [($isReply) ? 'forumreply' : 'forumthread', $post->id]) }}">REPORT</a>
            @endif
        @endauth
    </div>
    @if (Auth::check() && Auth::user()->isStaff())
        @if (!$isReply)
            @if (
                Auth::user()->staff('can_delete_forum_posts') ||
                Auth::user()->staff('can_edit_forum_posts') ||
                Auth::user()->staff('can_pin_forum_posts') ||
                Auth::user()->staff('can_lock_forum_posts')
            ) <div class="col-1-1 weight600 dark-grey-text forum-options"> @endif

            @if (Auth::user()->staff('can_delete_forum_posts'))
                <a class="report mr4" href="{{ route('forum.moderate', ['thread', 'delete', $post->id]) }}">{{ (!$post->is_deleted) ? 'DELETE' : 'UNDELETE' }}</a>
            @endif

            @if (Auth::user()->staff('can_edit_forum_posts'))
                <a class="report mr4" href="{{ route('forum.edit', ['thread', $post->id]) }}">EDIT</a>
            @endif

            @if (Auth::user()->staff('can_pin_forum_posts'))
                <a class="report mr4" href="{{ route('forum.moderate', ['thread', 'pin', $post->id]) }}">{{ (!$post->is_pinned) ? 'PIN' : 'UNPIN' }}</a>
            @endif

            @if (Auth::user()->staff('can_lock_forum_posts'))
                <a class="report mr4" href="{{ route('forum.moderate', ['thread', 'lock', $post->id]) }}">{{ (!$post->is_locked) ? 'LOCK' : 'UNLOCK' }}</a>
            @endif

            @if (
                Auth::user()->staff('can_delete_forum_posts') ||
                Auth::user()->staff('can_edit_forum_posts') ||
                Auth::user()->staff('can_pin_forum_posts') ||
                Auth::user()->staff('can_lock_forum_posts')
            ) </div> @endif
        @else
            @if (
                Auth::user()->staff('can_delete_forum_posts') ||
                Auth::user()->staff('can_edit_forum_posts')
            ) <div class="col-1-1 weight600 dark-grey-text forum-options"> @endif

            @if (Auth::user()->staff('can_delete_forum_posts'))
                <a class="report mr4" href="{{ route('forum.moderate', ['reply', 'delete', $post->id]) }}">{{ (!$post->is_deleted) ? 'DELETE' : 'UNDELETE' }}</a>
            @endif

            @if (Auth::user()->staff('can_edit_forum_posts'))
                <a class="report mr4" href="{{ route('forum.edit', ['reply', $post->id]) }}">EDIT</a>
            @endif

            @if (
                Auth::user()->staff('can_delete_forum_posts') ||
                Auth::user()->staff('can_edit_forum_posts')
            ) </div> @endif
        @endif
    @endif
</div>
<hr>
