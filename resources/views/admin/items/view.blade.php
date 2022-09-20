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
    'title' => "Item: {$item->name}"
])

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Thumbnail</div>
                <div class="card-body text-center">
                    <img src="{{ $item->thumbnail() }}">
                    <a href="{{ route('shop.item', $item->id) }}" class="button blue small w-100 mt-3" target="_blank"><i class="fas fa-link"></i> View in Shop</a>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6"><strong>Creation Date</strong></div>
                        <div class="col-6 text-right">{{ $item->created_at->format('M d, Y') }}</div>
                        <div class="col-6"><strong>Last Updated</strong></div>
                        <div class="col-6 text-right">{{ $item->updated_at->format('M d, Y') }}</div>
                        <div class="col-6"><strong>Owners</strong></div>
                        <div class="col-6 text-right">{{ number_format($item->owners()->count()) }}</div>
                        <div class="col-4"><strong>Bits</strong></div>
                        <div class="col-8 text-right bits-text">
                            <span class="bits-icon"></span>
                            <span>{{ number_format($item->price_bits) }}</span>
                        </div>
                        <div class="col-4"><strong>Bucks</strong></div>
                        <div class="col-8 text-right bucks-text">
                            <span class="bucks-icon"></span>
                            <span>{{ number_format($item->price_bucks) }}</span>
                        </div>
                        <div class="col-6"><strong>Is Off Sale</strong></div>
                        <div class="col-6 text-right">{{ (!$item->onsale) ? 'Yes' : 'No' }}</div>
                        <div class="col-6"><strong>Is Public</strong></div>
                        <div class="col-6 text-right">{{ ($item->public_view) ? 'Yes' : 'No' }}</div>
                        <div class="col-6"><strong>Status</strong></div>
                        <div class="col-6 text-right">{{ ucfirst($item->status) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <form action="{{ route('admin.items.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <div class="card">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        @if (staffUser()->staff('can_review_pending_assets'))
                            <button class="{{ ($item->status != 'approved') ? 'green' : 'red' }} w-100 mb-2" name="action" value="status">
                                @if ($item->status != 'approved')
                                    <i class="fas fa-check mr-1"></i>
                                    <span>Approve</span>
                                @else
                                    <i class="fas fa-times mr-1"></i>
                                    <span>Deny</span>
                                @endif
                            </button>
                        @endif

                        @if (staffUser()->staff('can_scrub_item_info'))
                            <button class="red w-100 mb-2" name="action" value="scrub">
                                <i class="fas fa-trash mr-1"></i>
                                <span>Scrub</span>
                            </button>
                        @endif

                        @if ((staffUser()->staff('can_edit_item_info') || $item->creator->id == staffUser()->id) && !in_array($item->type, ['tshirt', 'shirt', 'pants']))
                            <a href="{{ route('admin.edit_item.index', $item->id) }}" class="button blue w-100 mb-2">
                                <i class="fas fa-edit mr-1"></i>
                                <span>Edit</span>
                            </a>
                        @endif

                        @if (staffUser()->staff('can_render_thumbnails'))
                            <button class="orange w-100 mb-2" name="action" value="regen">
                                <i class="fas fa-sync mr-1"></i>
                                <span>Regen Thumbnail</span>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
