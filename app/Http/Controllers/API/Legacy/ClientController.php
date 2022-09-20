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

namespace App\Http\Controllers\API\Legacy;

use App\Models\Game;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', '=', $request->username);

        if (!$user->exists())
            return 'ERROR Invalid username.';

        $user = $user->first();

        if (!password_verify($request->password, $user->password))
            return 'ERROR Incorrect password.';

        return "SUCCESS {$user->id}";
    }

    public function games(Request $request)
    {
        $games = Game::where('creator_id', '=', $request->id)->get();
        $output = '';

        foreach ($games as $game)
            $output .= "{$game->id} {$game->name}\n";

        return $output;
    }

    public function assetTexture(Request $request)
    {
        $file = "uploads/{$request->id}.png";

        if (!Storage::exists($file))
            return response('bruh', 404);

        return response(Storage::get($file), 200, ['Content-Type' => 'image/png']);
    }

    public function assetD3D(Request $request)
    {
        $file = "client/{$request->id}.d3d";

        if (!Storage::exists($file))
            return response('bruh', 404);

        return response(Storage::get($file), 200, ['Content-Type' => 'text/plain']);
    }

    public function getAvatar(Request $request)
    {
        $avatar = UserAvatar::where('user_id', '=', $request->id);

        if (!$avatar->exists())
            return response('bruh', 404);

        $avatar = $avatar->first();
        $array = [];

        $array[] = str_replace('#', '', $avatar->color_head);
        $array[] = str_replace('#', '', $avatar->color_torso);
        $array[] = str_replace('#', '', $avatar->color_right_arm);
        $array[] = str_replace('#', '', $avatar->color_left_arm);
        $array[] = str_replace('#', '', $avatar->color_right_leg);
        $array[] = str_replace('#', '', $avatar->color_left_leg);
        $array[] = $avatar->face ?? 0;
        $array[] = $avatar->tshirt ?? 0;
        $array[] = $avatar->shirt ?? 0;
        $array[] = $avatar->pants ?? 0;

        for ($i = 1; $i <= 5; $i++)
            $array[] = $avatar->{"hat_{$i}"} ?? 0;

        return implode(',', $array);
    }
}
