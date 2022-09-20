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

use App\Models\Clan;
use App\Models\Game;
use App\Models\Item;
use App\Models\User;
use App\Models\Report;
use App\Models\StaffUser;
use Illuminate\Support\Str;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

$GLOBALS['js_files'] = require_once(__DIR__ . '/../js_files.php');

function js_file($filename)
{
    $file = $GLOBALS['js_files'][$filename] ?? null;

    if (!$file)
        return;

    return asset("js/{$file}");
}

function theme_file()
{
    $config = config('themes');
    $theme = (Auth::check()) ? Auth::user()->setting->theme : $config['default'];

    return asset($config['list'][$theme]['file']);
}

function site_setting($key)
{
    $settings = SiteSettings::where('id', '=', 1)->first();

    return $settings->$key;
}

function staffUser()
{
    return User::where('id', '=', session('staff_user_site_id'))->first();
}

function pendingAssetsCount()
{
    $count = 0;

    if (Auth::user()->staff('can_review_pending_assets')) {
        $count += Item::where('status', '=', 'pending')->whereIn('type', ['tshirt', 'shirt', 'pants'])->count();
        $count += Clan::where('is_thumbnail_pending', '=', true)->count();
        $count += Game::where('is_thumbnail_pending', '=', true)->count();
    }

    return $count;
}

function pendingReportsCount()
{
    if (Auth::user()->staff('can_review_pending_reports'))
        return Report::where('is_seen', '=', false)->count();

    return 0;
}

function itemType($type, $plural = false)
{
    if ($type == 'all')
        return 'all';

    $types = config('item_types');
    $type = (array_key_exists($type, $types)) ? $types[$type][($plural) ? 1 : 0] : ucfirst($type);

    return $type;
}

function itemTypeFromPlural($type)
{
    if ($type == 'all')
        return 'all';

    $types = config('item_types');

    if ($type == 't-shirts')
        $type = 'tshirt';

    foreach ($types as $t) {
        if ($t[1] == ucfirst($type))
            return array_search($t, $types);
    }

    return ucfirst($type);
}

function ip_hash($ip)
{
    return md5(md5($ip));
}

function shorten_number($number)
{
    if ($number < 999) {
        return $number;
    } else if ($number > 999 && $number <= 9999) {
        $number = substr($number, 0, 1);

        return "{$number}K+";
    } else if ($number > 9999 && $number <= 99999) {
        $number = substr($number, 0, 2);

        return "{$number}K+";
    } else if ($number > 99999 && $number <= 999999) {
        $number = substr($number, 0, 3);

        return "{$number}K+";
    } else if ($number > 999999 && $number <= 9999999) {
        $number = substr($number, 0, 1);

        return $number.'M+';
    } else if ($number > 9999999 && $number <= 99999999) {
        $number = substr($number, 0, 2);

        return "{$number}M+";
    } else if ($number > 99999999 && $number <= 999999999) {
        $number = substr($number, 0, 3);

        return "{$number}M+";
    }

    return $numb;
}

function get_brick_hill_asset($id, $type)
{
    $response = json_decode(file_get_contents("https://api.brick-hill.com/v1/assets/getPoly/1/{$id}"))[0];
    $assetId = str_replace('asset://', '', $response->$type);
    $url = "https://api.brick-hill.com/v1/assets/get/{$assetId}";

    do {
        $context = stream_context_create([
            'http' => [
                'follow_location' => false,
            ]
        ]);

        try {
            $result = file_get_contents("compress.zlib://{$url}", false, $context);
        } catch (Exception $err) {
            return;
        }

        $pattern = '/^Location:\s*(.*)$/i';
        $location_headers = preg_grep($pattern, $http_response_header);

        if (!empty($location_headers) && preg_match($pattern, array_values($location_headers)[0], $matches)) {
            $url = $matches[1];
            $repeat = true;
        } else {
            $repeat = false;
        }
    } while ($repeat);

    return $result;
}

function game_launch($string)
{
    $replaceArray = [
        0 => "SDOCPAP34394L",
        1 => "CNSA43278PDI4",
        2 => "FHB43REW4AS6N",
        3 => "ASD7VFGMHJDFG",
        4 => "NCIRF42423VU4",
        5 => "D940BXXAOER45",
        6 => "52V852J8KV38Y",
        7 => "ASD97DSF523VX",
        8 => "XSDAPFOR94NA3",
        9 => "STYU6GHJ4GDUU7",
        "-" => "ASDU4G234L3VHJ",
        "a" => "XA6W77ZKX4DX",
        "b" => "85JM67LHQK65",
        "c" => "Z5RU8XZS34M4",
        "d" => "S8KZEE7M6CZV",
        "e" => "YUHJ266VW7YK",
        "f" => "AJCDY8887SER",
        "g" => "UG9U2KCA762A",
        "h" => "FW6W64QWHYDN",
        "i" => "BW2GHBM5TRMB",
        "j" => "9ATDW583DNKH",
        "k" => "SE8YS2Q92WX8",
        "l" => "P6JVFRC3R2PU",
        "m" => "FH7R6P3NBD6S",
        "n" => "7MBNZB5S9Z3E",
        "o" => "ZJQ8N22TCU6W",
        "p" => "DADG4K98N7VA",
        "q" => "PV2P3FNZFRA2",
        "r" => "ZADKTJGV69UE",
        "s" => "XHEAG2KDBMZE",
        "t" => "NDGMDV6F5DSV",
        "u" => "3YZ5CNDYFXRJ",
        "v" => "G4G5C5WF3ETU",
        "w" => "4NQ74VWZAFK7",
        "x" => "JLGRJ9T5SGUV",
        "y" => "LESGS87NF685",
        "z" => "4DPSNWDQ4XMQ",
        "A" => "GK5MHDHSZ7C7",
        "B" => "RRV2E48JDBYB",
        "C" => "TPPT6L8RVT29",
        "D" => "B77RBGLKUJ93",
        "E" => "FSB7LP6956CL",
        "F" => "87H9TE7MFC2R",
        "G" => "FAJV5F8UA7ND",
        "H" => "99Y9BL6R3AQD",
        "I" => "TNGL8HGAXAWQ",
        "J" => "H7SL7CL98D3H",
        "K" => "NM6H6CP3WTU5",
        "L" => "5T2XL5GPT9B4",
        "M" => "RBW6MHQBRP6L",
        "N" => "R3N9TCNVEQ6Q",
        "O" => "5GAQK8GS7WZP",
        "P" => "V6R89BMBHMG4",
        "Q" => "DQ5WHTQ52NKH",
        "R" => "6Y4KLGMG9PEC",
        "S" => "DKUC4Q8GHVKQ",
        "T" => "X464XS96JPRU",
        "U" => "JWQXHK2UF35M",
        "V" => "BFTHE2A6BSPQ",
        "W" => "YWD3AWTAYHVM",
        "X" => "4ASA5KRER9VJ",
        "Y" => "F5LCBCQL3Z85",
        "Z" => "MTZA99FRDE8T"
    ];

    return strtr($string, $replaceArray);
}

function generate_filename()
{
    $length = 40;
    return substr(str_shuffle(str_repeat($x = '123456789', ceil($length / strlen($x)))), 1, $length);
}
