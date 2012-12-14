String.prototype.empty = function(){
        return this.match(/^(\s*|0+)$/);
}

/* * *
        IMAGE ONE
* * */
var image_one = {
        initialize: function () {
                $('.image-one a').click(this.del);
        },

        del: function () {
                var container = $('.image-one'),
                        deleted = container.toggleClass('image-delete').hasClass('image-delete');
                $(this).html(deleted ? '&ntilde; excluir' : 'excluir');
                container.find('input').val(deleted ? '1' : '');
                return false;
        }
}

/* * *
        IMAGES
* * */
var images = {
        count: 1,

        initialize: function (){
                $('#image-add').click(this.add);
                if($('#images').get(0)){
                        $('#images .iUp').click(this.up);
                        $('#images .iDown').click(this.down);
                        $('#images .icon-delete').click(this.del);
                        this.check();
                }
        },

        add: function () {
                var tagname = $('#image-field')[0].tagName;
                if (images.count < 4) {
                        $('#image-fields').append('<' + tagname + '>' + $('#image-field').html().replace(/\*/g, '').replace(/(_insert)0/g, '$1' + this.count) + '</' + tagname + '>');
                }
                images.count++;
                return false;
        },

        check: function () {
                $('#images_position, #images_delete').val('');
                var first = 0,
                        last = $('#images li').length - 1;

                var deleteall = $('#images').hasClass('delete-all');
                if (!deleteall) { //delete-all
                        $('#images').removeClass('image-delete');
                }

                $('#images li').each(function (i) {
                        var id = $(this).attr('id').match(/^(?:.*?)[_](.*)$/)[1];
                        if ($(this).hasClass('image-delete')) {
                                $('#images_delete')[0].value += '|' + id;
                        } else {
                                $('#images_position')[0].value += '|' + id;
                        }

                        var icons = $(this).children('.icons');
                        if(i == first) { // up
                                icons.children('.iUp').hide();
                                if (!deleteall) {
                                        icons.children('.icon-delete').html('excluir').hide();
                                }
                        } else {
                                icons.children('.icon').show();
                        }

                        if(i == last) { // down
                                icons.children('.iDown').hide();
                        } else {
                                icons.children('.iDown').show();
                        }
                });
        },

        element: function (element){
                return $(element).parent().parent();
        },

        _move: function (element){
                element.css('opacity', 0).fadeTo(150, 1);
                images.check();
        },

        up: function (){
                var element = images.element(this).css('z-index', 100)
                element.animate({ 'opacity': 0 }, 250, 'swing', function (){
                        element.insertBefore(element.prev()).css({ 'opacity': 1, 'z-index': 1 });
                        images._move(element.next());
                });
                return false;
        },

        down: function () {
                var element = images.element(this).css('z-index', 100);
                element.animate({ 'opacity': 0 }, 250, 'swing', function(){
                        element.insertAfter(element.next()).css({ 'opacity': 1, 'z-index': 1 });
                        images._move(element.prev());
                });
                return false;
        },

        del: function (){
                var deleted = images.element(this).toggleClass('image-delete').hasClass('image-delete');
                $(this).html(deleted ? '&ntilde; excluir' : 'excluir')
                images.check();
                return false;
        }
}

/* * *
        FILES
* * */
var files = {
        count:1,

        initialize: function () {
                $('#file-add').click(this.add);
                if($('#files').get(0)){
                        $('#files .icon-delete').click(this.del);
                        this.check();
                }
        },

        add: function (event) {
                var tagname = $('#file-field')[0].tagName;
                if (files.count < 4) {
                        $('#file-fields').append('<' + tagname + '>' + $('#file-field').html().replace(/\*/g, '').replace(/(_insert)0/g, '$1' + this.count) + '</' + tagname + '>');
                }
                files.count++;
                return false;
        },

        check: function () {
                $('#files_delete').val('');
                $('#files li').each(function (i) {
                        var id = $(this).attr('id').match(/^(?:.*?)[_](.*)$/)[1];
                        if($(this).hasClass('file-delete')) {
                                $('#files_delete')[0].value += '|' + id;
                        }
                });
        },

        del: function () {
                var deleted = $(this).parent().parent().toggleClass('file-delete').hasClass('file-delete');
                $(this).html(deleted ? '&ntilde; excluir' : 'excluir')
                files.check();
                return false;
        }
}


/* * *
        DELETE REGISTRE
* * */
var delete_registre = {
        initialize: function () {
                $('.roll .icon-delete').click(function () {
                        return confirm('Tem certeza que deseja excluir este registro?');
                });

                $('#panel .btn-delete').click(function () {
                        return confirm('Tem certeza que deseja excluir todos os registro?\nEsta ação não é reversível.');
                });
        }
}

/* * *
        FIELDS
* * */
var fields = {
        fns:[],

        initialize: function () {
                $('#registre form').submit(this.check);
                $('#editMsg a').each(function(){
                        $('#'+this.title).addClass('field-error');
                });
        },

        add: function (fn) {
                fields.fns.push(fn);
        },

        check: function () {
                var msg = '';
                fields.wait(true);
                $(':input').removeClass('field-error');
                $('label').each(function(e){
                        var field = $('#' + $(this).attr('for')),
                                name = $(this).html().replace(/[*:]/g, '').replace(/\<.+/, '').toLowerCase(),
                                error = false;

                        if (field) {
                                /* EMPTY */
                                if ($(this).html().indexOf('*') > 0 && field.val().empty()) {
                                        msg += fields.error(field, error = 'O campo <strong>' + name + '</strong> deve ser preenchido');
                                /* EMAIL */
                                } else if( $(this).hasClass('email')  &&  !new RegExp("^([-!#\$%&'*+./0-9=?A-Z^_`a-z{|}~])+@([-!#\$%&'*+/0-9=?A-Z^_`a-z{|}~]+\\.)+[a-zA-Z]{2,6}$").test(field.val())) {
                                        msg += fields.error(field, error = 'O campo <strong>' + name + '</strong> deve conter um e-mail válido');
                                /* DATE */
                                } else if($(this).hasClass('date')) {
                                        var dt = field.val().match(/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/i) || [],
                                                dt2 = new Date(dt[3], dt[2]-1, dt[1]);
                                        if (
                                                dt[1] != dt2.getDate() ||
                                                dt[2] != dt2.getMonth() + 1 ||
                                                dt[3] != dt2.getFullYear()
                                        ) {
                                                msg += fields.error(field, error = 'O campo <strong>' + name + '</strong> deve conter uma data válida no formato 15/06/1988');
                                        }
                                }
                        }
                });
                // added
                $(fields.fns).each(function () {
                        msg += this();
                });

                if (msg) {
                        fields.wait(false);
                        $('#editMsg').html('<ul>' + msg + '</ul>').css('opacity', 0).fadeTo(150, 1);
                        $(window).resize();
                        return false;
                }
        },

        error: function (field, msg) {
                field.addClass('field-error');
                return '<li>' + msg + ' <a href="#" class="iGo" onclick="return fields.go(\'#' + field.attr('id') + '\')">| verificar</a></li>';
        },

        go: function (target) {
                target = $(target);
                $('#main').animate({ scrollTop: target.offset().top - 150}, 300, function () {
                        target.focus().parent().css({ opacity: .1 }).fadeTo(300, 1);
                });
                return false;
        },

        wait: function (status) {
                $('#main').get(0).scrollTop = 0;
                if (status) {
                        $('#editMsg').html('');
                        $('#wait').height($('#registre').height()-200).show();
                        $('#registre').hide();
                } else {
                        $('#registre').show();
                        $('#wait').hide();
                }
        }
}

/* * *
        TEXTAREA LIMIT
* * */
var textarea_limit = {
        initialize: function (element, length) {
                element = '#' + element;
                $(element + '-length').html(length);

                this.check(element, length);
                $(element).bind('keyup keydown', function () {
                        textarea_limit.check(element, length);
                });
        },
        check: function (element, length) {
                $(element + '-info').html($(element).val().length);
                $(element + '-info').css('color', $(element).val().length > length ? '#f00' : '#353535');
        }
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


$(function(){
        image_one.initialize();
        images.initialize();
        files.initialize();
        delete_registre.initialize();
        fields.initialize();

        var container = $('#container'),
                content = $('#content'),
                navigation = $('#navigation').show(),
                footer = $('#footer').show()

        $(window).resize(function () {
                var value;
                if (navigation.length) {
                        navigation.css({ marginTop: 0 });
                        value = Math.max($(window).height() - (content.outerHeight(true) + footer.outerHeight(true)), 40);
                        navigation.css({ marginTop: value });
                } else {
                        content.css({ paddingTop: 0, paddingBottom: 0 });
                        value = $(window).height() - (container.height() + footer.outerHeight(true));
                        value = Math.floor(value / 2) - 1;
                        content.css({ paddingTop: value, paddingBottom: value });
                }
        }).resize();

        /* MASK */
        $('#date').mask('99/99/9999').datepicker();
        $('#cpf').mask('999.999.999-99');
        $('#cnpj').mask('99.999.999/9999-99');
        $('#phone, #fax, #mobile').mask('(99) 9999-9999');
        $('#cep').mask('99999-999');
});