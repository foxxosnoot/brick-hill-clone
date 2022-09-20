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

namespace App\Http\Controllers\Web\Account;

use App\Models\ItemPurchase;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        switch ($request->category) {
            case '':
            case 'exchange':
                $category = 'exchange';
                break;
            case 'purchases':
                $category = 'purchases';
                $transactions = ItemPurchase::where('buyer_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
                break;
            case 'sales':
                $category = 'sales';
                $transactions = ItemPurchase::where('seller_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
                break;
            default:
                abort(404);
        }

        return view('web.account.currency')->with([
            'category' => $category,
            'transactions' => $transactions ?? null
        ]);
    }

    public function convert(Request $request)
    {
        $user = Auth::user();

        switch ($request->type) {
            case 'to-bits':
                $this->validate(request(), [
                    'amount' => ['required', 'numeric', 'min:1']
                ]);

                $bits = $user->currency_bits + 10 * $request->amount;
                $bucks = $user->currency_bucks - $request->amount;

                if ($bits >= 0 && $bucks >= 0) {
                    $user->currency_bits = $bits;
                    $user->currency_bucks = $bucks;
                    $user->save();

                    return back()->with('success_message', 'Currencies have been converted successfully!');
                }

                return back()->withErrors(['Insufficient bucks.']);
            case 'to-bucks':
                $this->validate(request(), [
                    'amount' => ['required', 'numeric', 'min:10']
                ], [
                    'amount.min' => 'Amount must be divisible by 10!'
                ]);

                if ($request->amount / 10 != (int) ($request->amount / 10))
                    return back()->withErrors(['Amount must be divisible by 10!']);

                $bits = $user->currency_bits - $request->amount;
                $bucks = $user->currency_bucks + (int) ($request->amount / 10);

                if ($bits >= 0 && $bucks >= 0) {
                    $user->currency_bits = $bits;
                    $user->currency_bucks = $bucks;
                    $user->save();

                    return back()->with('success_message', 'Currencies have been converted successfully!');
                }

                return back()->withErrors(['Insufficient bits.']);
            default:
                abort(404);
        }
    }
}
