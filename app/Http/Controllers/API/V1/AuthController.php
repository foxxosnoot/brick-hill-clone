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

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function generateToken(Request $request)
    {
        $game = Game::where('id', '=', $request->set);

        if (!$game->exists())
            return response()->json([
                'error' => [
                    'message' => 'Record not found',
                    'prettyMessage' => 'Sorry, something went wrong'
                ]
            ]);

        $game = $game->first();

        return response()->json([
            'token' => (string) Auth::user()->id
        ]);
    }

    public function verifyToken(Request $request)
    {
        if (!$request->token)
            return response()->json([
                'error' => [
                    'message' => 'Missing parameters',
                    'prettyMessage' => 'Missing parameters'
                ]
            ]);

        $user = User::where('id', '=', $request->token);

        if (!$user->exists())
            return response()->json([
                'error' => 'Invalid token'
            ]);

        $user = $user->first();

        return response()->json([
            'validator' => null,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'is_admin' => $user->isStaff(),
                'membership' => [
                    'membership' => 1
                ]
            ]
        ]);
    }
}
