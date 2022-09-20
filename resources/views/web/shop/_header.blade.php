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

<div class="col-10-12 push-1-12 shop-bar">
    <div class="col-9-12">
        <div class="card">
            <div class="content">
                <div class="overflow-auto">
                    <form id="search">
                        <div class="col-8-12">
                            <input class="input rigid width-100" style="height:41px;" type="text" placeholder="Search">
                        </div>
                        <div class="col-2-12 mobile-col-1-2">
                            <button class="button blue mobile-fill" style="font-size:15px;" type=>Search</button>
                        </div>
                        <div class="col-2-12 mobile-col-1-2">
                            <a href="{{ route('shop.create') }}" class="button green mobile-fill" style="font-size:15px;">Create</a>
                        </div>
                    </form>
                </div>
                <hr>
                <div class="shop-categories">
                    <div class="category" data-category="all"><a>All</a></div>
                    <div class="category" data-category="hats"><a>Hats</a></div>
                    <div class="category" data-category="tools"><a>Tools</a></div>
                    <div class="category" data-category="t-shirts"><a>T-Shirts</a></div>
                    <div class="category" data-category="faces"><a>Faces</a></div>
                    <div class="category" data-category="shirts"><a>Shirts</a></div>
                    <div class="category" data-category="pants"><a>Pants</a></div>
                    <div class="category" data-category="heads"><a>Heads</a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-3-12">
        <div class="card">
            <div class="content">
                <div class="select-color-text mb1">Advanced Sort</div>
                <hr style="margin-top:-3px;">
                <select class="select width-100" id="sort">
                    <option value="updated" selected>Recently Updated</option>
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="expensive">Most Expensive</option>
                    <option value="inexpensive">Least Expensive</option>
                </select>
            </div>
        </div>
    </div>
</div>
