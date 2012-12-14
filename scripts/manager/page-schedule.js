$(function () {
    'use strict';
    var container = $('#schedule'),
        util = $('#schedule-util'),
        DATEMAX = '99999999',
        TIMEMAX = '9999';

    // MAIN
    function dateNumber(string) {
        return string.split('/').reverse().join('') || DATEMAX;
    }
    function timeNumber(string) {
        return string.replace(':', '') || TIMEMAX;
    }
    function build(element) {
       element.find('.date').mask('99/99/9999').datepicker();
       element.find('.time').mask('99:99');
    }
    function moveEffect(element) {
        element.css('opacity', 0).fadeTo(300, 1);
    }

    // DAY
    function clearDay(day) {
        day.attr('data-date', DATEMAX);
        day.find('.itens p').slice(1).remove();
        day.find(':input').val('');
        day.find('.itens p').attr('data-time', TIMEMAX);
    }

    container.on('click', '.day-add', function (event) {
        var dayBase = util.find('.day:last'),
            day = dayBase.clone();

        event.preventDefault();
        clearDay(day);
        // datepicker bugs
        day.find('.date').removeClass('hasDatepicker').removeAttr('id');
        build(day);
        day.insertAfter(dayBase);
    });

    container.on('click', '.day-remove', function () {
        var day = $(this).closest('.day');

        event.preventDefault();
        if (day.siblings().length) {
            day.remove();
        } else {
            clearDay(day);
        }
    });

    container.on('change', '.date', function (event) {
        var field = $(this),
            value = field.val(),
            number = dateNumber(value),
            day = field.closest('.day'),
            dayIndex = day.index()

        day.attr('data-date', number);
        day.find('.date-hidden').val(value);

        util.find('.day').sort(function (a, b) {
            return parseInt(a.getAttribute('data-date'), 10) - parseInt(b.getAttribute('data-date'), 10);
        }).appendTo(util);

        if (day.index() != dayIndex) {
            moveEffect(day);
        }
    });

    // ITEM
    function clearItem(item) {
        item.find(':input').not('.date-hidden').val('');
        item.attr('data-time', TIMEMAX);
    }

    container.on('click', '.item-add', function (event) {
        var day = $(this).closest('.day'),
            itemBase = day.find('.itens p:last'),
            item  = itemBase.clone();

        event.preventDefault();
        clearItem(item);
        build(item);
        item.insertAfter(itemBase);
    });

    container.on('click', '.item-remove', function (event) {
        var item = $(this).closest('p');

        event.preventDefault();
        if (item.siblings().length) {
            item.remove();
        } else {
            clearItem(item);
        }
    });

    container.on('change', '.time', function (event) {
        var field = $(this),
            number = timeNumber(field.val()),
            item = field.closest('p'),
            itemIndex = item.index(),
            itens = item.parent();

        item.attr('data-time', number);
        itens.children().sort(function (a, b) {
            return parseInt(a.getAttribute('data-time'), 10) - parseInt(b.getAttribute('data-time'), 10);
        }).appendTo(itens);

        if (item.index() != itemIndex) {
            moveEffect(item);
        }
    });

    // ERROR
    fields.fns.push(function () {
        var days = util.find('.day'),
            dateError = false,
            dateEmptyError = false,
            timeError = false,
            msg = '';

        days.each(function (i) {
            var day = this,
                itens = $(day).find('.itens p'),
                dateEmpty = true;

            // day
            if (day.getAttribute('data-date') != DATEMAX) {
                dateEmpty = false;
                days.slice(i + 1).each(function () {
                    if (this.getAttribute('data-date') === day.getAttribute('data-date')) {
                        $(day).add(this).find('.date').addClass('field-error');
                        dateError = true;
                    }
                });
            }

            // itens
            itens.each(function (i) {
                var item = this;
                if (item.getAttribute('data-time') != TIMEMAX) {
                    if (dateEmpty) {
                        $(day).find('.date').addClass('field-error');
                        dateEmptyError = true;
                    }
                    itens.slice(i + 1).each(function () {
                        if (this.getAttribute('data-time') === item.getAttribute('data-time')) {
                            $(item).add(this).find('.time').addClass('field-error');
                            timeError = true;
                        }
                    });
                }
            })
        });

        msg += dateError ? '<li>Não pode haver <strong>datas</strong> duplicadas na programação.</li>': '';
        msg += dateEmptyError ? '<li>Todos os horários devem ter <strong>datas</strong> definidas.</li>' : '';
        msg += timeError ? '<li>Não pode haver <strong>horários</strong> duplicadas na programação.</li>' : '';
        return msg;
    });

    // INITIALIZE
    build(container);
});