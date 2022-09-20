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

namespace App\Http\Controllers\Admin\Manage;

use App\Models\User;
use App\Models\StaffUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public const STAFF_PERMISSIONS = [
        'Item' => [
            'Can View Info'    => 'can_view_item_info',
            'Can Edit Info'    => 'can_edit_item_info',
            'Can Scrub Info'   => 'can_scrub_item_info',
            'Can Create Hats'  => 'can_create_hat_items',
            'Can Create Faces' => 'can_create_face_items',
            'Can Create Tools' => 'can_create_tool_items',
            'Can Create Heads' => 'can_create_head_items'
        ],

        'User' => [
            'Can View Info'       => 'can_view_user_info',
            'Can View Emails'     => 'can_view_user_emails',
            'Can Edit Info'       => 'can_edit_user_info',
            'Can Scrub Info'      => 'can_scrub_user_info',
            'Can Reset Passwords' => 'can_reset_user_passwords',
            'Can Give Items'      => 'can_give_items',
            'Can Take Items'      => 'can_take_items',
            'Can Give Currency'   => 'can_give_currency',
            'Can Take Currency'   => 'can_take_currency',
            'Can Ban Users'       => 'can_ban_users',
            'Can Unban Users'     => 'can_unban_users',
            'Can Ban IPs'         => 'can_ip_ban_users',
            'Can Unban IPs'       => 'can_ip_unban_users'
        ],

        'Review' => [
            'Can Review Pending Assets'  => 'can_review_pending_assets',
            'Can Review Pending Reports' => 'can_review_pending_reports'
        ],

        'Forum' => [
            'Can Edit Posts'   => 'can_edit_forum_posts',
            'Can Delete Posts' => 'can_delete_forum_posts',
            'Can Pin Posts'    => 'can_pin_forum_posts',
            'Can Lock Posts'   => 'can_lock_forum_posts'
        ],

        'Management' => [
            'Can Manage Forum Categories' => 'can_manage_forum_categories',
            'Can Manage Forum Topics'     => 'can_manage_forum_topics',
            'Can Manage Staff'            => 'can_manage_staff',
            'Can Manage Site'             => 'can_manage_site'
        ],

        'Etc' => [
            'Can Render Thumbnails' => 'can_render_thumbnails'
        ]
    ];

    public function __construct()
    {
        $this->middleware(function($request, $next) {
            if (!staffUser()->staff('can_manage_staff')) abort(404);

            return $next($request);
        });
    }

    public function index()
    {
        $users = StaffUser::orderBy('created_at', 'ASC')->paginate(25);

        return view('admin.manage.staff.index')->with([
            'users' => $users
        ]);
    }

    public function new()
    {
        $permissions = $this::STAFF_PERMISSIONS;

        return view('admin.manage.staff.new')->with([
            'permissions' => $permissions
        ]);
    }

    public function create(Request $request)
    {
        $user = User::where('username', '=', $request->username);
        $permissions = $request->except('_token', 'username');

        if (!$user->exists())
            return back()->withErrors(['This user does not exist.']);

        $user = $user->first();
        $exists = StaffUser::where('user_id', '=', $user->id)->exists();

        if ($exists)
            return back()->withErrors(['This user is already a staff member.']);

        if (empty($permissions))
            return back()->withErrors(['The user needs to have at least one permission set.']);

        try {
            $password = Str::random(25);

            $staffUser = new StaffUser;
            $staffUser->user_id = $user->id;

            foreach ($permissions as $name => $value)
                $staffUser->$name = true;

            $staffUser->password = bcrypt($password);
            $staffUser->save();

            $user->giveAward(3);
        } catch (Exception $e) {
            return back()->withErrors(['Something went wrong.']);
        }

        return redirect()->route('admin.manage.staff.index')->with('success_message', "User created. Password: <strong>{$password}</strong>.");
    }

    public function edit($id)
    {
        $user = StaffUser::where('user_id', '=', $id)->firstOrFail();
        $permissions = $this::STAFF_PERMISSIONS;

        return view('admin.manage.staff.edit')->with([
            'user' => $user,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request)
    {
        $user = StaffUser::where('user_id', '=', $request->id)->firstOrFail();
        $columns = array_keys($user->getAttributes());
        $permissions = $request->except('_token', 'id', 'delete');

        unset($columns[0]);
        unset($columns[1]);
        unset($columns[2]);
        unset($columns[3]);
        array_pop($columns);
        array_pop($columns);

        if ($request->has('delete')) {
            if ($user->user->id == staffUser()->id)
                return back()->withErrors(['You can not delete your own staff account.']);

            $user->delete();
            $user->user->removeAward(3);

            return redirect()->route('admin.manage.staff.index')->with('success_message', "{$user->user->username}'s staff account has been deleted.");
        }

        if ($request->has('password')) {
            $password = Str::random(25);

            $user->password = bcrypt($password);
            $user->save();

            if ($user->user->id == staffUser()->id) {
                $session = json_decode(session('admin_user'));

                session()->put('admin_user', json_encode([
                    'id' => $session->id,
                    'password' => bcrypt($password),
                    'updated_at' => time()
                ]));

                session()->save();
            }

            return redirect()->route('admin.manage.staff.index')->with('success_message', "{$user->user->username}'s password has been changed to <strong>{$password}</strong>.");
        }

        if (empty($permissions))
            return back()->withErrors(['The user needs to have at least one permission set.']);

        try {
            foreach ($columns as $column) {
                if (!in_array($column, ['created_at', 'updated_at']))
                    $user->$column = false;
            }

            foreach ($permissions as $name => $value)
                $user->$name = true;

            $user->save();
        } catch (Exception $e) {
            return back()->withErrors(['Something went wrong.']);
        }

        return redirect()->route('admin.manage.staff.index')->with('success_message', "{$user->user->username}'s permissions have been updated.");
    }
}
