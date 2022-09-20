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

namespace App\Http\Controllers\Web\Account;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\ThumbnailQueue;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    public const AVATAR_COLORS = [
        '#f3b700',
        '#d34a05',
        '#c60000',
        '#c81879',
        '#1c4399',
        '#3292d3',
        '#c2dc7f',
        '#1d6a19',
        '#85ad00',
        '#441209',
        '#c15b2c',
        '#f1f1f1',
        '#fcfcc9',
        '#fcff81',
        '#e087b6',
        '#815ea6',
        '#7eb2e6',
        '#39b2ca',
        '#b9ded1',
        '#caad64',
        '#eab372',
        '#ddddd0',
        '#e58700',
        '#810058',
        '#ac93c6',
        '#4578bb',
        '#4f607a',
        '#507051',
        '#76603f',
        '#ffffff',
        '#897f7e',
        '#7b8183',
        '#650013',
        '#220965',
        '#3b1e81',
        '#586e85',
        '#248233',
        '#6e703f',
        '#936941',
        '#8d290b',
        '#3b3f44',
        '#936b1c',
        '#0a1b32',
        '#103a21',
        '#210c07',
        '#000000',
        '#37302c',
        '#3b3f44',
        '#eeec9f',
        '#e1a479',
        '#de9c93',
        '#d97b87',
        '#e4b9d4',
        '#b9b6d7',
        '#cbe2ec',
        '#9ec6eb',
        '#d7e3f3',
        '#a2c8a5',
        '#bff59e',
        '#eeea98',
        '#bdb4b0',
        '#e9eaee',
        '#9ec6eb'
    ];

    public function index()
    {
        $colors = $this::AVATAR_COLORS;

        return view('web.account.character')->with([
            'colors' => $colors
        ]);
    }

    public function thumbnails()
    {
        return response()->json([
            'thumbnail' => Auth::user()->thumbnail()
        ]);
    }

    public function regenerate()
    {
        Auth::user()->render();

        return $this->thumbnails();
    }

    public function inventory(Request $request)
    {
        $search = (isset($request->search)) ? trim($request->search) : '';
        $type = lcfirst(itemTypeFromPlural($request->category));;

        if (!in_array($type, ['hat', 'face', 'tool', 'tshirt', 'shirt', 'pants', 'head', 'figure']))
            return response()->json(['error' => 'Invalid category.']);

        $items = Item::where([
            ['name', 'LIKE', "%{$search}%"],
            ['type', '=', $type],
            ['status', '=', 'approved']
        ])->join('inventories', 'inventories.item_id', '=', 'items.id')->where('inventories.user_id', '=', Auth::user()->id)->orderBy('inventories.created_at', 'DESC')->paginate(8);
        $avatar = Auth::user()->avatar();
        $json = ['current_page' => $items->currentPage(), 'total_pages' => $items->lastPage(), 'items' => []];

        if ($items->count() == 0)
            return response()->json(['error' => "No {$request->category} found."]);

        foreach ($items as $item) {
            $item->id = $item->item_id;

            switch ($item->type) {
                case 'hat':
                    $isWearing = ($avatar->hat_1 == $item->id || $avatar->hat_2 == $item->id || $avatar->hat_3 == $item->id || $avatar->hat_4 == $item->id || $avatar->hat_5 == $item->id);
                    break;
                case 'face':
                    $isWearing = $avatar->face == $item->id;
                    break;
                case 'tool':
                    $isWearing = $avatar->tool == $item->id;
                    break;
                case 'tshirt':
                    $isWearing = $avatar->tshirt == $item->id;
                    break;
                case 'shirt':
                    $isWearing = $avatar->shirt == $item->id;
                    break;
                case 'pants':
                    $isWearing = $avatar->pants == $item->id;
                    break;
                case 'head':
                    $isWearing = $avatar->head == $item->id;
                    break;
                case 'figure':
                    $isWearing = $avatar->figure == $item->id;
                    break;
            }

            $json['items'][] = ['id' => $item->id, 'name' => e($item->name), 'type' => $item->type, 'thumbnail' => $item->thumbnail(), 'url' => route('shop.item', $item->id), 'is_wearing' => $isWearing];
        }

        return response()->json($json);
    }

    public function wearing()
    {
        $avatar = Auth::user()->avatar();
        $items = [];

        if (
            !$avatar->hat_1 &&
            !$avatar->hat_2 &&
            !$avatar->hat_3 &&
            !$avatar->hat_4 &&
            !$avatar->hat_5 &&
            !$avatar->face &&
            !$avatar->tool &&
            !$avatar->tshirt &&
            !$avatar->shirt &&
            !$avatar->pants &&
            !$avatar->head &&
            !$avatar->figure
        ) return response()->json(['error' => 'You are not wearing any items.']);

        if ($avatar->hat_1)
            $items[] = ['name' => e($avatar->hat(1)->name), 'thumbnail' => $avatar->hat(1)->thumbnail(), 'url' => route('shop.item', $avatar->hat(1)->id), 'type' => 'hat_1'];

        if ($avatar->hat_2)
            $items[] = ['name' => e($avatar->hat(2)->name), 'thumbnail' => $avatar->hat(2)->thumbnail(), 'url' => route('shop.item', $avatar->hat(2)->id), 'type' => 'hat_2'];

        if ($avatar->hat_3)
            $items[] = ['name' => e($avatar->hat(3)->name), 'thumbnail' => $avatar->hat(3)->thumbnail(), 'url' => route('shop.item', $avatar->hat(3)->id), 'type' => 'hat_3'];

        if ($avatar->hat_4)
            $items[] = ['name' => e($avatar->hat(4)->name), 'thumbnail' => $avatar->hat(4)->thumbnail(), 'url' => route('shop.item', $avatar->hat(4)->id), 'type' => 'hat_4'];

        if ($avatar->hat_5)
            $items[] = ['name' => e($avatar->hat(5)->name), 'thumbnail' => $avatar->hat(5)->thumbnail(), 'url' => route('shop.item', $avatar->hat(5)->id), 'type' => 'hat_5'];

        if ($avatar->face)
            $items[] = ['name' => e($avatar->face()->name), 'thumbnail' => $avatar->face()->thumbnail(), 'url' => route('shop.item', $avatar->face()->id), 'type' => 'face'];

        if ($avatar->tool)
            $items[] = ['name' => e($avatar->tool()->name), 'thumbnail' => $avatar->tool()->thumbnail(), 'url' => route('shop.item', $avatar->tool()->id), 'type' => 'tool'];

        if ($avatar->tshirt)
            $items[] = ['name' => e($avatar->tshirt()->name), 'thumbnail' => $avatar->tshirt()->thumbnail(), 'url' => route('shop.item', $avatar->tshirt()->id), 'type' => 'tshirt'];

        if ($avatar->shirt)
            $items[] = ['name' => e($avatar->shirt()->name), 'thumbnail' => $avatar->shirt()->thumbnail(), 'url' => route('shop.item', $avatar->shirt()->id), 'type' => 'shirt'];

        if ($avatar->pants)
            $items[] = ['name' => e($avatar->pants()->name), 'thumbnail' => $avatar->pants()->thumbnail(), 'url' => route('shop.item', $avatar->pants()->id), 'type' => 'pants'];

        if ($avatar->head)
            $items[] = ['name' => e($avatar->head()->name), 'thumbnail' => $avatar->head()->thumbnail(), 'url' => route('shop.item', $avatar->head()->id), 'type' => 'head'];

        if ($avatar->figure)
            $items[] = ['name' => e($avatar->figure()->name), 'thumbnail' => $avatar->figure()->thumbnail(), 'url' => route('shop.item', $avatar->figure()->id), 'type' => 'figure'];

        return response()->json($items);
    }

    public function update(Request $request)
    {
        $avatar = Auth::user()->avatar();

        switch ($request->action) {
            case 'wear':
                $item = Item::where('id', '=', $request->id);

                if (!$item->exists())
                    return response()->json(['error' => 'Invalid item.']);

                $item = $item->first();
                $column = ($item->type == 'hat') ? 'hat_1' : $item->type;

                if (!Auth::user()->ownsItem($item->id))
                    return response()->json(['error' => 'You do not own this item.']);

                if ($item->status != 'approved')
                    return response()->json(['error' => 'This item is not approved.']);

                if ($item->type == 'hat') {
                    if (!$avatar->hat_1)
                        $column = 'hat_1';
                    else if (!$avatar->hat_2)
                        $column = 'hat_2';
                    else if (!$avatar->hat_3)
                        $column = 'hat_3';
                    else if (!$avatar->hat_4)
                        $column = 'hat_4';
                    else if (!$avatar->hat_5)
                        $column = 'hat_5';
                }

                $avatar->$column = $item->id;
                $avatar->save();

                return $this->regenerate();
            case 'remove':
                if (!in_array($request->type, ['hat_1', 'hat_2', 'hat_3', 'hat_4', 'hat_5', 'face', 'tool', 'tshirt', 'shirt', 'pants', 'head', 'figure']))
                    return response()->json(['error' => 'Invalid type.']);

                $avatar->{$request->type} = null;
                $avatar->save();

                return $this->regenerate();
            case 'color':
                $colors = $this::AVATAR_COLORS;

                if (!in_array($request->body_part, ['head', 'torso', 'left_arm', 'right_arm', 'left_leg', 'right_leg']))
                    return response()->json(['error' => 'Invalid body part.']);

                if (!in_array($request->color, $colors))
                    return response()->json(['error' => 'Invalid color.']);

                if ($avatar->{"color_{$request->body_part}"} == $request->color)
                    return $this->thumbnails();

                $avatar->{"color_{$request->body_part}"} = $request->color;
                $avatar->save();

                return $this->regenerate();
            default:
                return response()->json(['error' => 'Invalid action.']);
        }
    }
}
