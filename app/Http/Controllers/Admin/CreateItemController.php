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

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CreateItemController extends Controller
{
    public function index($type)
    {
        switch ($type) {
            case 'hat':
                $title = 'Create New Hat';

                if (!staffUser()->staff('can_create_hat_items')) abort(404);
                break;
            case 'face':
                $title = 'Create New Face';

                if (!staffUser()->staff('can_create_face_items')) abort(404);
                break;
            case 'tool':
                $title = 'Create New Tool';

                if (!staffUser()->staff('can_create_tool_items')) abort(404);
                break;
            case 'head':
                $title = 'Create New Head';

                if (!staffUser()->staff('can_create_head_items')) abort(404);
                break;
            default:
                abort(404);
        }

        return view('admin.create_item')->with([
            'title' => $title,
            'type' => $type
        ]);
    }

    public function create(Request $request)
    {
        if (
            !in_array($request->type, ['hat', 'face', 'tool', 'head']) ||
            (!staffUser()->staff('can_create_hat_items') && $request->type == 'hat') ||
            (!staffUser()->staff('can_create_face_items') && $request->type == 'face') ||
            (!staffUser()->staff('can_create_tool_items') && $request->type == 'tool') ||
            (!staffUser()->staff('can_create_head_items') && $request->type == 'head')
        ) abort(404);

        $onsale = $request->has('onsale');
        $official = $request->has('official');
        $public = $request->has('public');
        $brickHillItem = $request->has('brick_hill_item_id') && !empty($request->brick_hill_item_id);
        $auto = $request->has('auto');
        $special = $request->has('special');
        $filename = ($request->type == 'hat') ? generate_filename() : Str::random(50);
        $validate = [];

        if (!$auto) {
            $validate['name'] = ['required', 'min:1', 'max:70'];
            $validate['description'] = ['max:1024'];
        }

        if ($brickHillItem) {
            $validate['brick_hill_item_id'] = ['required', 'numeric', 'min:1'];
        } else {
            if ($request->type != 'head') {
                $validate['image'] = ($request->type != 'face') ? ['mimes:png,jpg,jpeg', 'max:2048'] : ['required', 'mimes:png,jpg,jpeg', 'max:2048'];
                $validate['material'] = ['mimes:txt', 'max:2048'];
            }

            if ($request->type != 'face')
                $validate['model'] = ['required', 'mimes:txt', 'max:2048'];
        }

        if ($onsale) {
            $validate['price_bits'] = ['required', 'numeric', 'min:0', 'max:1000000'];
            $validate['price_bucks'] = ['required', 'numeric', 'min:0', 'max:1000000'];
        }

        if ($special)
            $validate['stock'] = ['required', 'numeric', 'min:0', 'max:500'];

        $this->validate($request, $validate);

        if ($brickHillItem) {
            $response = Http::get("https://api.brick-hill.com/v1/shop/{$request->brick_hill_item_id}")->json();

            if (isset($response['error']))
                return back()->withErrors(["The Brick Hill API returned the following error: {$check['error']['prettyMessage']}"]);

            if ($response['data']['type']['type'] != $request->type)
                return back()->withErrors(['Invalid item type.']);

            if ($auto) {
                $request->name = $response['data']['name'];
                $request->description = $response['data']['description'];
            }
        }

        switch ($request->onsale_for) {
            case '1_hour':
                $time = 3600;
                break;
            case '12_hours':
                $time = 43200;
                break;
            case '1_day':
                $time = 86400;
                break;
            case '3_days':
                $time = 259200;
                break;
            case '7_days':
                $time = 604800;
                break;
            case '14_days':
                $time = 1209600;
                break;
            case '21_days':
                $time = 1814400;
                break;
            case '1_month':
                $time = 2592000;
                break;
        }

        $item = new Item;
        $item->creator_id = (!$official) ? staffUser()->id : 1;
        $item->name = $request->name;
        $item->description = $request->description;
        $item->type = $request->type;
        $item->status = 'approved';
        $item->price_bits = ($onsale) ? $request->price_bits : 0;
        $item->price_bucks = ($onsale) ? $request->price_bucks : 0;
        $item->special_type = ($special) ? 'special_edition' : null;
        $item->stock = ($special) ? $request->stock : 0;
        $item->public_view = $public;
        $item->onsale = $onsale;
        $item->filename = $filename;
        $item->onsale_until = ($onsale && isset($time)) ? Carbon::createFromTimestamp(time() + $time)->format('Y-m-d H:i:s') : null;
        $item->save();

        if ($brickHillItem) {
            if ($item->type != 'head')
                Storage::put("uploads/{$filename}.png", get_brick_hill_asset($request->brick_hill_item_id, 'texture'));

            if ($item->type != 'face')
                Storage::put("uploads/{$filename}.obj", get_brick_hill_asset($request->brick_hill_item_id, 'mesh'));
        } else {
            if ($item->type != 'head') {
                if ($request->hasFile('image'))
                    Storage::putFileAs('uploads', $request->file('image'), "{$filename}.png");

                if ($request->hasFile('material'))
                    Storage::putFileAs('uploads', $request->file('material'), "{$filename}.mtl");
            }

            if ($item->type != 'face')
                Storage::putFileAs('uploads', $request->file('model'), "{$filename}.obj");
        }

        $item->render();
        $item->notifyWebhooks(true);

        return redirect()->route('shop.item', $item->id);
    }
}
