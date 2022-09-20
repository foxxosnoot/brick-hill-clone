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

use App\Models\Inventory;
use App\Models\ItemComment;
use Illuminate\Support\Str;
use App\Jobs\NotifyWebhooks;
use App\Models\ItemFavorite;
use App\Models\ItemPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'type',
        'status',
        'price_bits',
        'price_bucks',
        'special_type',
        'stock',
        'public_view',
        'onsale',
        'thumbnail_url',
        'filename',
        'onsale_until',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'onsale_until' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }

    public function thumbnail()
    {
        $url = config('site.storage_url');

        if ($this->status != 'approved')
            return "{$url}/default/{$this->status}.png";

        return "{$url}/thumbnails/items/{$this->thumbnail_url}.png";
    }

    public function render()
    {
        $thumbnail = Str::random(20);
        $request = [
            'key' => config('site.renderer.key'),
            'id' => 'item_' . $this->id,
            'resolution' => 375,
            'textures' => []
        ];

        switch ($this->type) {
            case 'hat':
                $request['hats'] = [$this->filename];
                $request['textures'][] = $this->filename . '.png';
                break;
            case 'face':
                $request['face'] = $this->filename;
                break;
            case 'tool':
                $request['gear'] = $this->filename;
                $request['textures'][] = $this->filename . '.png';
                break;
            case 'tshirt':
                $request['tshirt'] = $this->filename;
                break;
            case 'shirt':
                $request['shirt'] = $this->filename;
                break;
            case 'pants':
                $request['pants'] = $this->filename;
                break;
            case 'head':
                $request['head'] = $this->filename;
                break;
        }

        $ch = curl_init(config('site.renderer.url'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        if (isset($response->thumbnail)) {
			Storage::put("thumbnails/items/{$thumbnail}.png", base64_decode($response->thumbnail));

			if (Storage::exists("thumbnails/items/{$this->thumbnail_url}.png"))
				Storage::delete("thumbnails/items/{$this->thumbnail_url}.png");

            $this->timestamps = false;
			$this->thumbnail_url = $thumbnail;
			$this->save();
		}
    }

    public function isTimed()
    {
        return !empty($this->onsale_until) && strtotime($this->onsale_until) > time();
    }

    public function onsale()
    {
        if ($this->onsale_until && strtotime($this->onsale_until) < time())
            return false;

        if ($this->special_type && $this->stock < 1)
            return false;

        return $this->onsale;
    }

    public function owners()
    {
        return Inventory::where('item_id', '=', $this->id)->get();
    }

    public function sold()
    {
        return ItemPurchase::where('item_id', '=', $this->id)->get();
    }

    public function favorites()
    {
        return ItemFavorite::where('item_id', '=', $this->id)->get();
    }

    public function resellers()
    {
        return ItemReseller::where('item_id', '=', $this->id)->orderBy('price', 'ASC')->paginate(10);
    }

    public function comments($hasPagination = true)
    {
        if (Auth::check() && Auth::user()->isStaff())
            $comments = ItemComment::where('item_id', '=', $this->id)->orderBy('created_at', 'DESC');
        else
            $comments = ItemComment::where([
                ['item_id', '=', $this->id],
                ['is_deleted', '=', false]
            ])->orderBy('created_at', 'DESC');

        return ($hasPagination) ? $comments->paginate(10) : $comments->get();
    }

    public function recentAveragePrice()
    {
        $purchases = ItemPurchase::where('item_id', '=', $this->id);
        $average = 0;

        if ($purchases->count() > 0)
            $average = $purchases->avg('price');

        return (integer) $average;
    }

    public function scrub($column)
    {
        $this->timestamps = false;

        switch ($column) {
            case 'name':
            case 'description':
                $this->$column = '[ Content Removed ]';
                $this->save();
                break;
        }
    }

    public function notifyWebhooks($isNew)
    {
        if ($this->public_view)
            NotifyWebhooks::dispatch($this->id, $isNew);
    }
}
