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

var id;
var gamesCount = 0;
var currentCategory = '';
var currentPage = 1;
var currentTab = 'crate';
var currentGame = 1;

$(() => {
    const meta = $('meta[name="user-info"]');
    id = parseInt(meta.attr('data-id'));

    inventory('all', 1);
    games();

    $('[data-tab]').click(function() {
        $(`[data-tab="${currentTab}"]`).removeClass('blue').addClass('transparent');
        $(`[data-tab-section="${currentTab}"]`).hide();

        $(this).removeClass('transparent').addClass('blue');
        currentTab = $(this).attr('data-tab');

        $(`[data-tab-section="${currentTab}"]`).show();
    });

    $('[data-category]').click(function() {
        var oldCategory = currentCategory;

        $(`[data-category="${currentCategory}"]`).removeClass('active');
        $(this).addClass('active');

        currentCategory = $(this).attr('data-category');

        if (currentCategory != oldCategory)
            inventory(currentCategory, 1);
    });

    $(document).on('click', '.read-more-desc', function () {
        $(this).parent().parent().toggleClass('closed');

        if($(this).text() == 'Read More') {
            $(this).text('Show Less');
            $('.user-description-box .content').css('min-height', $('.user-description-box .content').height() + 33)
        } else {
            $(this).text('Read More');
            $('.user-description-box .content').css('min-height', $('.user-description-box .content').height() - 33)
        }
    });

    if ($('.user-description-box .user-desc').height() <= 80) {
        $('.read-more-desc').css('display', 'none');
        $('.toggle-user-desc').addClass('open');
    }
});

function inventory(category, page)
{
    $.get(`/api/v1/user/${id}/crate`, { type: category, page, limit: 10 }).done((data) => {
        $('#inventory').html('');
        currentCategory = category;
        currentPage = page;

        if (!data.data.length)
            return $('#inventory').html('');

        $.each(data.data, function() {
            var serial = (this.item.is_special) ? `<span class="trade-serial">#${this.serial}</span>` : '';

            $('#inventory').append(`
            <li class="col-1-5 mobile-col-1-2" style="padding-left:3px;padding-right:3px;">
                <a href="${this.item.url}">
                    <div class="profile-card crate">
                        ${serial}
                        <img src="${this.item.thumbnail}">
                        <div class="ellipsis" style="color:rgb(118,118,118);height:19px;">${this.item.name}</div>
                    </div>
                </a>
            </li>`);
        });

        if (data.total_pages > 1) {
            const previousDisabled = (data.current_page == 1) ? 'disabled' : '';
            const nextDisabled = (data.current_page == data.total_pages) ? 'disabled' : '';
            const previousPage = data.current_page - 1;
            const nextPage = data.current_page + 1;

            $('#inventory').append(`
            <li class="col-1-1 center-text">
                <button class="small red" onclick="inventory('${currentCategory}', ${previousPage})" ${previousDisabled}>&laquo;</button>
                <span style="margin-left:5px;margin-right:5px;">${data.current_page} of ${data.total_pages}</span>
                <button class="small green" onclick="inventory('${currentCategory}', ${nextPage})" ${nextDisabled}>&raquo;</button>
            </li>`);
        }
    }).fail(() => $('#inventory').html(''));
}

function games()
{
    $.get(`/api/v1/user/${id}/sets`).done((data) => {
        gamesCount = data.data.length;

        if (gamesCount == 0)
            return $('#games').html(`
            <div class="card">
                <div class="content" style="text-align:center;border-radius:5px;">
                    <span>This user has no sets!</span>
                </div>
            </div>`);

        var i = 0;
        var html = '';
        var slider = (gamesCount == 1) ? '' : `
        <div>
            <a class="slider-button left" data-games-paginate="previous">
                <i class="fas fa-angle-left"></i>
            </a>
            <a class="slider-button right" data-games-paginate="next">
                <i class="fas fa-angle-right"></i>
            </a>
        </div>`;

        $.each(data.data, function() {
            i++;

            var active = (i == 1) ? 'active' : '';

            html += `
            <li class="set ${active}" data-iteration="${i}">
                <a href="${this.url}">
                    <div class="card">
                        <div class="content ellipsis" style="text-align:center;overflow:hidden;border-radius:5px;">
                            <span class="set-title">${this.name}</span>
                            <img src="${this.thumbnail}" style="width:400px;max-width:90%;margin:10px auto;display:block;height:266.66px;">
                            <div class="set-stats">
                                <ul>
                                    <li>
                                        <div>Visits</div>
                                        <div>${this.visits}</div>
                                    </li>
                                    <li>
                                        <div>Playing</div>
                                        <div>${this.playing}</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </a>
            </li>`;
        });

        $('#games').html(`
        <ul class="set-list">
            ${html}
        </ul>
        ${slider}`);

        $('[data-games-paginate]').click(function() {
            var action = $(this).attr('data-games-paginate');
            var game;

            if (action == 'previous')
                game = (currentGame == 1) ? gamesCount : currentGame - 1;
            else
                game = (currentGame >= gamesCount) ? 1 : currentGame + 1;

            $(`.set[data-iteration="${currentGame}"]`).removeClass('active');
            $(`.set[data-iteration="${game}"]`).addClass('active');

            currentGame = game;
        });
    });
}
