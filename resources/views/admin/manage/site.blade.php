<!--
MIT License

Copyright (c) 2021-2022 FoxxoSnoot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
-->

@extends('layouts.admin', [
    'title' => 'Site Settings'
])

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css">
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js"></script>
    <script>
        $(() => {
            $('input[name="alert_background_color"]').spectrum({
                color: '{{ site_setting('alert_background_color') }}',
                allowEmpty: false
            });

            $('input[name="alert_text_color"]').spectrum({
                color: '{{ site_setting('alert_text_color') }}',
                allowEmpty: false
            });

            $('input[name="alert_background_color"]').change(function() {
                $(this).val($(this).spectrum('get').toHexString());
            });

            $('input[name="alert_text_color"]').change(function() {
                $(this).val($(this).spectrum('get').toHexString());
            });
        });
    </script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">Site Settings</div>
        <div class="card-body">
            <form action="{{ route('admin.manage.site.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <strong>Features</strong>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="maintenance_enabled" @if (site_setting('maintenance_enabled')) checked @endif>
                            <label class="form-check-label" for="maintenance_enabled">Maintenance Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="alert_enabled" @if (site_setting('alert_enabled')) checked @endif>
                            <label class="form-check-label" for="alert_enabled">Alert Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="item_purchases_enabled" @if (site_setting('item_purchases_enabled')) checked @endif>
                            <label class="form-check-label" for="item_purchases_enabled">Item Purchases Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="item_creation_enabled" @if (site_setting('item_creation_enabled')) checked @endif>
                            <label class="form-check-label" for="item_creation_enabled">Item Creation Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="forum_enabled" @if (site_setting('forum_enabled')) checked @endif>
                            <label class="form-check-label" for="forum_enabled">Forum Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="avatar_editor_enabled" @if (site_setting('avatar_editor_enabled')) checked @endif>
                            <label class="form-check-label" for="avatar_editor_enabled">Avatar Editor Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="trading_enabled" @if (site_setting('trading_enabled')) checked @endif>
                            <label class="form-check-label" for="trading_enabled">Trading Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="clans_enabled" @if (site_setting('clans_enabled')) checked @endif>
                            <label class="form-check-label" for="clans_enabled">Clans Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="settings_enabled" @if (site_setting('settings_enabled')) checked @endif>
                            <label class="form-check-label" for="settings_enabled">Settings Enabled</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="registration_enabled" @if (site_setting('registration_enabled')) checked @endif>
                            <label class="form-check-label" for="registration_enabled">Registration Enabled</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <strong>Alert Message</strong><br>
                        <textarea class="form-control mb-2" name="alert_message" placeholder="Site alert here..." rows="5">{{ site_setting('alert_message') }}</textarea>
                        <strong>Alert Colors</strong>
                        <div class="row">
                            <div class="col-6">
                                <label for="alert_background_color">Background</label><br>
                                <input class="form-control mb-2" type="text" name="alert_background_color" placeholder="Alert Background Color" value="{{ site_setting('alert_background_color') }}" required>
                            </div>
                            <div class="col-6">
                                <label for="alert_text_color">Text</label><br>
                                <input class="form-control mb-2" type="text" name="alert_text_color" placeholder="Alert Text Color" value="{{ site_setting('alert_text_color') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <strong>Maintenance Passwords</strong>
                        @forelse ($maintenancePasswords as $maintenancePassword)
                            <div><small>{{ $maintenancePassword }}</small></div>
                        @empty
                            <p>No passwords found.</p>
                        @endforelse
                    </div>
                </div>
                <button class="green w-100 mt-1" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection
