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
use App\Models\User;
use App\Models\Trade;
use App\Jobs\RenderUser;
use App\Models\Inventory;
use App\Models\ItemReseller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TradesController extends Controller
{
    public function index(Request $request)
    {
        return view('web.account.trades.index');
    }

    public function send($id)
    {
        $user = User::where('id', '=', $id)->firstOrFail();

        if (Auth::user()->id == $user->id || $user->isBanned()) abort(404);

        $giving = $this->inventory(Auth::user()->id);
        $receiving = $this->inventory($user->id);

        return view('web.account.trades.send')->with([
            'user' => $user,
            'giving' => $giving,
            'receiving' => $receiving
        ]);
    }

    public function process(Request $request)
    {
        switch ($request->action) {
            case 'send':
                $user = User::where('id', '=', $request->id);

                if (!$user->exists() || Auth::user()->id == $user->first()->id || $user->first()->isBanned())
                    return response()->json(['error' => 'Invalid user.']);

                $user = $user->first();

                $givingItems = [];
                if ($request->g1) $givingItems[] = $request->g1;
                if ($request->g2) $givingItems[] = $request->g2;
                if ($request->g3) $givingItems[] = $request->g3;
                if ($request->g4) $givingItems[] = $request->g4;

                $receivingItems = [];
                if ($request->r1) $receivingItems[] = $request->r1;
                if ($request->r2) $receivingItems[] = $request->r2;
                if ($request->r3) $receivingItems[] = $request->r3;
                if ($request->r4) $receivingItems[] = $request->r4;

                if (empty($givingItems) || empty($receivingItems))
                    return response()->json(['error' => 'You need to give and request at least one (1) item.']);

                if (($request->gCurrency && !is_numeric($request->gCurrency)) || ($request->rCurrency && !is_numeric($request->rCurrency)))
                    return response()->json(['error' => 'Invalid bucks amounts.']);

                foreach ($givingItems as $item) {
                    $item = Inventory::where([
                        ['id', '=', $item],
                        ['user_id', '=', Auth::user()->id]
                    ]);

                    if (!$item->exists())
                        return response()->json(['error' => 'You do not own one of the giving items.']);

                    $item = $item->first()->item;

                    if (!$item->special_type)
                        return response()->json(['error' => 'One of the giving items is not a special.']);
                }

                foreach ($receivingItems as $item) {
                    $item = Inventory::where([
                        ['id', '=', $item],
                        ['user_id', '=', $user->id]
                    ]);

                    if (!$item->exists())
                        return response()->json(['error' => 'Receiver does not own one of the giving items.']);

                    $item = $item->first()->item;

                    if (!$item->special_type)
                        return response()->json(['error' => 'One of the receiving items is not a special.']);
                }

                if ($request->gCurrency > Auth::user()->currency_bucks)
                    return response()->json(['error' => 'You are offering more bucks than you actually have.']);

                $trade = new Trade;
                $trade->receiver_id = $user->id;
                $trade->sender_id = Auth::user()->id;
                $trade->giving_1 = $request->g1;
                $trade->giving_2 = $request->g2;
                $trade->giving_3 = $request->g3;
                $trade->giving_4 = $request->g4;
                $trade->giving_currency = $request->gCurrency;
                $trade->receiving_1 = $request->r1;
                $trade->receiving_2 = $request->r2;
                $trade->receiving_3 = $request->r3;
                $trade->receiving_4 = $request->r4;
                $trade->receiving_currency = $request->rCurrency;
                $trade->save();

                return response(['url' => route('account.trades.index')]);
            case 'accept':
                $trade = Trade::where([
                    ['id', '=', $request->id],
                    ['status', '=', 'pending']
                ])->firstOrFail();

                if (Auth::user()->id != $trade->receiver->id) abort(404);

                $givingItems = [];
                if ($trade->giving_1) $givingItems[] = $trade->giving_1;
                if ($trade->giving_2) $givingItems[] = $trade->giving_2;
                if ($trade->giving_3) $givingItems[] = $trade->giving_3;
                if ($trade->giving_4) $givingItems[] = $trade->giving_4;

                $receivingItems = [];
                if ($trade->receiving_1) $receivingItems[] = $trade->receiving_1;
                if ($trade->receiving_2) $receivingItems[] = $trade->receiving_2;
                if ($trade->receiving_3) $receivingItems[] = $trade->receiving_3;
                if ($trade->receiving_4) $receivingItems[] = $trade->receiving_4;

                foreach ($givingItems as $item) {
                    $itemExists = Inventory::where([
                        ['id', '=', $item],
                        ['user_id', '=', $trade->sender->id]
                    ])->exists();

                    if (!$itemExists) {
                        $trade->status = 'declined';
                        $trade->save();

                        return back()->withErrors(['You do not own one of the giving items.']);
                    }
                }

                foreach ($receivingItems as $item) {
                    $itemExists = Inventory::where([
                        ['id', '=', $item],
                        ['user_id', '=', $trade->receiver->id]
                    ])->exists();

                    if (!$itemExists) {
                        $trade->status = 'declined';
                        $trade->save();

                        return back()->withErrors(['Receiver does not own one of the giving items.']);
                    }
                }

                if ($trade->giving_currency && $trade->giving_currency > $trade->sender->currency_bucks) {
                    $trade->status = 'declined';
                    $trade->save();

                    return back()->withErrors(['Sender does not have enough bucks so the trade has been declined.']);
                }

                if ($trade->receiving_currency && $trade->receiving_currency > $trade->receiver->currency_bucks)
                    return back()->withErrors(['You do not have enough bucks.']);

                $trade->status = 'accepted';
                $trade->save();

                foreach ($givingItems as $item) {
                    $listing = ItemReseller::where('inventory_id', '=', $item);

                    if ($listing->exists())
                        $listing->first()->delete();

                    $inventory = Inventory::where('id', '=', $item)->first();
                    $inventory->user_id = $trade->receiver->id;
                    $inventory->save();
                }

                foreach ($receivingItems as $item) {
                    $listing = ItemReseller::where('inventory_id', '=', $item);

                    if ($listing->exists())
                        $listing->first()->delete();

                    $inventory = Inventory::where('id', '=', $item)->first();
                    $inventory->user_id = $trade->sender->id;
                    $inventory->save();
                }

                if ($trade->giving_currency) {
                    $trade->sender->currency_bucks -= $trade->giving_currency;
                    $trade->sender->save();

                    $trade->receiver->currency_bucks += $trade->giving_currency;
                    $trade->receiver->save();
                }

                if ($trade->receiving_currency) {
                    $trade->sender->currency_bucks += $trade->receiving_currency;
                    $trade->sender->save();

                    $trade->receiver->currency_bucks -= $trade->receiving_currency;
                    $trade->receiver->save();
                }

                foreach ($givingItems as $item) {
                    $inventory = Inventory::where('id', '=', $item)->first();

                    if (!$trade->sender->ownsItem($inventory->item_id) && $trade->sender->isWearingItem($inventory->item_id)) {
                        $trade->sender->takeOffItem($inventory->item_id);

                        RenderUser::dispatch($trade->sender->id);
                    }
                }

                foreach ($receivingItems as $item) {
                    $inventory = Inventory::where('id', '=', $item)->first();

                    if (!$trade->receiver->ownsItem($inventory->item_id) && $trade->receiver->isWearingItem($inventory->item_id)) {
                        $trade->receiver->takeOffItem($inventory->item_id);

                        RenderUser::dispatch($trade->receiver->id);
                    }
                }

                return back()->with('success_message', 'Trade has been accepted.');
            case 'decline':
                $trade = Trade::where([
                    ['id', '=', $request->id],
                    ['status', '=', 'pending']
                ])->firstOrFail();

                if ($trade->receiver->id != Auth::user()->id && $trade->sender->id != Auth::user()->id) abort(404);

                $trade->status = 'declined';
                $trade->save();

                return back()->with('success_message', 'Trade has been declined.');
            default:
                return response()->json(['error' => 'Invalid action.']);
        }
    }

    public function inventory($id)
    {
        $user = User::where('id', '=', $id)->first();

        $items = Item::where([
            ['special_type', '!=', null],
            ['stock', '<=', 0],
            ['public_view', '=', true]
        ])->join('inventories', 'inventories.item_id', '=', 'items.id')->where('inventories.user_id', '=', $user->id)->orderBy('inventories.created_at', 'DESC')->get();

        foreach ($items as $item) {
            $inventoryItem = Inventory::where('id', '=', $item->id)->first();

            $item->recent_average_price = $inventoryItem->item->recentAveragePrice();
            $item->serial = $inventoryItem->serial();
        }

        return $items;
    }
}
