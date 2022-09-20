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
    'title' => $title
])

@section('header')
    <a href="{{ config('site.storage_url') }}/default/{{ ($type != 'face') ? 'avatar.blend' : 'face.png' }}" class="button green small" download>
        <i class="fas fa-share"></i>
        <span>Download {{ ($type != 'face') ? 'Avatar' : 'Face' }}</span>
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">{{ $title }}</div>
        <div class="card-body">
            <form action="{{ route('admin.create_item.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                    <div class="col-md-6">
                        <label for="name">Name</label>
                        <input class="form-control mb-2" type="text" name="name" placeholder="Item Name">
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col">
                                <label for="price_bucks">
                                    <span class="bucks-icon"></span>
                                    <span>Bucks</span>
                                </label>
                                <input class="form-control mb-2" type="number" name="price_bucks" placeholder="Bucks" min="0" max="1000000">
                            </div>
                            <div class="col">
                                <label for="price_bits">
                                    <span class="bits-icon"></span>
                                    <span>Bits</span>
                                </label>
                                <input class="form-control mb-2" type="number" name="price_bits" placeholder="Bits" min="0" max="1000000">
                            </div>
                        </div>
                    </div>
                </div>
                <label for="description">Description</label>
                <textarea class="form-control mb-2" name="description" placeholder="Item Description" rows="5"></textarea>
                <label for="stock">Special Stock</label>
                <input class="form-control mb-2" type="number" name="stock" placeholder="Special Stock" min="0" max="500">
                <label for="brick_hill_item_id ">Brick Hill Item ID (leave this empty if it's custom)</label>
                <input class="form-control mb-2" type="number" name="brick_hill_item_id" placeholder="Brick Hill Item ID" min="1">
                <label for="onsale_for">Onsale For</label>
                <select class="form-control mb-2" name="onsale_for">
                    <option value="forever" selected>Forever</option>
                    <option value="1_hour">1 Hour</option>
                    <option value="12_hours">12 Hours</option>
                    <option value="1_day">1 Day</option>
                    <option value="3_days">3 Days</option>
                    <option value="7_days">7 Days</option>
                    <option value="14_days">14 Days</option>
                    <option value="21_days">21 Days</option>
                    <option value="1_month">1 Month</option>
                </select>
                <div class="row">
                    @if ($type != 'head')
                        <div class="col-12 mb-2">
                            <label for="image">Image</label><br>
                            <input name="image" type="file" accept=".png,.jpg,.jpeg">
                        </div>

                        @if ($type != 'face')
                            <div class="col-12 mb-2">
                                <label for="material">Material (optional)</label><br>
                                <input name="material" type="file" accept=".mtl">
                            </div>
                        @endif
                    @endif

                    @if ($type != 'face')
                        <div class="col-12 mb-2">
                            <label for="model">Model</label><br>
                            <input name="model" type="file" accept=".obj">
                        </div>
                    @endif
                </div>
                <label>Options</label>
                <div class="row mb-1">
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="onsale">
                            <label class="form-check-label" for="onsale">For Sale</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="special">
                            <label class="form-check-label" for="special">Special</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="public">
                            <label class="form-check-label" for="public_view">Public</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="official">
                            <label class="form-check-label" for="official">Official (upload to system)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="auto">
                            <label class="form-check-label" for="auto">Auto set name & description (if Brick Hill Item ID)</label>
                        </div>
                    </div>
                </div>
                <button class="green w-100" type="submit">Create</button>
            </form>
        </div>
    </div>
@endsection
