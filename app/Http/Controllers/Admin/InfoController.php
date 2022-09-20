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
use App\Models\User;
use App\Models\UserBan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function index()
    {
        $serverData = $this->data('server');
        $siteData = $this->data('site');

        return view('admin.info')->with([
            'siteData' => $siteData,
            'serverData' => $serverData
        ]);
    }

    public function data($type)
    {
        switch ($type) {
            case 'site':
                $totalUsers = User::count();
                $joinedToday = User::where('created_at', '>=', Carbon::now()->subDays(1))->count();
                $onlineUsers = User::where('updated_at', '>=', Carbon::now()->subMinutes(3))->count();
                $bannedUsers = UserBan::where('active', '=', true)->count();

                return [
                    'Total Users' => $totalUsers,
                    'Joined Today' => $joinedToday,
                    'Online Users' => $onlineUsers,
                    'Banned Users' => $bannedUsers
                ];
            case 'server':
                if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                    $cpuUsage = sys_getloadavg()[0] . '%';

                    $execFree = explode("\n", trim(shell_exec('free')));
                    $getMem = preg_split("/[\s]+/", $execFree[1]);
                    $ramUsage = round($getMem[2] / $getMem[1] * 100, 0) . '%';

                    $uptime = preg_split("/[\s]+/", trim(shell_exec('uptime')))[2] . ' Days';
                }

                return [
                    'CPU Usage' => $cpuUsage ?? '???',
                    'RAM Usage' => $ramUsage ?? '???',
                    'PHP Version' => phpversion(),
                    'Uptime' => $uptime ?? '???'
                ];
        }
    }
}
