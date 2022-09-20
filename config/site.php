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

$aU = env('ASSET_URL');

return [
    'name' => env('APP_NAME', 'Brick Hill Clone'),
    'logo' => $aU . '/images/logos/bh_small.png',
    'icon' => $aU . '/images/logos/bh_icon.png',
    'theme_color' => '#00A9FE',

    'route_domains' => [
        'main_site' => 'brick-hill.local',
        'admin_site' => 'admin.brick-hill.local'
    ],

    'storage_url' => env('STORAGE_URL'),
    'discord_url' => '',
    'emails' => [
        'support' => 'help@brick-hill.local',
        'moderation' => 'appeals@brick-hill.local'
    ],

    'system_user_id' => 1,
    'news_topic_id' => 1,
    'rules_thread_id' => null,
    'saint_item_id' => null,

    'username_change_price' => 250,
    'clan_creation_price' => 25,

    'flood_time' => 5,
    'forum_age_requirement' => 0,
    'message_age_requirement' => 0,

    'renderer' => [
        'url' => env('RENDER_URL'),
        'key' => env('RENDER_KEY'),
        'default_filename' => '7Nr5llNgVgiHUsBjw7mc'
    ],

    'admin_panel_code' => '',
    'maintenance_passwords' => [
        'newmaintenancekeygetfucked'
    ]
];
