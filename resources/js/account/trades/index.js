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
var userInfo = {};
var currentCategory;
var currentTrade;

$(() => {
    const routesMeta = $('meta[name="routes"]');
    routes.process = routesMeta.attr('data-process');

    const userInfoMeta = $('meta[name="user-info"]');
    userInfo.id = userInfoMeta.attr('data-id');

    switchCategory('inbound');

    $('[data-modal-close]').click(function() {
        $(`[data-modal="${$(this).attr('data-modal-close')}"]`).hide();
    });

    $('#category').change(function() {category
        switchCategory(this.value);
    });
});

function switchCategory(category)
{
    $.get(`/api/v1/user/trades/${userInfo.id}/${category}`).done(function(data) {
        currentCategory = category;

        $('#trade').html(`<div class="text-center">No trade selected.</div>`);

        if (!data.data.length)
            return $('#trades').removeClass('trade-picker').html(`<div class="text-center" style="margin-top:10px;">No trades available.</div`);

        $('#trades').addClass('trade-picker').html('');

        var shown = false;

        $.each(data.data, function() {
            var isFirst = !shown;
            var word = 'Pending';
            var selected = (isFirst) ? 'selected' : '';

            if (isFirst) {
                shown = true;
                showTrade(this.id)
            }

            if (this.is_accepted)
                word = 'Accepted';
            else if (this.is_cancelled)
                word = 'Declined';

            $('#trades').append(`
            <div class="trade ${selected}" data-trade="${this.id}">
                <img src="${this.user.thumbnail}" class="trade-user-thumbnail">
                <div style="padding-top: 20px;">${this.user.username}</div>
                <div class="trade-date dark-gray-text">${this.human_time}</div>
                <div class="trade-status dark-gray-text">${word}</div>
            </div>`);
        });

        $('[data-trade]').click(function() {
            var oldTrade = currentTrade;

            $(`[data-trade="${currentTrade}"]`).removeClass('selected');
            $(this).addClass('selected');

            currentTrade = $(this).attr('data-trade');

            if (currentTrade != oldTrade)
                showTrade(currentTrade);
        });
    });
}

function showTrade(id)
{
    $.get(`/api/v1/user/trades/${id}`).done(function(data) {
        currentTrade = data.data.id;

        var word = '';
        var user = '';
        var html = '';
        var buttons = '';

        if (currentCategory == 'inbound')
            buttons = `
            <div class="text-center">
                <button class="button green" data-accept>ACCEPT</button>
                <form action="${routes.process}" method="POST" style="display:inline;">
                    <input type="hidden" name="_token" value="${_token}">
                    <input type="hidden" name="id" value="${data.data.id}">
                    <button class="button red" name="action" value="decline">DECLINE</button>
                </form>
            </div>`;
        else if (currentCategory == 'outbound')
            buttons = `
            <div class="text-center">
                <form action="${routes.process}" method="POST" style="display:inline;">
                    <input type="hidden" name="_token" value="${_token}">
                    <input type="hidden" name="id" value="${data.data.id}">
                    <button class="button red" name="action" value="decline">CANCEL</button>
                </form>
            </div>`;

        const receiving = data.data.trade[0];
        const giving = data.data.trade[1];

        if (userInfo.id == receiving.user.id) {
            word = 'from';
            user = giving.user;
            html = tradeHtml('Giving', receiving, true) + tradeHtml('Getting', giving, false);
        } else {
            word = 'To';
            user = receiving.user;
            html = tradeHtml('Giving', giving, true) + tradeHtml('Getting', receiving, false);
        }

        $('#trade').html(`
        <div class="large-text">Trade ${word} <a href="${user.url}">${user.username}</a></div>
        ${html}
        ${buttons}`);

        $('[data-accept]').click(function() {
            $('#acceptModalId').val(data.data.id);
            $(`[data-modal="accept"]`).show();
        });
    });
}

function tradeHtml(text, trade, showDivider)
{
    var items = '';
    var average = '';
    var divider = (showDivider) ? '<hr>' : '';
    var bucks = (trade.bucks == 0) ? '' : `
    <div class="text-center" style="padding:25px;">
        <span class="trade-bucks">+</span>
        <span class="bucks-text bold">
            <span class="bucks-icon" style="transform:scale(2.5);margin:22px;"></span>
            <span class="trade-bucks">${trade.bucks}</span>
        </span>
    </div>`;

    if (!trade.items.length) {
        items = `<div class="smedium-text">Nothing</div>`;
    } else {
        var averageTotal = 0;

        items += `<ul class="tile-holder no-center">`;

        $.each(trade.items, function() {
            var serial = (this.serial) ? `<span class="trade-serial">#${this.serial}</span>` : '';

            averageTotal += this.item.average_price;
            items += `
            <li class="item-card-tile five-wide inline no-border">
                <a href="${this.item.url}" target="_blank">
                    <div class="item-card-container">
                        <div class="item-card-image">
                            ${serial}
                            <img src="${this.item.thumbnail}">
                        </div>
                        <div class="item-card-name gray-text ellipsis">${this.item.name}</div>
                        <div class="item-card-data">
                            <span class="light-gray-text" style="font-size:12px;">Avg.</span>
                            <div class="inline">
                                <span class="bucks-icon"></span>
                                <span style="color:#009624;font-size:12px;">${this.item.average_price_abbr}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </li>`;
        });

        average = `
        <div class="smedium-text">
            <span>Total average price:</span>
            <span class="bucks-text very-bold">
                <span class="bucks-icon" style="margin:3px;"></span>
                <span>${averageTotal}</span>
            </span>
        </div>`;

        items += `</ul>`;
    }

    return `
    <div class="medium-text">${text}</div>
    ${items}
    ${bucks}
    ${average}
    ${divider}`;
}
