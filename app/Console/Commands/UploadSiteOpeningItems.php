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

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UploadSiteOpeningItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload_site_opening_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload the first few iems for the website release.';

    /**
     * The items to be uploaded.
     *
     * @var array
     */
    protected $items = [
        [
            // Brick Hill's Cap
            'id' => 70
        ],
        [
            // Chef Hat
            'id' => 77,
            'bits' => 30
        ],
        [
            // Clumsy Lumberjack
            'id' => 67,
            'bits' => 10
        ],
        [
            // Ninja Mask
            'id' => 207,
            'bucks' => 2
        ],
        [
            // brick-luke's Fedora
            'id' => 20,
            'name' => 'Fedora',
            'description' => 'Fedoras are for federal agents.',
            'bits' => 100,
            'bucks' => 10
        ],
        [
            // Blue Top Hat
            'id' => 203,
            'bits' => 75
        ],
        [
            // Balloon
            'id' => 216,
            'bits' => 5
        ],
        [
            // Light Blue Shades
            'id' => 270,
            'bucks' => 2
        ],
        [
            // Pirate Hat
            'id' => 263,
            'bits' => 15
        ],
        [
            // Swashbuckler Face
            'id' => 259,
            'bits' => 10
        ],
        [
            // Red Velvet Fedora
            'id' => 11932,
            'bits' => 50
        ],
        [
            // Donate Sign
            'id' => 188603
        ],
        [
            // o.O
            'id' => 20741
        ],
        [
            // The Leon
            'id' => 40785,
            'description' => 'Who says you need to have proper programming experience to get a job? Not Brick Hill!',
            'bucks' => 2
        ],
        [
            // Pineapple Fedora
            'id' => 63695,
            'offsale' => true
        ],
        [
            // Head of Considerable Length
            'id' => 1577,
            'bits' => 250
        ],
        [
            // Stop Sign
            'id' => 21300,
            'bits' => 20
        ],
        [
            // Red-Dipped Dreads
            'id' => 105513,
            'bits' => 5
        ],
        [
            // Backwards Bandana
            'id' => 83670,
            'bits' => 35
        ],
        [
            // Corn Suit
            'id' => 33023,
            'bits' => 32
        ],
        [
            // aeo's Header
            'id' => 28796,
            'description' => 'Rewarded to users who received the Jackpot award.',
            'offsale' => true
        ]
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->items as $data) {
            $response = Http::get("https://api.brick-hill.com/v1/shop/{$data['id']}")->json();

            if (isset($response['error']))
                continue;

            $filename = Str::random(50);

            $item = new Item;
            $item->creator_id = 1;
            $item->name = $data['name'] ?? $response['data']['name'];
            $item->description = $data['description'] ?? $response['data']['description'];
            $item->type = $response['data']['type']['type'];
            $item->status = 'approved';
            $item->price_bits = $data['bits'] ?? 0;
            $item->price_bucks = $data['bucks'] ?? 0;
            $item->onsale = (isset($data['offsale']) && $data['offsale']) ? false : true;
            $item->filename = $filename;
            $item->save();

            if ($item->type != 'head')
                Storage::put("uploads/{$filename}.png", get_brick_hill_asset($data['id'], 'texture'));

            if ($item->type != 'face')
                Storage::put("uploads/{$filename}.obj", get_brick_hill_asset($data['id'], 'mesh'));

            echo "Rendering item \"{$item->name}\" ({$item->id}).\n";
            $item->render();
            sleep(3);
        }
    }
}
