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

use App\Models\Clan;
use App\Models\ClanRank;
use App\Models\ClanMember;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ClansController extends Controller
{
    public function index()
    {
        $search = (isset($request->search)) ? trim($request->search) : '';
        $clans = Clan::where('name', 'LIKE', "%{$search}%")->join('clan_members', 'clan_members.clan_id', '=', 'clans.id')->select('clans.*', DB::raw('COUNT(clan_members.id) AS member_count'))->groupBy([
            'clans.id',
            'clans.owner_id',
            'clans.tag',
            'clans.name',
            'clans.description',
            'clans.is_private',
            'clans.thumbnail',
            'clans.is_thumbnail_pending',
            'clans.created_at',
            'clans.updated_at'
        ])->orderBy('member_count', 'DESC')->paginate(20);

        return view('web.clans.index')->with([
            'clans' => $clans
        ]);
    }

    public function create()
    {
        return view('web.clans.create');
    }

    public function purchase(Request $request)
    {
        $filename = Str::random(50);
        $price = config('site.clan_creation_price');
        $ranks = [
            [
                'name' => 'Owner',
                'rank' => 100,
                'permissions' => [
                    'can_post_wall',
                    'can_moderate_wall',
                    'can_invite_users',
                    'can_manage_relations',
                    'can_rank_members',
                    'can_manage_ranks',
                    'can_edit_description',
                    'can_post_shout',
                    'can_add_funds',
                    'can_take_funds',
                    'can_edit_clan'
                ]
            ],
            [
                'name' => 'Admin',
                'rank' => 99,
                'permissions' => [
                    'can_post_wall',
                    'can_moderate_wall',
                    'can_rank_members'
                ]
            ],
            [
                'name' => 'Member',
                'rank' => 1,
                'permissions' => [
                    'can_post_wall'
                ]
            ]
        ];

        $this->validate($request, [
            'tag' => ['required', 'min:2', 'max:4', 'regex:/^[a-z0-9 .\-!,\':;<>?()\[\]+=\/]+$/i'],
            'name' => ['required', 'min:3', 'max:30', 'regex:/^[a-z0-9 .\-!,\':;<>?()\[\]+=\/]+$/i', 'unique:clans'],
            'description' => ['max:1024'],
            'image' => ['required', 'dimensions:min_width=100,min_height=100', 'mimes:png,jpg,jpeg', 'max:2048']
        ]);

        if (Auth::user()->currency_bucks < $price)
            return back()->withErrors(["You need at least {$price} bucks to create a clan."]);

        if (Auth::user()->reachedClanLimit())
            return back()->withErrors(['You have reached the limit of clans you can be apart of.']);

        $clan = new Clan;
        $clan->owner_id = Auth::user()->id;
        $clan->tag = $request->tag;
        $clan->name = $request->name;
        $clan->description = $request->description;
        $clan->thumbnail = $filename;
        $clan->save();

        foreach ($ranks as $rank) {
            $clanRank = new ClanRank;
            $clanRank->clan_id = $clan->id;
            $clanRank->name = $rank['name'];
            $clanRank->rank = $rank['rank'];

            foreach ($rank['permissions'] as $permission)
                $clanRank->$permission = true;

            $clanRank->save();
        }

        $clanMember = new ClanMember;
        $clanMember->user_id = Auth::user()->id;
        $clanMember->clan_id = $clan->id;
        $clanMember->rank = 100;
        $clanMember->save();

        $user = Auth::user();
        $user->currency_bucks -= $price;
        $user->save();

        $logo = imagecreatefromstring($request->file('image')->get());
        $img = imagecreatetruecolor(420, 420);

        imagealphablending($img, false);
        imagesavealpha($img, true);
        imagefilledrectangle($img, 0, 0, 420, 420, imagecolorallocatealpha($img, 255, 255, 255, 127));
        imagecopyresampled($img, $logo, 0, 0, 0, 0, 420, 420, imagesx($logo), imagesy($logo));

        $logo = $img;
        imagealphablending($logo, false);
        imagesavealpha($logo, true);

        if (!Storage::exists('thumbnails/clans/'))
            Storage::makeDirectory('thumbnails/clans/');

        Storage::put("thumbnails/clans/{$filename}.png", Image::make($logo)->encode('png'));

        return redirect()->route('clans.view', $clan->id)->with('success_message', 'Clan has been created.');
    }

    public function view($id)
    {
        $clan = Clan::where('id', '=', $id)->firstOrFail();

        return view('web.clans.view')->with([
            'clan' => $clan
        ]);
    }

    public function edit($id)
    {
        $clan = Clan::where('id', '=', $id)->firstOrFail();

        if (Auth::user()->id != $clan->owner->id) abort(403);

        return view('web.clans.edit')->with([
            'clan' => $clan
        ]);
    }

    public function update(Request $request)
    {
        //
    }

    public function membership(Request $request)
    {
        $user = Auth::user();
        $clan = Clan::where('id', '=', $request->id)->firstOrFail();
        $isInClan = $user->isInClan($clan->id);
        $message = (!$isInClan) ? 'You have joined this clan.' : 'You have left this clan.';

        if ($user->id == $clan->owner->id) abort(403);

        if (!$isInClan) {
            if ($user->reachedClanLimit())
                return back()->withErrors(['You have reached the limit of clan you can be apart of.']);

            $clanMember = new ClanMember;
            $clanMember->user_id = $user->id;
            $clanMember->clan_id = $clan->id;
            $clanMember->rank = 1;
            $clanMember->save();
        } else {
            if ($user->primary_clan_id == $clan->id) {
                $user->primary_clan_id = null;
                $user->save();
            }

            $clanMember = ClanMember::where([
                ['user_id', '=', $user->id],
                ['clan_id', '=', $clan->id]
            ])->first();

            $clanMember->delete();
        }

        return back()->with('success_message', $message);
    }

    public function primary(Request $request)
    {
        $user = Auth::user();
        $clan = Clan::where('id', '=', $request->id)->firstOrFail();
        $isInClan = $user->isInClan($clan->id);

        if ($clan->id == $user->primary_clan_id) {
            $user->primary_clan_id = null;
            $user->save();

            return back()->with('success_message', 'This is no longer your primary clan.');
        }

        if (!$isInClan)
            return back()->withErrors(['You are not a member of this clan.']);

        $user->primary_clan_id = $clan->id;
        $user->save();

        return back()->with('success_message', 'This is now your primary clan.');
    }
}
