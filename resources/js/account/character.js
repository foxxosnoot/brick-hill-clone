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

var currentTab = 'hats';
var currentSearch = '';
var routes = {};
var bodyColor;
var currentPage;
var currentPart = 'head';
var avatarImage;

$(() => {
    const meta = 'meta[name="routes"]';
    routes.regenerate = $(meta).attr('data-regen');
    routes.inventory = $(meta).attr('data-inventory');
    routes.wearing = $(meta).attr('data-wearing');
    routes.update = $(meta).attr('data-update');

    inventory(currentTab, 1);
    wearing();

    $('[data-part]').click(function() {
        currentPart = $(this).attr('data-part');
        $('#currentlyEditing').html(capitalizeWords(currentPart.replace('_', ' ')));
    });

    $('[data-color]').click(function() {
        bodyColor = $(this).css('background-color');

        update('color', $(this).attr('data-color'));
    });

    $('[data-tab]').click(function() {
        var oldTab = currentTab;

        $(`[data-tab="${currentTab}"]`).removeClass('active');
        $(this).addClass('active');

        currentTab = $(this).attr('data-tab');

        if (oldTab != currentTab)
            inventory(currentTab, 1);
    });

    $('#search').on('keypress', function(event) {
        var search = $(this).val();

        if (event.which === 13 && search != currentSearch)
            inventory(currentTab, 1, search);
    });
});

function update(action, id)
{
    var params = {};
    var bodyPart = currentPart;

    avatarImage = $('#avatar').attr('src');

    $('#avatar').hide();
    $('#loader').show();

    switch (action) {
        case 'wear':
            params = { _token, action, id };
            break;
        case 'remove':
            params = { _token, action, type: id };
            break;
        case 'color':
            params = { _token, action, color: id, body_part: bodyPart };
            break;
        default:
            return;
    }

    $.post(routes.update, params).done(function(data) {
        $('#avatar').attr('src', data.thumbnail);
        $('#avatar').show();
        $('#loader').hide();

        if (typeof data.error !== 'undefined')
            return showError(data.error);

        switch (action) {
            case 'wear':
            case 'remove':
                inventory(currentTab, currentPage);
                wearing();
                break;
            case 'color':
                $(`[data-part="${bodyPart}"]`).css('background-color', bodyColor);
                break;
        }
    }).fail(() => showError('Unable to update character.'));
}

function wearing()
{
    $.get(routes.wearing).done((data) => {
        $('#wearing').html('');

        if (typeof data.error !== 'undefined')
            return $('#wearing').html(`<div class="col">${data.error}</div>`);

        $.each(data, function() {
            $('#wearing').append(`
            <div class="avatar-card crate" style="position:relative;">
                <button class="red" style="position:absolute;top:10px;left:10px;padding:4px;" onclick="update('remove', '${this.type}')">-</button>
                <div class="img-holder">
                    <img src="${this.thumbnail}">
                </div>
                <a href="${this.url}">
                    <span class="ellipsis" style="color:#767676">${this.name}</span>
                </a>
            </div>`);
        });
    }).fail(() => $('#wearing').html('<div class="col-1-1">Unable to load items.</div>'));
}

function inventory(category, page, search = currentSearch)
{
    currentSearch = search;

    $.get(routes.inventory, { category, page, search }).done((data) => {
        $('#inventory').html('');
        currentPage = page;

        if (typeof data.error !== 'undefined')
            return $('#inventory').html('');

        $.each(data.items, function() {
            const disabled = (this.is_wearing) ? 'disabled' : '';

            $('#inventory').append(`
            <div class="avatar-card crate" style="position:relative;">
                <button class="green" style="position:absolute;top:10px;left:10px;padding:4px;" onclick="update('wear', ${this.id})" ${disabled}>+</button>
                <div class="img-holder">
                    <img src="${this.thumbnail}">
                </div>
                <a href="${this.url}">
                    <span class="ellipsis" style="color:#767676">${this.name}</span>
                </a>
            </div>`);
        });

        if (data.total_pages > 1) {
            const previousDisabled = (data.current_page == 1) ? 'disabled' : '';
            const nextDisabled = (data.current_page == data.total_pages) ? 'disabled' : '';
            const previousPage = data.current_page - 1;
            const nextPage = data.current_page + 1;

            $('#inventory').append(`
            <div class="col-1-1 center-text">
                <button class="small red" onclick="inventory('${currentTab}', ${previousPage})" ${previousDisabled}>&laquo;</button>
                <span style="margin-left:5px;margin-right:5px;">${data.current_page} of ${data.total_pages}</span>
                <button class="small green" onclick="inventory('${currentTab}', ${nextPage})" ${nextDisabled}>&raquo;</button>
            </div>`);
        }
    }).fail(() => $('#inventory').html('<div class="col-1-1">Unable to load items.</div>'));
}

function showError(text)
{
    $('#globalError').html(text).show();
    $('#avatar').attr('src', avatarImage);
}

function capitalizeWords(words) {
    var separateWord = words.toLowerCase().split(' ');

    for (var i = 0; i < separateWord.length; i++) {
       separateWord[i] = separateWord[i].charAt(0).toUpperCase() +
       separateWord[i].substring(1);
    }

    return separateWord.join(' ');
 }
