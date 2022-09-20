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

Route::view('/docs', 'api.docs');

Route::group(['namespace' => 'Legacy', 'prefix' => 'legacy'], function() {
    Route::group(['prefix' => 'client'], function() {
        Route::get('/login', 'ClientController@login');
        Route::get('/games', 'ClientController@games');
        Route::get('/assetTexture', 'ClientController@assetTexture');
        Route::get('/assetD3D', 'ClientController@assetD3D');
        Route::get('/getAvatar', 'ClientController@getAvatar');
    });

    Route::group(['prefix' => 'games'], function() {
        Route::post('/publish', 'GamesController@publish');
        Route::post('/upload', 'GamesController@upload');
    });
});

Route::group(['namespace' => 'V1'], function() {
    Route::post('shop/render/preview', 'ShopController@renderPreview')->middleware('auth');

    Route::group(['prefix' => 'v1'], function() {
        Route::get('/comments/1/{id}', 'ShopController@comments');

        Route::group(['prefix' => 'auth'], function() {
            Route::get('/generateToken', 'AuthController@generateToken')->middleware('auth');
            Route::get('/verifyToken', 'AuthController@verifyToken');
            Route::get('/verifyTokenClient', 'AuthController@verifyTokenClient');
        });

        Route::group(['prefix' => 'sets'], function() {
            Route::get('/{setId}', 'GamesController@info');
        });

        Route::group(['prefix' => 'games'], function() {
            Route::get('/retrieveAsset', 'GamesController@retrieveAsset');
            Route::get('/retrieveAvatar', 'GamesController@retrieveAvatar');
        });

        Route::group(['prefix' => 'shop'], function() {
            Route::get('/list', 'ShopController@list');
            Route::get('/{itemId}', 'ShopController@info');
            Route::get('/{itemId}/owners', 'ShopController@owners');
            Route::get('/{itemId}/recommended', 'ShopController@recommended');
        });

        Route::group(['prefix' => 'user'], function() {
            Route::get('/profile', 'UserController@profile');
            Route::get('/id', 'UserController@id');
            Route::get('/{userId}/sets', 'UserController@sets');
            Route::get('/{userId}/crate', 'UserController@crate');
            Route::get('/{userId}/owns/{itemId}', 'UserController@owns');
            Route::get('/trades/{userId}/{category}', 'UserController@trades')->middleware('auth');
            Route::get('/trades/{tradeId}', 'UserController@trade')->middleware('auth');
        });

        Route::group(['prefix' => 'clans'], function() {
            Route::get('/members/{clanId}/{rank}', 'ClansController@members');
        });
    });
});
