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

use App\Models\Clan;
use App\Models\ClanMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClansController extends Controller
{
    public function members($id, $rank, Request $request)
    {
        $clan = Clan::where('id', '=', $id);

        if (!$clan->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $clan = $clan->first();
        $json = [];
        $limit = (in_array((integer) $request->limit, [1, 10, 20, 25, 50, 100])) ? $request->limit : 10;
        $members = ClanMember::where([
            ['clan_id', '=', $clan->id],
            ['rank', '=', $rank]
        ])->orderBy('updated_at', 'DESC')->paginate($limit);

        foreach ($members as $member) {
            $image = $member->user->avatar()->image;

            $json[] = [
                'id' => $member->id,
                'clan_id' => $member->clan_id,
                'user_id' => $member->user_id,
                'rank' => $member->rank,
                'status' => 'accepted',
                'created_at' => $member->created_at,
                'updated_at' => $member->updated_at,
                'user' => [
                    'id' => $member->user->id,
                    'username' => $member->user->username,
                    'avatar_hash' => ($image == 'default') ? config('site.renderer.default_filename') : $image,
                    'thumbnail' => $member->user->thumbnail(),
                    'url' => route('users.profile', $member->user->id)
                ]
            ];
        }

        return response()->json([
            'data'         => $json,
            'current_page' => $members->currentPage(),
            'total_pages'  => $members->lastPage()
        ]);
    }
}
