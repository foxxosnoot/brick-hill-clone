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

// I didn't want to actually setup an asset compiler lol
// ghetto, but it works /shrug
return [
    'app' => 'App.js',
    'bundle' => 'Bundle.js',

    'account/inbox/message' => 'SendMessage.js',
    'account/trades/index' => 'Trades.js',
    'account/trades/send' => 'SendTrade.js',
    'account/billing' => 'Billing.js',
    'account/character' => 'CustomizeCharacter.js',
    'account/promocodes' => 'RedeemCodes.js',
    'account/settings' => 'Settings.js',

    'clans/view' => 'ViewClan.js',

    'games/download' => 'DownloadClient.js',
    'games/edit' => 'EditGame.js',
    'games/view' => 'Game.js',

    'shop/create' => 'CreateItem.js',
    'shop/edit' => 'EditItem.js',
    'shop/index' => 'Shop.js',
    'shop/item' => 'Item.js',

    'users/profile' => 'Profile.js'
];
