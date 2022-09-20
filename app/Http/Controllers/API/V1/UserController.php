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

namespace App\Http\Controllers\API\V1;

use App\Models\Item;
use App\Models\User;
use App\Models\Trade;
use App\Models\Status;
use App\Models\Inventory;
use App\Models\UserAward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = User::where('id', '=', $request->id);

        if (!$user->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();
        $avatar = $user->avatar();
        $status = Status::where('creator_id', '=', $user->id)->orderBy('created_at', 'DESC')->first() ?? null;
        $awards = UserAward::where('user_id', '=', $user->id)->get();

        $awardsJson = [];
        $statusJson = (!$status) ? [] : [
            'id' => $status->id,
            'clan_id' => null,
            'owner_id' => $user->id,
            'body' => $status->message,
            'date' => $status->created_at,
            'type' => 'user'
        ];

        foreach ($awards as $award) {
            $data = config('awards')[$award->award_id];

            $aw = new \stdClass;
            $aw->id = $award->id;
            $aw->user_id = $award->user_id;
            $aw->award_id = $award->award_id;
            $aw->own = true;
            $aw->award = [
                'id' => $award->award_id,
                'name' => $data['name'],
                'description' => $data['description']
            ];

            $awardsJson[] = $aw;
        }

        return response()->json([
            'description' => e($user->description),
            'username' => $user->username,
            'id' => $user->id,
            'last_online' => $user->updated_at,
            'created_at' => $user->created_at,
            'img' => $avatar->image,
            'awards' => $awardsJson,
            'status' => $statusJson,
            'membership' => null
        ]);
    }

    public function id(Request $request)
    {
        $user = User::where('username', '=', $request->username);

        if (!$user->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username
        ]);
    }

    public function sets($userId)
    {
        $user = User::where('id', '=', $userId);

        if (!$user->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();
        $json = [];

        foreach ($user->games() as $game)
            $json[] = [
                'id' => $game->id,
                'creator_id' => $game->creator->id,
                'name' => e($game->name),
                'description' => e($game->description),
                'playing' => $game->playing,
                'visits' => $game->visits,
                'active' => $game->is_active,
                'max_players' => $game->max_players,
                'friends_only' => $game->friends_only,
                'is_dedicated' => $game->is_dedicated,
                'created_at' => $game->created_at,
                'updated_at' => $game->updated_at,
                'url' => route('games.view', $game->id),
                'thumbnail' => $game->thumbnail()
            ];

        return response()->json([
            'data' => $json
        ]);
    }

    public function crate($userId, Request $request)
    {
        $user = User::where('id', '=', $userId);

        if (!$user->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();
        $type = lcfirst(itemTypeFromPlural($request->type ?? 'all'));
        $limit = (in_array((integer) $request->limit, [1, 10, 20, 25, 50, 100])) ? $request->limit : 10;
        $json = [];
        $data = [
            ['public_view', '=', true]
        ];

        if (!in_array($type, ['all', 'specials']))
            $data[] = ['type', '=', $type];

        $items = Item::where($data)->join('inventories', 'inventories.item_id', '=', 'items.id')->where('inventories.user_id', '=', $user->id);

        if ($type == 'all')
            $items = $items->whereIn('type', ['hat', 'tool', 'face', 'head', 'figure']);
        else if ($type == 'specials')
            $items = $items->where('special_type', '!=', null);

        $items = $items->orderBy('inventories.created_at', 'DESC')->paginate($limit);

        foreach ($items as $item) {
            $serial = Inventory::where('id', '=', $item->id)->first()->serial();

            $json[] = [
                'id' => $item->id,
                'serial' => $serial,
                'item' => [
                    'id' => $item->item_id,
                    'name' => e($item->name),
                    'thumbnail' => $item->thumbnail(),
                    'url' => route('shop.item', $item->item_id),
                    'is_special' => $item->special_type
                ],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        }

        return response()->json([
            'data'         => $json,
            'current_page' => $items->currentPage(),
            'total_pages'  => $items->lastPage()
        ]);
    }

    public function owns($userId, $itemId)
    {
        $user = User::where('id', '=', $userId);
        $item = Item::where('id', '=', $itemId);

        if (!$user->exists() || !$item->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();
        $item = $item->first();

        return response()->json([
            'owns' => (boolean) $user->ownsItem($item->id)
        ]);
    }

    public function trades($userId, $category)
    {
        $user = User::where('id', '=', $userId);

        if (!$user->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $user = $user->first();
        $json = [];

        if ($user->id != Auth::user()->id)
            return response()->json([
                'error' => [
                    'message' => 'User does not have the right permissions.',
                    'prettyMessage' => 'Error authenticating, try refreshing the page"'
                ]
            ]);

        switch ($category) {
            case 'inbound':
                $trades = Trade::where([
                    ['receiver_id', '=', Auth::user()->id],
                    ['status', '=', 'pending']
                ]);
                break;
            case 'outbound':
                $trades = Trade::where([
                    ['sender_id', '=', Auth::user()->id],
                    ['status', '=', 'pending']
                ]);
                break;
            case 'history':
                $trades = Trade::where([
                    ['receiver_id', '=', Auth::user()->id],
                    ['status', '!=', 'pending']
                ])->orWhere([
                    ['sender_id', '=', Auth::user()->id],
                    ['status', '!=', 'pending']
                ]);
                break;
            default:
                return response()->json(['data' => []]);
        }

        $trades = $trades->orderBy('created_at', 'DESC')->get();

        foreach ($trades as $trade) {
            $user = ($category == 'inbound') ? $trade->sender : $trade->receiver;

            if ($user->id == Auth::user()->id && $request->category == 'history')
                $user = $trade->sender;

            $image = $user->avatar()->image;

            $json[] = [
                'id' => $trade->id,
                'is_accepted' => $trade->status == 'accepted',
                'is_pending' => $trade->status == 'pending',
                'is_cancelled' => $trade->status == 'declined',
                'has_errored' => $trade->status == 'error',
                'updated_at' => $trade->updated_at,
                'human_time' => $trade->created_at->diffForHumans(),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar_hash' => ($image == 'default') ? config('site.renderer.default_filename') : $image,
                    'thumbnail' => $user->thumbnail(),
                    'url' => route('users.profile', $user->id)
                ]
            ];
        }

        return response()->json([
            'data' => $json
        ]);
    }

    public function trade($tradeId)
    {
        $trade = Trade::where('id', '=', $tradeId);

        if (!$trade->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $trade = $trade->first();
        $json = [];

        if ($trade->receiver->id != Auth::user()->id && $trade->sender->id != Auth::user()->id)
            return response()->json([
                'error' => [
                    'message' => 'User does not have the right permissions.',
                    'prettyMessage' => 'Error authenticating, try refreshing the page"'
                ]
            ]);

        $giving = [];
        $receiving = [];
        $givingJson = [];
        $receivingJson = [];
        $receiverImage = $trade->receiver->avatar()->image;
        $senderImage = $trade->sender->avatar()->image;

        if ($trade->giving_1) {
            $item = Inventory::where('id', '=', $trade->giving_1)->first();
            $giving[$trade->giving_1] = $item;
        }

        if ($trade->giving_2) {
            $item = Inventory::where('id', '=', $trade->giving_2)->first();
            $giving[$trade->giving_2] = $item;
        }

        if ($trade->giving_3) {
            $item = Inventory::where('id', '=', $trade->giving_3)->first();
            $giving[$trade->giving_3] = $item;
        }

        if ($trade->giving_4) {
            $item = Inventory::where('id', '=', $trade->giving_4)->first();
            $giving[$trade->giving_4] = $item;
        }

        if ($trade->receiving_1) {
            $item = Inventory::where('id', '=', $trade->receiving_1)->first();
            $receiving[$trade->receiving_1] = $item;
        }

        if ($trade->receiving_2) {
            $item = Inventory::where('id', '=', $trade->receiving_2)->first();
            $receiving[$trade->receiving_2] = $item;
        }

        if ($trade->receiving_3) {
            $item = Inventory::where('id', '=', $trade->receiving_3)->first();
            $receiving[$trade->receiving_3] = $item;
        }

        if ($trade->receiving_4) {
            $item = Inventory::where('id', '=', $trade->receiving_4)->first();
            $receiving[$trade->receiving_4] = $item;
        }

        foreach ($giving as $inventoryItem) {
            $recentAveragePrice = $inventoryItem->item->recentAveragePrice();

            $givingJson[] = [
                'id' => $inventoryItem->id,
                'serial' => $inventoryItem->serial(),
                'item' => [
                    'id' => $inventoryItem->item->id,
                    'name' => $inventoryItem->item->name,
                    'thumbnail' => $inventoryItem->item->thumbnail(),
                    'is_special' => $inventoryItem->item->special_type,
                    'average_price' => $recentAveragePrice,
                    'average_price_abbr' => shorten_number($recentAveragePrice),
                    'url' => route('shop.item', $inventoryItem->item->id)
                ],
                'created_at' => $inventoryItem->created_at,
                'updated_at' => $inventoryItem->updated_at
            ];
        }

        foreach ($receiving as $inventoryItem) {
            $recentAveragePrice = $inventoryItem->item->recentAveragePrice();

            $receivingJson[] = [
                'id' => $inventoryItem->id,
                'serial' => $inventoryItem->serial(),
                'item' => [
                    'id' => $inventoryItem->item->id,
                    'name' => $inventoryItem->item->name,
                    'thumbnail' => $inventoryItem->item->thumbnail(),
                    'is_special' => $inventoryItem->item->special_type,
                    'average_price' => $recentAveragePrice,
                    'average_price_abbr' => shorten_number($recentAveragePrice),
                    'url' => route('shop.item', $inventoryItem->item->id)
                ],
                'created_at' => $inventoryItem->created_at,
                'updated_at' => $inventoryItem->updated_at
            ];
        }

        $json['id'] = $trade->id;
        $json['trade'] = [
            [
                'user' => [
                    'id' => $trade->receiver->id,
                    'username' => $trade->receiver->username,
                    'avatar_hash' => ($receiverImage == 'default') ? config('site.renderer.default_filename') : $receiverImage,
                    'thumbnail' => $trade->receiver->thumbnail(),
                    'url' => route('users.profile', $trade->receiver->id)
                ],
                'bucks' => $trade->receiving_currency ?? 0,
                'items' => $receivingJson
            ],
            [
                'user' => [
                    'id' => $trade->sender->id,
                    'username' => $trade->sender->username,
                    'avatar_hash' => ($senderImage == 'default') ? config('site.renderer.default_filename') : $senderImage,
                    'thumbnail' => $trade->sender->thumbnail(),
                    'url' => route('users.profile', $trade->sender->id)
                ],
                'bucks' => $trade->giving_currency ?? 0,
                'items' => $givingJson
            ]
        ];

        return response()->json([
            'data' => $json
        ]);
    }
}
