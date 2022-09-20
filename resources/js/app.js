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

window._token = '';

$(() => window._token = $('meta[name="csrf-token"]').attr('content'));

$.ajaxSetup({
    beforeSend: function(xhr, options) {
        if (options.url.indexOf('http') !== 0 )
            options.url = options.url;
    }
});

$('[data-dropdown-open]').click(function(event) {
    var dropdown = $(this).attr('data-dropdown-open');
    var object = `[data-dropdown="${dropdown}"]`;
    var opened = $(object).hasClass('active');

    if (!opened) {
        if (targetMatches(true, event.target, `[data-dropdown-open="${dropdown}"], [data-dropdown-open="${dropdown}"] *`)) {
            const self = this;

            $(object).addClass('active').css({
                top: ($(self).height() + 30) + 'px',
                left: $(self).offset().left + 'px'
            });

            window.onresize = function() {
                $(object).css({
                    top: ($(self).height() + 30) + 'px',
                    left: $(self).offset().left + 'px'
                });
            };
        }
    } else {
        if (targetMatches(false, event.target, `${dropdown}, ${dropdown} *`)) {
            $(object).removeClass('active');

            window.onresize = null;
        }
    }
});


function targetMatches(does, eventTarget, target)
{
    if (does)
        return (eventTarget.matches) ? eventTarget.matches(target) : eventTarget.msMatchesSelector(target);

    return (eventTarget.matches) ? !eventTarget.matches(target) : !eventTarget.msMatchesSelector(target);
}
