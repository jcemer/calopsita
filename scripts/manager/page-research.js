$(function () {
    'use strict';
    var container = $('#research'),
        util = $('#research-util');

    // MAIN
    function countNumber(string) {
        return ('0' + count).slice(-2);
    }
    function moveEffect(element) {
        element.css('opacity', 0).fadeTo(300, 1);
    }

    // QUESTION
    function clearQuestion(question) {
        question.find('.itens p').slice(1).remove();
        question.find(':input').val('');
        question.find('.question-active').val('1');
    }

    container.on('click', '.question-hide', function () {
        var question = $(this).closest('.question'),
            active = question.toggleClass('active').hasClass('active');
        question.find('.question-active').val(active ? '1' : '0');
    });

    container.on('change', '.question-type', function () {
        var type = $(this).val(),
            question = $(this).closest('.question');
        question.find('.itens').toggleClass('active', type == '4');
    });

    // ITEM
    function clearItem(item) {
        item.removeClass('db').addClass('active');
        item.find(':input').not('.item-question').val('');
        item.find('.item-active').val('1');
    }

    container.on('click', '.item-add', function (event) {
        var question = $(this).closest('.question'),
            itemBase = question.find('.itens p:last'),
            item = itemBase.clone();

        if (question.find('.itens p').length >= 20) {
            alert('O máximo permitido são vinte questões internas.');
        } else {
            event.preventDefault();
            clearItem(item);
            item.insertAfter(itemBase);
        }
    });

    container.on('click', '.item-remove', function (event) {
        var item = $(this).closest('p'),
            active;

        event.preventDefault();
        if (item.hasClass('db')) {
            active = item.toggleClass('active').hasClass('active');
            item.find('.item-active').val(active ? '1' : '0');
        } else {
            item.remove();
        }
    });

    fields.fns.push(function () {
        var itensError = false,
            msg = '';

        util.find('.question.active .itens.active').each(function () {
            if (!$(this).find('p.active').length) {
                itensError = true;
                $(this).find('.field').addClass('field-error');
            }
        });

        msg += itensError ? '<li>Não pode haver questões do tipo <strong>outra questões</strong> com todos os itens ocultos.</li>' : '';
        return msg;
    });

    // // ERROR
    // fields.fns.push(function () {
    //     var days = util.find('.day'),
    //         dateError = false,
    //         dateEmptyError = false,
    //         timeError = false,
    //         msg = '';

    //     days.each(function (i) {
    //         var day = this,
    //             itens = $(day).find('.itens p'),
    //             dateEmpty = true;

    //         // day
    //         if (day.getAttribute('data-date') != DATEMAX) {
    //             dateEmpty = false;
    //             days.slice(i + 1).each(function () {
    //                 if (this.getAttribute('data-date') === day.getAttribute('data-date')) {
    //                     $(day).add(this).find('.date').addClass('field-error');
    //                     dateError = true;
    //                 }
    //             });
    //         }

    //         // itens
    //         itens.each(function (i) {
    //             var item = this;
    //             if (item.getAttribute('data-time') != TIMEMAX) {
    //                 if (dateEmpty) {
    //                     $(day).find('.date').addClass('field-error');
    //                     dateEmptyError = true;
    //                 }
    //                 itens.slice(i + 1).each(function () {
    //                     if (this.getAttribute('data-time') === item.getAttribute('data-time')) {
    //                         $(item).add(this).find('.time').addClass('field-error');
    //                         timeError = true;
    //                     }
    //                 });
    //             }
    //         })
    //     });

    //     msg += dateError ? '<li>Não pode haver <strong>datas</strong> duplicadas na programação.</li>': '';
    //     msg += dateEmptyError ? '<li>Todos os horários devem ter <strong>datas</strong> definidas.</li>' : '';
    //     msg += timeError ? '<li>Não pode haver <strong>horários</strong> duplicadas na programação.</li>' : '';
    //     return msg;
    // });
});