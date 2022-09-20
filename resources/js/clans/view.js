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

var clanInfo = {};
var currentTab = 'members';

$(() => {
    const clanInfoMeta = $('meta[name="clan-info"]');
    clanInfo.id = clanInfoMeta.attr('data-id');

    members(1, 1);

    $('[data-tab]').click(function() {
        $(`[data-tab="${currentTab}"]`).removeClass('blue').addClass('transparent');
        $(`[data-tab-section="${currentTab}"]`).hide();

        $(this).removeClass('transparent').addClass('blue');
        currentTab = $(this).attr('data-tab');

        $(`[data-tab-section="${currentTab}"]`).show();
    });

    $('#rank').change(function() {
        members(this.value, 1);
    });
});

function members(rank, page)
{
    $.get(`/api/v1/clans/members/${clanInfo.id}/${rank}`, { page }).done(function(data) {
        $('#members').html('');

        if (!data.data.length)
            return;

        $.each(data.data, function() {
            $('#members').append(`
            <a href="${this.user.url}">
                <div class="col-1-5 mobile-col-1-2">
                    <img style="width:145px;height:145px;" src="${this.user.thumbnail}">
                    <div class="ellipsis">${this.user.username}</div>
                </div>
            </a>`);
        });

        if (data.total_pages > 1) {
            const previousDisabled = (data.current_page == 1) ? 'disabled' : '';
            const nextDisabled = (data.current_page == data.total_pages) ? 'disabled' : '';
            const previousPage = data.current_page - 1;
            const nextPage = data.current_page + 1;

            $('#members').append(`
            <li class="col-1-1 center-text">
                <button class="small red" onclick="members('${rank}', ${previousPage})" ${previousDisabled}>&laquo;</button>
                <span style="margin-left:5px;margin-right:5px;">${data.current_page} of ${data.total_pages}</span>
                <button class="small green" onclick="members('${rank}', ${nextPage})" ${nextDisabled}>&raquo;</button>
            </li>`);
        }
    }).fail(() => $('#members').html(''));
}
