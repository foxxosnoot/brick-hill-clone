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

Route::group(['as' => 'home.'], function() {
    Route::get('/', 'HomeController@index')->name('index')->middleware('guest');
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard')->middleware('auth');
    Route::post('/status', 'HomeController@status')->name('status')->middleware('auth');
});

Route::group(['as' => 'maintenance.', 'prefix' => 'offline'], function() {
    Route::get('/', 'MaintenanceController@index')->name('index');
    Route::post('/login', 'MaintenanceController@authenticate')->name('authenticate');
    Route::get('/exit', 'MaintenanceController@exit')->name('exit');
});

Route::group(['as' => 'auth.', 'namespace' => 'Auth'], function() {
    Route::post('/logout', 'LoginController@logout')->name('logout');

    Route::group(['middleware' => 'guest'], function() {
        Route::group(['as' => 'login.', 'prefix' => 'login'], function() {
            Route::get('/', 'LoginController@index')->name('index');
            Route::post('/authenticate', 'LoginController@authenticate')->name('authenticate');
        });

        Route::group(['as' => 'register.', 'prefix' => 'register'], function() {
            Route::get('/', 'RegisterController@index')->name('index');
            Route::get('/{referralCode}', 'RegisterController@index')->name('referred');
            Route::post('/authenticate', 'RegisterController@authenticate')->name('authenticate');
        });

        Route::group(['as' => 'forgot_password.', 'prefix' => 'password/forgot'], function() {
            Route::get('/', 'ForgotPasswordController@index')->name('index');
            Route::post('/send', 'ForgotPasswordController@send')->name('send');
            Route::get('/{token}', 'ForgotPasswordController@change')->name('change');
            Route::post('/finish', 'ForgotPasswordController@finish')->name('finish');
        });
    });
});

Route::group(['as' => 'account.', 'namespace' => 'Account'], function() {
    Route::group(['middleware' => 'auth'], function() {
        Route::group(['as' => 'billing.'], function() {
            Route::get('/membership', 'BillingController@index')->name('index');
        });

        Route::group(['as' => 'promocodes.', 'prefix' => 'promocodes'], function() {
            Route::get('/', 'PromocodesController@index')->name('index');
            Route::post('/redeem', 'PromocodesController@redeem')->name('redeem');
        });

        Route::group(['as' => 'banned.', 'prefix' => 'banned'], function() {
            Route::get('/', 'BannedController@index')->name('index');
            Route::post('/reactivate', 'BannedController@reactivate')->name('reactivate');
        });

        Route::group(['as' => 'character.', 'prefix' => 'customize'], function() {
            Route::get('/', 'CharacterController@index')->name('index');
            Route::post('/regenerate', 'CharacterController@regenerate')->name('regenerate');
            Route::get('/inventory', 'CharacterController@inventory')->name('inventory');
            Route::get('/wearing', 'CharacterController@wearing')->name('wearing');
            Route::post('/update', 'CharacterController@update')->name('update');
        });

        Route::group(['as' => 'verify.', 'prefix' => 'email'], function() {
            Route::get('/cancelEmail', 'VerifyController@cancel')->name('cancel');
            Route::get('/sendEmail', 'VerifyController@send')->name('send');
            Route::get('/verify', 'VerifyController@confirm')->name('confirm');
        });

        Route::group(['as' => 'settings.', 'prefix' => 'settings'], function() {
            Route::get('/', 'SettingsController@index')->name('index');
            Route::post('/update', 'SettingsController@update')->name('update');
        });

        Route::group(['as' => 'friends.', 'prefix' => 'friends'], function() {
            Route::get('/', 'FriendsController@index')->name('index');
            Route::post('/update', 'FriendsController@update')->name('update');
        });

        Route::group(['as' => 'inbox.'], function() {
            Route::group(['prefix' => 'message'], function() {
                Route::get('/{id}', 'InboxController@message')->name('message');
                Route::get('/{id}/send', 'InboxController@new')->name('new');
            });

            Route::group(['prefix' => 'messages'], function() {
                Route::get('/', 'InboxController@index');
                Route::get('/{category}', 'InboxController@index')->name('index');
                Route::post('/create', 'InboxController@create')->name('create');
            });
        });

        Route::group(['as' => 'currency.', 'prefix' => 'currency'], function() {
            Route::get('/', 'CurrencyController@index');
            Route::get('/{category}', 'CurrencyController@index')->name('index');
            Route::post('/convert', 'CurrencyController@convert')->name('convert');
        });

        Route::group(['as' => 'trades.'], function() {
            Route::group(['prefix' => 'trade'], function() {
                Route::get('/create/{id}', 'TradesController@send')->name('send');
            });

            Route::group(['prefix' => 'trades'], function() {
                Route::get('/', 'TradesController@index')->name('index');
                Route::post('/process', 'TradesController@process')->name('process');
            });
        });
    });
});

Route::group(['as' => 'report.', 'prefix' => 'report', 'middleware' => 'auth'], function() {
    Route::get('/{type}/{id}', 'ReportController@index')->name('index');
    Route::post('/submit', 'ReportController@submit')->name('submit');
});

Route::group(['as' => 'games.'], function() {
    Route::get('/client', 'GamesController@download')->name('download');
    Route::get('/sets', 'GamesController@creations')->name('creations')->middleware('auth');

    Route::group(['prefix' => 'play'], function() {
        Route::get('/', 'GamesController@index')->name('index');
        Route::get('/create', 'GamesController@create')->name('create')->middleware('auth');
        Route::get('/{id}', 'GamesController@view')->name('view');
        Route::get('/{id}/edit', 'GamesController@edit')->name('edit')->middleware('auth');
        Route::post('/update', 'GamesController@update')->name('update')->middleware('auth');
    });
});

Route::group(['as' => 'shop.'], function() {
    Route::post('/comments/create', 'ShopController@comment')->name('comment')->middleware('auth');
    Route::post('/favorites/create', 'ShopController@favorite')->name('favorite')->middleware('auth');

    Route::group(['prefix' => 'shop'], function() {
        Route::get('/', 'ShopController@index')->name('index');
        Route::get('/create', 'ShopController@create')->name('create')->middleware('auth');
        Route::post('/upload', 'ShopController@upload')->name('upload')->middleware('auth');
        Route::get('/{id}', 'ShopController@item')->name('item');
        Route::get('/{id}/edit', 'ShopController@edit')->name('edit')->middleware('auth');
        Route::post('/update', 'ShopController@update')->name('update')->middleware('auth');
        Route::post('/purchase', 'ShopController@purchase')->name('purchase')->middleware('auth');
        Route::post('/resell', 'ShopController@resell')->name('resell')->middleware('auth');
        Route::post('/take-off-sale', 'ShopController@takeOffSale')->name('take_off_sale')->middleware('auth');
    });
});

Route::group(['as' => 'forum.', 'prefix' => 'forum'], function() {
    Route::get('/', 'ForumController@index')->name('index');
    Route::get('/search', function() { return 'mine diamonds'; })->name('search');
    Route::get('/{id}', 'ForumController@topic')->name('topic');
    Route::get('/thread/{id}', 'ForumController@thread')->name('thread');
    Route::get('/new/{type}/{id}', 'ForumController@new')->name('new')->middleware('auth');
    Route::post('/create', 'ForumController@create')->name('create')->middleware('auth');
    Route::get('/edit/{type}/{id}', 'ForumController@edit')->name('edit')->middleware('require_staff');
    Route::post('/edit', 'ForumController@update')->name('update')->middleware('require_staff');
    Route::get('/moderate/{type}/{action}/{id}', 'ForumController@moderate')->name('moderate')->middleware('require_staff');
});

Route::group(['as' => 'users.'], function() {
    Route::group(['prefix' => 'search'], function() {
        Route::get('/', 'UsersController@index');
        Route::get('/{category}', 'UsersController@index')->name('index');
    });

    Route::group(['prefix' => 'user/{id}'], function() {
        Route::get('/', 'UsersController@profile')->name('profile');
        Route::get('/friends', 'UsersController@friends')->name('friends');
        Route::get('/clans', 'UsersController@clans')->name('clans');
    });
});

Route::group(['as' => 'clans.', 'prefix' => 'clans'], function() {
    Route::get('/', 'ClansController@index')->name('index');
    Route::get('/create', 'ClansController@create')->name('create')->middleware('auth');
    Route::post('/purchase', 'ClansController@purchase')->name('purchase')->middleware('auth');
    Route::get('/{id}', 'ClansController@view')->name('view');
    Route::get('/{id}/edit', 'ClansController@edit')->name('edit')->middleware('auth');
    Route::post('/edit', 'ClansController@update')->name('update')->middleware('auth');
    Route::post('/membership', 'ClansController@membership')->name('membership')->middleware('auth');
    Route::post('/primary', 'ClansController@primary')->name('primary')->middleware('auth');
});

Route::group(['as' => 'awards.', 'prefix' => 'awards'], function() {
    Route::get('/', 'AwardsController@index')->name('index');
});

Route::group(['as' => 'info.'], function() {
    Route::get('/terms', 'InfoController@terms')->name('terms');
    Route::get('/privacy', 'InfoController@privacy')->name('privacy');
    Route::get('/staff', 'InfoController@staff')->name('staff');
});
