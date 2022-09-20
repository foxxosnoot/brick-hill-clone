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

namespace App\Http\Controllers\Web;

use App\Models\Item;
use App\Jobs\RenderUser;
use App\Models\Inventory;
use App\Models\ItemComment;
use Illuminate\Support\Str;
use App\Models\ItemFavorite;
use App\Models\ItemPurchase;
use App\Models\ItemReseller;
use Illuminate\Http\Request;
use App\Models\AssetChecksum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function index()
    {
        return view('web.shop.index');
    }

    public function create()
    {
        return view('web.shop.create');
    }

    public function upload(Request $request)
    {
        $filename = Str::random(50);
        $dimensions = ($request->type != 'tshirt') ? 'dimensions:min_width=512,max_height=512,min_height=512,max_height=512' : 'dimensions:min_width=100,min_height=100';
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'min:3', 'max:70', 'regex:/^[a-z0-9 .\-!,\':;<>?()\[\]+=\/]+$/i'],
            'file' => ['required', $dimensions, 'mimes:png,jpg,jpeg', 'max:2048'],
            'type' => ['required', 'in:tshirt,shirt,pants']
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()]);

        $hash = md5_file($request->file('file'));
        $checksum = AssetChecksum::where('hash', '=', "{$request->type}_{$hash}");

        $item = new Item;
        $item->creator_id = Auth::user()->id;
        $item->name = $request->title;
        $item->type = $request->type;
        $item->price_bits = 0;
        $item->price_bucks = 0;
        $item->onsale = false;
        $item->filename = $filename;

        if ($checksum->exists()) {
            $checksum = $checksum->first();

            $item->status = $checksum->item->status;
            $item->filename = $checksum->item->filename;
            $item->thumbnail_url = $checksum->item->thumbnail_url;
        }

        $item->save();

        $inventory = new Inventory;
        $inventory->user_id = Auth::user()->id;
        $inventory->item_id = $item->id;
        $inventory->save();

        if (!$checksum->exists()) {
            if ($item->type != 'tshirt') {
                Storage::putFileAs('uploads', $request->file('file'), "{$filename}.png");
            } else {
                $tshirt = imagecreatefromstring($request->file('file')->get());
                $img = imagecreatetruecolor(420, 420);

                imagealphablending($img, false);
                imagesavealpha($img, true);
                imagefilledrectangle($img, 0, 0, 420, 420, imagecolorallocatealpha($img, 255, 255, 255, 127));
                imagecopyresampled($img, $tshirt, 0, 0, 0, 0, 420, 420, imagesx($tshirt), imagesy($tshirt));

                $tshirt = $img;
                imagealphablending($tshirt, false);
                imagesavealpha($tshirt, true);

                Storage::put("uploads/{$filename}.png", Image::make($tshirt)->encode('png'));
            }
        }

        return response()->json(['url' => route('shop.item', $item->id)]);
    }

    public function item($id)
    {
        $item = Item::where('id', '=', $id)->firstOrFail();

        if (!$item->public_view && (!Auth::check() || (!Auth::user()->ownsItem($item->id) && !Auth::user()->isStaff()))) abort(403);

        return view('web.shop.item')->with([
            'item' => $item
        ]);
    }

    public function edit($id)
    {
        $item = Item::where([
            ['id', '=', $id],
            ['creator_id', '=', Auth::user()->id]
        ])->whereIn('type', ['tshirt', 'shirt', 'pants', 'outfit'])->firstOrFail();

        return view('web.shop.edit')->with([
            'item' => $item
        ]);
    }

    public function update(Request $request)
    {
        $onsale = $request->has('onsale');
        $item = Item::where([
            ['id', '=', $request->id],
            ['creator_id', '=', Auth::user()->id]
        ])->whereIn('type', ['tshirt', 'shirt', 'pants', 'outfit'])->firstOrFail();

        $this->validate($request, [
            'name' => ['required', 'min:3', 'max:70', 'regex:/^[a-z0-9 .\-!,\':;<>?()\[\]+=\/]+$/i'],
            'price_bits' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'price_bucks' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'description' => ['max:1024']
        ]);

        $item->name = $request->name;
        $item->price_bits = $request->price_bits;
        $item->price_bucks = $request->price_bucks;
        $item->description = $request->description;
        $item->onsale = $onsale;
        $item->save();

        return redirect()->route('shop.item', $item->id)->with('success_message', 'Item has been updated.');
    }

    public function purchase(Request $request)
    {
        $item = Item::where('id', '=', $request->id)->firstOrFail();
        $item->timestamps = false;

        $listing = ItemReseller::where('id', '=', $request->reseller_id);
        $isReseller = $request->has('reseller_id') && $listing->exists();

        if ($isReseller)
            $listing = $listing->first();

        if (!Auth::user()->isStaff() && !$item->public_view) abort(403);

        $this->validate(request(), [
            'currency' => ['required', 'in:bits,bucks,free']
        ], [
            'currency.required' => 'You must provide a currency to pay with.'
        ]);

        if ($item->status != 'approved')
            return back()->withErrors(['This item is not approved.']);

        if (!$isReseller && $item->special_type && $item->stock <= 0)
            return back()->withErrors(['This item is out of stock.']);

        if (!$isReseller && !$item->onsale())
            return back()->withErrors(['This item is not on sale.']);

        if ($isReseller && Auth::user()->id == $listing->seller->id)
            return back()->withErrors(['You can not buy your own item.']);

        if (!$isReseller && Auth::user()->ownsItem($item->id))
            return back()->withErrors(['You already own this item.']);

        if ($isReseller && $request->currency != 'bucks')
            return back()->withErrors(['You can only buy resold items for bucks.']);

        switch ($request->currency) {
            case 'bits':
                $price = $item->price_bits;
                $column = 'currency_bits';

                if ($price == 0)
                    return back()->withErrors(['This item can not be purchased with bits.']);
                break;
            case 'bucks':
                $price = ($isReseller) ? $listing->price : $item->price_bucks;
                $column = 'currency_bucks';

                if ($price == 0)
                    return back()->withErrors(['This item can not be purchased with bucks.']);
                break;
            case 'free':
                $price = 0;
                $column = null;

                if ($item->price_bits > 0 && $item->price_bucks > 0)
                    return back()->withErrors(['This item can not be purchased for free.']);
                break;
            default:
                abort(404);
        }

        $seller = (!$isReseller) ? $item->creator : $listing->seller;

        if ($request->currency != 'free') {
            if (Auth::user()->$column < $price && $price > 0)
                return back()->withErrors(["You do not have enough {$request->currency} to purchase this item."]);

            $seller->timestamps = false;
            $seller->$column += round(($price / 1.3), 0, PHP_ROUND_HALF_UP);
            $seller->save();

            $myU = Auth::user();
            $myU->$column -= $price;
            $myU->save();
        }

        if ($isReseller) {
            $inventory = $listing->inventory;
            $inventory->user_id = Auth::user()->id;
            $inventory->save();

            $listing->delete();

            if (!$seller->ownsItem($item->id) && $seller->isWearingItem($item->id)) {
                $seller->takeOffItem($item->id);

                RenderUser::dispatch($seller->id);
            }
        } else {
            $inventory = new Inventory;
            $inventory->user_id = Auth::user()->id;
            $inventory->item_id = $item->id;
            $inventory->save();
        }

        $purchase = new ItemPurchase;
        $purchase->seller_id = $seller->id;
        $purchase->buyer_id = Auth::user()->id;
        $purchase->item_id = $item->id;
        $purchase->ip = Auth::user()->lastIP();
        $purchase->currency_used = $request->currency;
        $purchase->price = $price;
        $purchase->save();

        if ($item->special_type && $item->stock > 0) {
            $item->stock -= 1;
            $item->save();
        }

        return back()->with('success_message', 'You now own this item!');
    }

    public function resell(Request $request)
    {
        $copy = Inventory::where([
            ['id', '=', $request->id],
            ['user_id', '=', Auth::user()->id]
        ])->firstOrFail();

        $isReselling = ItemReseller::where('inventory_id', '=', $copy->id)->exists();

        if (!$copy->item->special_type || ($copy->item->special_type && $copy->item->stock > 0) || $isReselling) abort(404);

        $this->validate($request, [
            'price' => ['required', 'numeric', 'min:1', 'max:1000000']
        ]);

        $reseller = new ItemReseller;
        $reseller->seller_id = Auth::user()->id;
        $reseller->item_id = $copy->item->id;
        $reseller->inventory_id = $copy->id;
        $reseller->price = $request->price;
        $reseller->save();

        return redirect()->route('shop.item', $copy->item->id)->with('success_message', 'Item has been put up for sale.');
    }

    public function takeOffSale(Request $request)
    {
        $copy = ItemReseller::where([
            ['id', '=', $request->id],
            ['seller_id', '=', Auth::user()->id]
        ])->firstOrFail();

        $id = $copy->item_id;

        $copy->delete();

        return redirect()->route('shop.item', $id)->with('success_message', 'Item has been taken off sale.');
    }

    public function favorite(Request $request)
    {
        $item = Item::where('id', '=', $request->id);

        if (!$item->exists())
            return response()->json(['success' => false]);

        $item = $item->first();

        if (!$item->public_view && (!Auth::check() || (!Auth::user()->ownsItem($item->id) && !Auth::user()->isStaff())))
            return response()->json(['success' => false]);

        if (!Auth::user()->hasFavoritedItem($item->id)) {
            $favorite = new ItemFavorite;
            $favorite->item_id = $item->id;
            $favorite->user_id = Auth::user()->id;
            $favorite->save();
        } else {
            $favorite = ItemFavorite::where([
                ['item_id', '=', $item->id],
                ['user_id', '=', Auth::user()->id]
            ])->first();

            $favorite->delete();
        }

        return response()->json(['success' => true]);
    }

    public function comment(Request $request)
    {
        $item = Item::where('id', '=', $request->id)->firstOrFail();

        if (!$item->public_view && (!Auth::check() || (!Auth::user()->ownsItem($item->id) && !Auth::user()->isStaff()))) abort(403);

        $this->validate($request, [
            'comment' => ['required', 'min:3', 'max:150']
        ]);

        $comment = new ItemComment;
        $comment->item_id = $item->id;
        $comment->creator_id = Auth::user()->id;
        $comment->body = $request->comment;
        $comment->save();

        return redirect()->route('shop.item', $item->id);
    }
}
