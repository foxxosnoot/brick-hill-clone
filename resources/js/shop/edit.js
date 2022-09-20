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

var routes = {};
var currentCategory = null;
var currentSearch = '';
var currentSort = 'updated';
var currentPage = 1;
var hitEnd = true;
var isPreviousEventComplete = true;
var hasSearched = false;

$(() => {
    const routesMeta = $('meta[name="routes"]');
    routes.index = routesMeta.attr('data-index');
    routes.indexTitle = routesMeta.attr('data-index-title');

    $('#search').submit(function(event) {
        event.preventDefault();

        var oldSearch = currentSearch;
        currentSearch = $(this).find('input').val();

        if (currentSearch != oldSearch)
            search(currentCategory, 1, currentSearch, true);
    });

    $('#sort').change(function() {
        currentSort = this.value;
        search(currentCategory, 1, currentSearch, true);
    });

    $('[data-category]').click(function() {
        var oldCategory = currentCategory;

        $(`[data-category="${currentCategory}"]`).removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-category');

        if (currentCategory != oldCategory)
            search(currentCategory, 1, currentSearch, true);
    });

    $(window).scroll(function() {
        if (hasSearched && isPreviousEventComplete && $(window).scrollTop() + $(window).height() >= $(document).height() - 100 && !hitEnd)
            search(currentCategory, currentPage + 1, currentSearch, false);
    });
});

function search(category, page, search, clear)
{
    if (!hasSearched) {
        if (!currentCategory) {
            category = 'all';
            currentCategory = 'all';
        }

        document.title = routes.indexTitle;
        window.history.pushState(null, null, routes.index);
    }

    hasSearched = true;
    isPreviousEventComplete = false;

    $.get('/api/v1/shop/list', { sort: currentSort, type: category, search, limit: 20, page }).done((data) => {
        $(`[data-category='${currentCategory}']`).removeClass('active');

        currentCategory = category;
        currentSearch = search;

        $(`[data-category='${currentCategory}']`).addClass('active');

        if (clear)
            $('#items').html('');

        if (!data.data.length) {
            currentPage = 1;
            hitEnd = true;
            return $('#items').html('');
        }

        currentPage = data.current_page;
        hitEnd = data.current_page == data.total_pages;

        $.each(data.data, function() {
            var price = '';
            var thumbnail = `
            <div class="thumbnail dark" style="position:relative;padding:20px;">
                <img src="${this.thumbnail}" alt="${this.name}">
            </div>`;

            if (!this.offsale) {
                if (this.bits == 0 && this.bucks == 0) {
                    price += `<span class="offsale-text">Free</span>`;
                } else {
                    if (this.bucks > 0)
                        price += `<span class="bucks-text"><span class="bucks-icon"></span> ${this.bucks}</span>`;

                    if (this.bucks > 0 && this.bits > 0)
                        price += `<div style="width:5px;display:inline-block;"></div>`;

                    if (this.bits > 0)
                        price += `<span class="bits-text"><span class="bits-icon"></span> ${this.bits}</span>`;
                }
            } else if (this.offsale) {
                price = `<span class="offsale-text">Offsale</span>`;
            }

            if (this.special_edition)
                thumbnail = `
                <div class="thumbnail dark" style="position:relative;border:5px solid #ffd52d;border-bottom:0;padding:15px 15px 20px;">
                    <span class="special-e-icon"></span>
                    <img src="${this.thumbnail}" alt="${this.name}">
                </div>`;
            else if (this.special)
                thumbnail = `
                <div class="thumbnail dark" style="position:relative;border:5px solid #ffd52d;border-bottom:0;padding:15px 15px 20px;">
                    <span class="special-icon"></span>
                    <img src="${this.thumbnail}" alt="${this.name}">
                </div>`;

            $('#items').append(`
            <div class="col-1-4 mobile-col-1-2 mobile-half-fill" style="padding-right:20px;">
                <div class="card">
                    <a href="${this.url}">${thumbnail}</a>
                    <div class="item-content">
                        <a href="${this.url}" style="color:#000;">
                            <span class="name">${this.name}</span>
                        </a>
                        <div class="creator">By <a href="${this.creator.url}">${this.creator.username}</a></div>
                        <div class="price">${price}</div>
                    </div>
                </div>
            </div>`);

            isPreviousEventComplete = true;
        });
    }).fail(() => {
        currentPage = 1;
        hitEnd = true;
        isPreviousEventComplete = true;
        $('#items').html('<div class="col-1-1">Unable to load items.</div>')
    });
}
