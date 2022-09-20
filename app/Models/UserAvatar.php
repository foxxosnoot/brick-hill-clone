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

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

$GLOBALS['tempavcache'] = [];

class UserAvatar extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_avatars';

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function hat($num)
    {
        if (isset($GLOBALS['tempavcache']['hat_' . $num]))
            return $GLOBALS['tempavcache']['hat_' . $num];

        $item = Item::where('id', '=', $this->{"hat_{$num}"})->first();
        $tempcache['hat_' . $num] = $item;

        return $item;
    }

    public function face()
    {
        if (isset($GLOBALS['tempavcache']['face']))
            return $GLOBALS['tempavcache']['face'];

        $item = Item::where('id', '=', $this->face)->first();
        $tempcache['face'] = $item;

        return $item;
    }

    public function tool()
    {
        if (isset($GLOBALS['tempavcache']['tool']))
            return $GLOBALS['tempavcache']['tool'];

        $item = Item::where('id', '=', $this->tool)->first();
        $tempcache['tool'] = $item;

        return $item;
    }

    public function tshirt()
    {
        if (isset($GLOBALS['tempavcache']['tshirt']))
            return $GLOBALS['tempavcache']['tshirt'];

        $item = Item::where('id', '=', $this->tshirt)->first();
        $tempcache['tshirt'] = $item;

        return $item;
    }

    public function shirt()
    {
        if (isset($GLOBALS['tempavcache']['shirt']))
            return $GLOBALS['tempavcache']['shirt'];

        $item = Item::where('id', '=', $this->shirt)->first();
        $tempcache['shirt'] = $item;

        return $item;
    }

    public function pants()
    {
        if (isset($GLOBALS['tempavcache']['pants']))
            return $GLOBALS['tempavcache']['pants'];

        $item = Item::where('id', '=', $this->pants)->first();
        $tempcache['pants'] = $item;

        return $item;
    }

    public function head()
    {
        if (isset($GLOBALS['tempavcache']['head']))
            return $GLOBALS['tempavcache']['head'];

        $item = Item::where('id', '=', $this->head)->first();
        $tempcache['head'] = $item;

        return $item;
    }

    public function figure()
    {
        if (isset($GLOBALS['tempavcache']['figure']))
            return $GLOBALS['tempavcache']['figure'];

        $item = Item::where('id', '=', $this->figure)->first();
        $tempcache['figure'] = $item;

        return $item;
    }

    public function reset()
    {
        $thumbnail = "thumbnails/avatars/{$this->image}.png";

        $this->timestamps = false;
        $this->image = 'default';
        $this->hat_1 = null;
        $this->hat_2 = null;
        $this->hat_3 = null;
        $this->hat_4 = null;
        $this->hat_5 = null;
        $this->head = null;
        $this->face = null;
        $this->tool = null;
        $this->tshirt = null;
        $this->shirt = null;
        $this->pants = null;
        $this->figure = null;
        $this->color_head = '#f3b700';
        $this->color_torso = '#c60000';
        $this->color_left_arm = '#f3b700';
        $this->color_right_arm = '#f3b700';
        $this->color_left_leg = '#650013';
        $this->color_right_leg = '#650013';
        $this->save();

        if (Storage::exists($thumbnail))
            Storage::delete($thumbnail);
    }
}
