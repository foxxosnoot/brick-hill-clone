<?php
/**
 * MIT License
 *
 * Copyright (c) 2021-2022 FoxxoSnoot
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'reporter_id',
        'content_id',
        'type',
        'category',
        'comment'
    ];

    public function reporter()
    {
        return $this->belongsTo('App\Models\User', 'reporter_id');
    }

    public function reviewer()
    {
        return $this->belongsTo('App\Models\User', 'reviewer_id');
    }

    public function content()
    {
        switch ($this->type) {
            case 'user':
                $model = 'User';
                break;
            case 'status':
                $model = 'Status';
                break;
            case 'item':
                $model = 'Item';
                break;
            case 'comment':
                $model = 'ItemComment';
                break;
            case 'forumthread':
                $model = 'ForumThread';
                break;
            case 'forumreply':
                $model = 'ForumReply';
                break;
            case 'clan':
                $model = 'Clan';
                break;
            case 'set':
                $model = 'Game';
                break;
            case 'message':
                $model = 'Message';
                break;
        }

        return $this->belongsTo("App\Models\\{$model}", 'content_id');
    }

    public function type()
    {
        return ucwords(str_replace('-', ' ', $this->type));
    }

    public function url()
    {
        switch ($this->type) {
            case 'user':
                return route('users.profile', $this->content->id);
            case 'status':
                return route('users.profile', $this->content->creator->id);
            case 'item':
                return route('shop.item', $this->content->id);
            case 'comment':
                return route('shop.item', $this->content->item->id);
            case 'forumthread':
                return route('forum.thread', $this->content->id);
            case 'forumreply':
                return route('forum.thread', $this->content->thread_id);
            case 'clan':
                return route('clans.view', $this->content->id);
            case 'set':
                return route('games.view', $this->content->id);
        }
    }
}
