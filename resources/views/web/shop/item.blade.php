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

@extends('layouts.default', [
    'title' => $item->name,
    'image' => $item->thumbnail()
])

@section('meta')
    <meta
        name="routes"
        data-index="{{ route('shop.index') }}"
        data-index-title="Shop - {{ config('site.name') }}"
        data-favorite="{{ route('shop.favorite') }}"
    >
    <meta
        name="item-info"
        data-id="{{ $item->id }}"
        @if ($item->isTimed())
            data-onsale-until="{{ $item->onsale_until->format('Y-m-d H:i') }}"
        @endif
    >
@endsection

@section('js')
    @if ($item->isTimed())
        <script src="{{ asset('js/vendor/jquery.countdown.min.js') }}"></script>
        <script src="{{ asset('js/vendor/moment.min.js') }}"></script>
        <script src="{{ asset('js/vendor/moment.timezone.min.js') }}"></script>
    @endif

    <script src="{{ js_file('shop/item') }}"></script>
@endsection

@section('content')
    @if (!$item->public_view)
        <div class="col-10-12 push-1-12">
            <div class="alert error">
                This item is not public.
            </div>
        </div>
    @endif
    @include('web.shop._header')
    <div class="col-10-12 push-1-12 item-holder" id="items">
        <div class="card mb4">
            <div class="content item-page">
                <div class="col-5-12" style="padding-right:10px;">
                    <div class="box relative shaded item-img  {{ (!$item->special_type && Auth::check() && Auth::user()->ownsItem($item->id)) ? 'owns' : '' }} {{ ($item->special_type) ? 'special' : '' }}">
                        <img src="{{ $item->thumbnail() }}" alt="{{ $item->name }}">
                        @if (Auth::check() && Auth::user()->ownsItem($item->id))
                            <div class="owned-check-tri {{ ($item->special_type) ? 'special' : 'owns' }}">
                                <i class="fas fa-check owned-check"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-7-12 item-data">
                    <div class="padding-bottom">
                        <div class="ellipsis">
                            <span class="medium-text bold ablack-text">{{ $item->name }}</span>
                            <span>{{ itemType($item->type) }}</span>
                        </div>
                        <div class="item-creator">By <a href="{{ route('users.profile', $item->creator->id) }}">{{ $item->creator->username }}</a></div>
                    </div>
                    <div class="item-purchase-buttons mt2 mb2">
                        @if ($item->isTimed())
                            <span id="timer" style="display:block;padding-bottom:5px;color:red;"></span>
                        @endif

                        @if ($item->special_type && $item->stock > 0)
                            <span style="display:block;padding-bottom:5px;color:red;">{{ $item->stock }} out of {{ $item->owners()->count() + $item->stock }} remaining</span>
                        @endif

                        @if (site_setting('item_purchases_enabled') && $item->status == 'approved' && $item->onsale())
                            @if ($item->price_bits == 0 && $item->price_bucks == 0)
                                <button class="purchase free flat no-cap" data-modal-open="purchase_free">
                                    <span>FREE</span>
                                </button>
                                <div class="modal" style="display:none;" data-modal="purchase_free">
                                    <div class="modal-content">
                                        <span class="close" data-modal-close="purchase_free">×</span>
                                        @guest
                                            <span>You are not logged in</span>
                                            <hr>
                                            <span>You must login to purchase an item</span>
                                            <div class="modal-buttons">
                                                <a href="{{ route('auth.login.index') }}" class="button bucks" style="margin-right:10px;">Login</a>
                                                <button class="cancel-button" type="button" data-modal-close="purchase_free">Cancel</button>
                                            </div>
                                        @else
                                            @if (Auth::user()->ownsItem($item->id))
                                                <span>You already own this item</span>
                                                <hr>
                                                <span>You can't purchase an item you already own</span>
                                                <div class="modal-buttons">
                                                    <button class="cancel-button" type="button" data-modal-close="purchase_free">Cancel</button>
                                                </div>
                                            @else
                                                <span>Buy Item</span>
                                                <hr>
                                                <span>Are you sure you want to buy <b>{{ $item->name }}</b> for free?</span>
                                                <div class="modal-buttons">
                                                    <form action="{{ route('shop.purchase') }}" style="display:inline;" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <input type="hidden" name="currency" value="free">
                                                        <button class="free" style="margin-right:10px;" type="submit">Buy Now</button>
                                                    </form>
                                                    <button type="button" class="cancel-button" data-modal-close="purchase_free">Cancel</button>
                                                </div>
                                            @endif
                                        @endguest
                                    </div>
                                </div>
                            @else
                                @if ($item->price_bucks > 0)
                                    <button class="purchase bucks flat no-cap" data-modal-open="purchase_bucks">
                                        <span class="bucks-icon img-white"></span>
                                        <span>{{ $item->price_bucks }} Bucks</span>
                                    </button>
                                    <div class="modal" style="display:none;" data-modal="purchase_bucks">
                                        <div class="modal-content">
                                            <span class="close" data-modal-close="purchase_bucks">×</span>
                                            @guest
                                                <span>You are not logged in</span>
                                                <hr>
                                                <span>You must login to purchase an item</span>
                                                <div class="modal-buttons">
                                                    <a href="{{ route('auth.login.index') }}" class="button bucks" style="margin-right:10px;">Login</a>
                                                    <button class="cancel-button" type="button" data-modal-close="purchase_bucks">Cancel</button>
                                                </div>
                                            @else
                                                @if (Auth::user()->ownsItem($item->id))
                                                    <span>You already own this item</span>
                                                    <hr>
                                                    <span>You can't purchase an item you already own</span>
                                                    <div class="modal-buttons">
                                                        <button class="cancel-button" type="button" data-modal-close="purchase_bucks">Cancel</button>
                                                    </div>
                                                @else
                                                    @if (Auth::user()->currency_bucks < $item->price_bucks)
                                                        <span>You do not have enough</span>
                                                        <hr>
                                                        <div style="text-align:center;padding-top:25px;padding-bottom:25px;">
                                                            <svg class="bucks-icon" style="transform:scale(5);padding:0px;margin-top:10px;"></svg>
                                                        </div>
                                                        <div class="modal-buttons">
                                                            <a href="{{ route('account.billing.index') }}" class="button bucks" style="margin-right:10px;">Buy More</a>
                                                            <button class="cancel-button" type="button" data-modal-close="purchase_bucks">Cancel</button>
                                                        </div>
                                                    @else
                                                        <span>Buy Item</span>
                                                        <hr>
                                                        <span>Are you sure you want to buy <b>{{ $item->name }}</b> for <span class="bucks-icon" style="margin-left:2px;"></span> {{ $item->price_bucks }}?</span>
                                                        <div class="modal-buttons">
                                                            <form action="{{ route('shop.purchase') }}" style="display:inline;" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                <input type="hidden" name="currency" value="bucks">
                                                                <button class="bucks" style="margin-right:10px;" type="submit">Buy Now</button>
                                                            </form>
                                                            <button type="button" class="cancel-button" data-modal-close="purchase_bucks">Cancel</button>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endguest
                                        </div>
                                    </div>
                                @endif

                                @if ($item->price_bits > 0)
                                    <button class="purchase bits flat no-cap" data-modal-open="purchase_bits">
                                        <span class="bits-icon img-white"></span>
                                        <span>{{ $item->price_bits }} Bits</span>
                                    </button>
                                    <div class="modal" style="display:none;" data-modal="purchase_bits">
                                        <div class="modal-content">
                                            <span class="close" data-modal-close="purchase_bits">×</span>
                                            @guest
                                                <span>You are not logged in</span>
                                                <hr>
                                                <span>You must login to purchase an item</span>
                                                <div class="modal-buttons">
                                                    <a href="{{ route('auth.login.index') }}" class="button bucks" style="margin-right:10px;">Login</a>
                                                    <button class="cancel-button" type="button" data-modal-close="purchase_bits">Cancel</button>
                                                </div>
                                            @else
                                                @if (Auth::user()->ownsItem($item->id))
                                                    <span>You already own this item</span>
                                                    <hr>
                                                    <span>You can't purchase an item you already own</span>
                                                    <div class="modal-buttons">
                                                        <button class="cancel-button" type="button" data-modal-close="purchase_bits">Cancel</button>
                                                    </div>
                                                @else
                                                    @if (Auth::user()->currency_bits < $item->price_bits)
                                                        <span>You do not have enough</span>
                                                        <hr>
                                                        <div style="text-align:center;padding-top:25px;padding-bottom:25px;">
                                                            <svg class="bits-icon" style="transform:scale(5);padding:0px;margin-top:10px;"></svg>
                                                        </div>
                                                        <div class="modal-buttons">
                                                            <a href="{{ route('account.billing.index') }}" class="button bucks" style="margin-right:10px;">Buy More</a>
                                                            <button class="cancel-button" type="button" data-modal-close="purchase_bits">Cancel</button>
                                                        </div>
                                                    @else
                                                        <span>Buy Item</span>
                                                        <hr>
                                                        <span>Are you sure you want to buy <b>{{ $item->name }}</b> for <span class="bits-icon" style="margin-left:2px;"></span> {{ $item->price_bits }}?</span>
                                                        <div class="modal-buttons">
                                                            <form action="{{ route('shop.purchase') }}" style="display:inline;" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                <input type="hidden" name="currency" value="bits">
                                                                <button class="bits" style="margin-right:10px;" type="submit">Buy Now</button>
                                                            </form>
                                                            <button type="button" class="cancel-button" data-modal-close="purchase_bits">Cancel</button>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endguest
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif

                        @auth
                            @if ($item->creator->id == Auth::user()->id && in_array($item->type, ['tshirt', 'shirt', 'pants']))
                                <a href="{{ route('shop.edit', $item->id) }}" class="button purchase green flat no-cap">EDIT</a>
                            @endif

                            @if (Auth::user()->isStaff())
                                @if (Auth::user()->staff('can_edit_item_info') && !in_array($item->type, ['tshirt', 'shirt', 'pants']))
                                    <a href="{{ route('admin.edit_item.index', $item->id) }}" class="button purchase red flat no-cap" target="_blank">EDIT</a>
                                @endif

                                @if (Auth::user()->staff('can_view_item_info'))
                                    <a href="{{ route('admin.items.view', $item->id) }}" class="button purchase red flat no-cap" target="_blank">INFO</a>
                                @endif
                            @endif
                        @endauth
                    </div>
                    <div class="agray-text bold">{!! nl2br(e($item->description)) !!}</div>
                    <div class="padding-30"></div>
                    <div class="small-text mt6 mb2">
                        <div class="item-stats">
                            <span class="agray-text">Created:</span>
                            <span class="darkest-gray-text" title="{{ $item->created_at->format('D, M d Y h:i A') }}">{{ $item->created_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="item-stats">
                            <span class="agray-text">Updated:</span>
                            <span class="darkest-gray-text" title="{{ $item->updated_at->format('D, M d Y h:i A') }}">{{ $item->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="item-stats">
                            <span class="agray-text">Sold:</span>
                            <span class="darkest-gray-text">{{ $item->owners()->count() }}</span>
                        </div>
                    </div>
                    <span class="hover-cursor favorite-text" id="favorite">
                        <i class="{{ (Auth::check() && Auth::user()->hasFavoritedItem($item->id)) ? 'fas' : 'far' }} fa-star" {!! (Auth::check()) ? 'id="favoriteIcon"' : '' !!}></i>
                        <span style="font-size: 0.9rem;" id="favoriteCount">{{ $item->favorites()->count() }}</span>
                    </span>
                    @if (Auth::check() && $item->creator->id != Auth::user()->id && !$item->creator->isStaff())
                        <a href="{{ route('report.index', ['item', $item->id]) }}" class="red-text" style="margin-left:15px;">
                            <i class="far fa-flag"></i>
                            <span style="font-size: 0.9rem;">Report</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @if ($item->special_type && $item->stock <= 0)
            <div class="card mb2">
                <div class="content item-page">
                    <span class="mb1">Price Chart</span>
                    <span class="push-right">Average: {{ $item->recentAveragePrice() }}</span>
                    <div class="text-center mt3 bits-text" style="font-size:50px;"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="text-center mt2 agray-text">Chart coming soon.</div>
                </div>
            </div>
            <div class="card" style="margin-bottom:20px;">
                <div class="content item-page">
                    <span class="item-name" style="font-size:1.1rem;">Private Sellers</span>
                    @if (Auth::check() && Auth::user()->ownsItem($item->id) && !empty(Auth::user()->resellableCopiesOfItem($item->id)))
                        <a class="button small blue" style="float:right;margin-top:-5px;" data-modal-open="sell">SELL</a>
                        <div class="modal" style="display:none;" data-modal="sell">
                            <div class="modal-content">
                                <form action="{{ route('shop.resell') }}" style="display:inline;" method="POST">
                                    @csrf
                                    <span class="close" data-modal-close="sell">×</span>
                                    <span>Sell {{ $item->name }}</span>
                                    <hr>
                                    <select class="select" style="width:100%;" name="id">
                                        @foreach (Auth::user()->resellableCopiesOfItem($item->id) as $copy)
                                            <option value="{{ $copy->id }}">#{{ $copy->serial }} of {{ $item->owners()->count() + $item->stock }}</option>
                                        @endforeach
                                    </select>
                                    <div style="width:100%;padding-top:15px;">
                                        <span style="color:#7f817f;">Price (min 1)</span>
                                        <input style="width:100%;box-sizing:border-box;" type="number" name="price" min="1" max="1000000">
                                    </div>
                                    <div class="modal-buttons">
                                        <button class="bucks" style="margin-right:10px;" type="submit">Sell Now</button>
                                        <button type="button" class="cancel-button" data-modal-close="sell">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    <hr>
                    <div class="sellers">
                        @forelse ($item->resellers() as $listing)
                            <div class="seller">
                                <div class="owner">
                                    <a href="{{ route('users.profile', $listing->seller->id) }}">
                                        <img src="{{ $listing->seller->thumbnail() }}">
                                        <br>
                                        <span class="dark-gray-text">{{ $listing->seller->username }}</span>
                                    </a>
                                </div>
                                <div class="price">
                                    <div class="push-right">
                                        <div class="serial">#{{ $listing->inventory->serial() }} of {{ $item->owners()->count() + $item->stock }}</div>
                                        @if (!Auth::check() || (Auth::check() && $listing->seller->id != Auth::user()->id))
                                            <a class="button bucks small flat" data-modal-open="purchase_{{ $listing->id }}">Buy for <span class="bucks-icon img-white"></span> {{ $listing->price }}</a>
                                        @else
                                            <form action="{{ route('shop.take_off_sale') }}" method="POST" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $listing->id }}">
                                                <button class="small flat" style="background:#999;" type="submit">TAKE OFFSALE (<span class="bucks-icon img-white"></span> {{ $listing->price }})</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="modal" style="display:none;" data-modal="purchase_{{ $listing->id }}">
                                <div class="modal-content">
                                    <span class="close" data-modal-close="purchase_{{ $listing->id }}">×</span>
                                    @guest
                                        <span>You are not logged in</span>
                                        <hr>
                                        <span>You must login to purchase an item</span>
                                        <div class="modal-buttons">
                                            <a href="{{ route('auth.login.index') }}" class="button bucks" style="margin-right:10px;">Login</a>
                                            <button class="cancel-button" type="button" data-modal-close="purchase_{{ $listing->id }}">Cancel</button>
                                        </div>
                                    @else
                                        @if (Auth::user()->currency_bucks < $listing->price)
                                            <span>You do not have enough</span>
                                            <hr>
                                            <div style="text-align:center;padding-top:25px;padding-bottom:25px;">
                                                <svg class="bucks-icon" style="transform:scale(5);padding:0px;margin-top:10px;"></svg>
                                            </div>
                                            <div class="modal-buttons">
                                                <a href="{{ route('account.billing.index') }}" class="button bucks" style="margin-right:10px;">Buy More</a>
                                                <button class="cancel-button" type="button" data-modal-close="purchase_{{ $listing->id }}">Cancel</button>
                                            </div>
                                        @else
                                            <span>Buy Item</span>
                                            <hr>
                                            <span>Are you sure you want to buy <b>{{ $item->name }}</b> for <span class="bucks-icon" style="margin-left:2px;"></span> {{ $listing->price }}?</span>
                                            <div class="modal-buttons">
                                                <form action="{{ route('shop.purchase') }}" style="display:inline;" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                    <input type="hidden" name="currency" value="bucks">
                                                    <input type="hidden" name="reseller_id" value="{{ $listing->id }}">
                                                    <button class="bucks" style="margin-right:10px;" type="submit">Buy Now</button>
                                                </form>
                                                <button type="button" class="cancel-button" data-modal-close="purchase_{{ $listing->id }}">Cancel</button>
                                            </div>
                                        @endif
                                    @endguest
                                </div>
                            </div>
                        @empty
                            <span>No one is currently selling this item.</span>
                        @endforelse
                    </div>
                    <div class="pages">{{ $item->resellers()->onEachSide(1) }}</div>
                </div>
            </div>
        @endif
        <div class="tabs">
            <div class="tab col-1-2 active" data-tab="comments">Comments</div>
            <div class="tab col-1-2" data-tab="recommended">Recommended</div>
            <div class="tab-holder">
                <div class="tab-body active" data-tab-section="comments">
                    <div class="comments-holder">
                        <form action="{{ route('shop.comment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <span class="smedium-text bold">Comment</span>
                            <textarea class="width-100 mb2" style="height:80px;" name="comment" placeholder="Enter Comment"></textarea>
                            <button class="blue" type="submit">Post</button>
                        </form>
                        <hr>
                        <div id="comments"></div>
                    </div>
                </div>
                <div class="tab-body" data-tab-section="recommended">
                    <div id="recommendations"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
