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
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GamesController extends Controller
{
    public function publish(Request $request)
    {
        $game = Game::where('id', '=', $request->id);

        if (!$game->exists())
            return response('bruh', 404);

        $game = $game->first();

        if ($request->hasFile('thumbnail')) {
            $filename = Str::random(50);

            if (Storage::exists("thumbnails/games/{$game->thumbnail}.png"))
                Storage::delete("thumbnails/games/{$game->thumbnail}.png");

            $game->thumbnail = $filename;

            $thumbnail = imagecreatefromstring($request->file('thumbnail')->get());
            $img = imagecreatetruecolor(892, 640);

            imagealphablending($img, false);
            imagesavealpha($img, true);
            imagefilledrectangle($img, 0, 0, 892, 640, imagecolorallocatealpha($img, 255, 255, 255, 127));
            imagecopyresampled($img, $thumbnail, 0, 0, 0, 0, 892, 640, imagesx($thumbnail), imagesy($thumbnail));

            $thumbnail = $img;
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);

            if (!Storage::exists('thumbnails/games/'))
                Storage::makeDirectory('thumbnails/games/');

            Storage::put("thumbnails/games/{$filename}.png", Image::make($thumbnail)->encode('png'));
        }

        $game->save();

        return 'SUCCESS';
    }

    public function upload(Request $request)
    {
        $game = Game::where('id', '=', $request->id);

        if (!$game->exists())
            return response('bruh', 404);

        $game = $game->first();

        if ($request->hasFile('brk')) {
            if (!Storage::exists('brks'))
                Storage::makeDirectory('brks');

            Storage::put("brks/{$game->id}.brk", $request->file('brk')->get());
        }

        return 'SUCCESS';
    }
}
