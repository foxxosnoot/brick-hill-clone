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

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AwardsController extends Controller
{
    public function index()
    {
        $awards = ['Membership' => [], 'Community' => [], 'Excellence' => []];
        $categories = [
            [
                'name' => 'Membership',
                'color' => 'red',
                'award_ids' => [6, 7, 8, 4]
            ],
            [
                'name' => 'Community',
                'color' => 'green',
                'award_ids' => [1, 2, 3]
            ],
            [
                'name' => 'Excellence',
                'color' => 'orange',
                'award_ids' => [5]
            ]
        ];

        foreach ($categories as $category)
            foreach ($category['award_ids'] as $id)
                $awards[$category['name']][] = config('awards')[$id];

        return view('web.awards.index')->with([
            'awards' => $awards,
            'categories' => $categories
        ]);
    }
}
