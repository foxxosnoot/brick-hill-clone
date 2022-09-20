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
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function renderPreview(Request $request)
    {
        $dimensions = ($request->type != 'tshirt') ? 'dimensions:min_width=512,max_height=512,min_height=512,max_height=512' : 'dimensions:min_width=100,min_height=100';
        $validator = Validator::make($request->all(), [
            'texture' => ['required', $dimensions, 'mimes:png,jpg,jpeg', 'max:2048'],
            'type' => ['in:tshirt,shirt,pants']
        ]);

        if ($validator->fails())
            return response()->json(['errors' => $validator->errors()]);

        Storage::putFileAs('uploads', $request->file('texture'), "preview_{$request->type}.png");

        $http = [
            'key' => config('site.renderer.key'),
            'id' => 'preview_' . $request->type,
            'resolution' => 375
        ];

        switch ($request->type) {
            case 'tshirt':
                $http['tshirt'] = 'preview_' . $request->type;
                break;
            case 'shirt':
                $http['shirt'] = 'preview_' . $request->type;
                break;
            case 'pants':
                $http['pants'] = 'preview_' . $request->type;
                break;
        }

        $ch = curl_init(config('site.renderer.url'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($http));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        return (isset($response->thumbnail)) ? 'data:image/png;base64,' . $response->thumbnail : response()->json(['errors' => ['Unable to render preview.']]);
    }

    public function list(Request $request)
    {
        $type = strtolower(itemTypeFromPlural($request->type ?? 'all'));
        $limit = (in_array((integer) $request->limit, [1, 10, 20, 25, 50, 100])) ? $request->limit : 10;
        $json = [];
        $data = [
            ['name', 'LIKE', "%{$request->search}%"],
            ['status', '=', 'approved'],
            ['public_view', '=', true]
        ];

        if ($type != 'all') {
            $data[] = ['type', '=', $type];
        } else {
            $data2 = $data;
            $data2[] = ['creator_id', '=', 1];

            $data[] = ['type', '!=', 'tshirt'];
            $data[] = ['type', '!=', 'shirt'];
            $data[] = ['type', '!=', 'pants'];
        }

        switch ($request->sort) {
            case 'updated':
            default:
                $sort = ['updated_at', 'DESC'];
                break;
            case 'newest':
                $sort = ['created_at', 'DESC'];
                break;
            case 'oldest':
                $sort = ['created_at', 'ASC'];
                break;
            case 'expensive':
                $sort = ['price_bits', 'DESC'];
                $sort2 = ['price_bucks', 'DESC'];
                break;
            case 'inexpensive':
                $sort = ['price_bits', 'ASC'];
                $sort2 = ['price_bucks', 'ASC'];
                break;
        }

        $items = ($type == 'all') ? Item::where($data)->orWhere($data2) : Item::where($data);
        $items = (isset($sort2)) ? $items->orderBy($sort[0], $sort[1])->orderBy($sort2[0], $sort2[1]) : $items->orderBy($sort[0], $sort[1]);
        $items = $items->paginate($limit);

        foreach ($items as $item) {
            $image = $item->creator->avatar()->image;

            $json[] = [
                'id' => $item->id,
                'creator' => [
                    'id' => $item->creator->id,
                    'username' => $item->creator->username,
                    'avatar_hash' => ($image == 'default') ? config('site.renderer.default_filename') : $image,
                    'thumbnail' => $item->creator->thumbnail(),
                    'url' => route('users.profile', $item->creator->id)
                ],
                'type' => [
                    'id' => 0,
                    'type' => $item->type
                ],
                'is_public' => $item->public_view,
                'name' => e($item->name),
                'description' => e($item->description),
                'bits' => $item->price_bits,
                'bucks' => $item->price_bucks,
                'offsale' => !$item->onsale(),
                'special_edition' => $item->special_type == 'special_edition',
                'special' => $item->special_type == 'special',
                'stock' => $item->stock,
                'timer' => $item->isTimed(),
                'timer_date' => $item->onsale_until,
                'thumbnail' => $item->thumbnail(),
                'url' => route('shop.item', $item->id),
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

    public function info($id)
    {
        $item = Item::where('id', '=', $id);

        if (!$item->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $item = $item->first();
        $image = $item->creator->avatar()->image;

        return response()->json([
            'data' => [
                'id' => $item->id,
                'creator' => [
                    'id' => $item->creator->username,
                    'username' => $item->creator->username,
                    'avatar_hash' => ($image == 'default') ? config('site.renderer.default_filename') : $image
                ],
                'type' => [
                    'id' => 0,
                    'type' => $item->type
                ],
                'is_public' => $item->public_view,
                'name' => e($item->name),
                'description' => e($item->description),
                'bits' => $item->price_bits,
                'bucks' => $item->price_bucks,
                'offsale' => !$item->onsale(),
                'special_edition' => $item->special_type == 'special_edition',
                'special' => $item->special_type == 'special',
                'stock' => $item->stock,
                'timer' => $item->isTimed(),
                'timer_date' => $item->onsale_until,
                'thumbnail' => $item->thumbnail(),
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ]
        ]);
    }

    public function owners($id, Request $request)
    {
        $item = Item::where('id', '=', $id);

        if (!$item->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $item = $item->first();
        $json = [];
        $limit = (in_array((integer) $request->limit, [1, 10, 20, 25, 50, 100])) ? $request->limit : 10;
        $inventoryItems = Inventory::where('item_id', '=', $item->id)->orderBy('created_at', 'ASC')->paginate($limit);

        foreach ($inventoryItems as $inventoryItem)
            $json[] = [
                'id' => $inventoryItem->id,
                'serial' => $inventoryItem->serial(),
                'user' => [
                    'id' => $inventoryItem->user->id,
                    'username' => $inventoryItem->user->username
                ],
                'created_at' => $inventoryItem->created_at,
                'updated_at' => $inventoryItem->updated_at
            ];

        return response()->json([
            'data'         => $json,
            'current_page' => $inventoryItems->currentPage(),
            'total_pages'  => $inventoryItems->lastPage()
        ]);
    }

    public function comments($id)
    {
        $item = Item::where('id', '=', $id);

        if (!$item->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $item = $item->first();
        $comments = $item->comments();
        $json = [];

        foreach ($comments as $comment) {
            $image = $comment->creator->avatar()->image;

            $json[] = [
                'id' => $comment->id,
                'author_id' => $comment->creator_id,
                'asset_id' => $comment->item_id,
                'comment' => e($comment->body),
                'scrubbed' => 0,
                'can_report' => !$comment->creator->isStaff(),
                'report_url' => route('report.index', ['comment', $comment->id]),
                'created_at' => $comment->created_at,
                'created_at_formatted' => $comment->created_at->format('d/m/Y h:i A'),
                'author' => [
                    'id' => $comment->creator->id,
                    'username' => $comment->creator->username,
                    'avatar_hash' => ($image == 'default') ? config('site.renderer.default_filename') : $image,
                    'thumbnail' => $comment->creator->thumbnail(),
                    'url' => route('users.profile', $comment->creator->id)
                ]
            ];
        }

        return response()->json([
            'data'         => $json,
            'current_page' => $comments->currentPage(),
            'total_pages'  => $comments->lastPage()
        ]);
    }

    public function recommended($id)
    {
        $item = Item::where('id', '=', $id);

        if (!$item->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $item = $item->first();
        $recommendations = Item::where([
            ['id', '!=', $item->id],
            ['public_view', '=', true],
            ['status', '=', 'approved'],
            ['type', '=', $item->type]
        ])->take(5)->inRandomOrder()->get();
        $json = [];

        foreach ($recommendations as $recommendation)
            $json[] = [
                'id' => $recommendation->id,
                'creator_id' => $recommendation->creator->id,
                'name' => e($recommendation->name),
                'thumbnail' => $recommendation->thumbnail(),
                'url' => route('shop.item', $recommendation->id),
                'approved' => true
            ];

        return response()->json([
            'data' => $json
        ]);
    }
}
