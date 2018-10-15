/**
 * Estatística dashboard
 */
function dashboard() {
    $.post('ajax/dashboard.php', {
            action: 'siteviews',
            key: 'dashboard'
        },
        function (data) {
            $('.siteviews_online').text(data.online);
            $('.siteviews_users b').text(data.users);
            $('.siteviews_views b').text(data.views);
            $('.siteviews_pages b').text(data.pages);
            $('.siteviews_stats').text(data.stats);
        }, 'json');
}

$(function () {
        "use strict"; // Start of use strict
        // Configure tooltips for collapsed side navigation
        $('.navbar-sidenav [data-toggle="tooltip"]').tooltip({
            template: '<div class="tooltip navbar-sidenav-tooltip" role="tooltip" style="pointer-events: none;"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
        })
        // Toggle the side navigation
        $("#sidenavToggler").click(function (e) {
            e.preventDefault();
            $("body").toggleClass("sidenav-toggled");
            $(".navbar-sidenav .nav-link-collapse").addClass("collapsed");
            $(".navbar-sidenav .sidenav-second-level, .navbar-sidenav .sidenav-third-level").removeClass("show");
        });
        // Force the toggled class to be removed when a collapsible nav link is clicked
        $(".navbar-sidenav .nav-link-collapse").click(function (e) {
            e.preventDefault();
            $("body").removeClass("sidenav-toggled");
        });
        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        $('body.fixed-nav .navbar-sidenav, body.fixed-nav .sidenav-toggler, body.fixed-nav .navbar-collapse').on('mousewheel DOMMouseScroll', function (e) {
            var e0 = e.originalEvent,
                delta = e0.wheelDelta || -e0.detail;
            this.scrollTop += (delta < 0 ? 1 : -1) * 30;
            e.preventDefault();
        });
        // Scroll to top button appear
        $(document).scroll(function () {
            var scrollDistance = $(this).scrollTop();
            if (scrollDistance > 100) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });
        // Configure tooltips globally
        $('[data-toggle="tooltip"]').tooltip()
        // Smooth scrolling using jQuery easing
        $(document).on('click', 'a.scroll-to-top', function (event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: ($($anchor.attr('href')).offset().top)
            }, 1000, 'easeInOutExpo');
            event.preventDefault();
        });

        /**
         * Abre um link externo em uma nova janela
         */
        $('.external').on('click', function (e) {
            e.preventDefault();
            window.open($(this).attr("href"));
        });

        /**
         * Carrega a imagem destacada
         */
        $('.load-image').change(function () {
            var input = $(this);
            var target = $('.' + input.attr('name'));

            if (!input.val()) {
                target.fadeOut('fast', function () {
                    $('.load-image-src').fadeOut('fast');
                });
                return false;
            }

            if (this.files && this.files[0].type.match('image.*')) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    target.fadeOut('fast', function () {
                        $(this).html('<img src="' + e.target.result + '" width="100%" height="100%" />').fadeIn('fast');
                    });
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                alert('Erro!</b> O arquivo <b>' + this.files[0].name + '</b> não é válido! <b>Selecione uma imagem JPG ou PNG!</b>');
                target.fadeOut('fast', function () {
                    $('.load-image-src').fadeOut('fast');
                });
                input.val('');
                return false;
            }
        });

        /**
         * Configurações de carregamento do TinyMCE
         */
        tinymce.init({
            selector: '.tiny-editor',
            height: 300,
            language: 'pt_BR',
            menubar: false,
            theme: 'modern',
            skin: 'light',
            entity_encoding: 'raw',
            theme_advanced_resizing: true,
            plugins: [
                'advlist autolink link image media lists charmap preview hr anchor spellchecker',
                'searchreplace wordcount code fullscreen',
                'table contextmenu directionality paste textcolor'
            ],
            toolbar: 'styleselect | bold | italic | bullist | numlist | alignleft | aligncenter | alignright | link | unlink | searchreplace | forecolor | backcolor | pastetext | removeformat | charmap | underline | strikethrough | image link | media | outdent | indent | table | code | preview | fullscreen ',

            style_formats: [
                {title: 'Normal', block: 'p'},
                {title: 'Titulo 3', block: 'h3'},
                {title: 'Titulo 4', block: 'h4'},
                {title: 'Titulo 5', block: 'h5'},
                {title: 'Código', block: 'pre', classes: 'brush: php;'}
            ],
            setup: function (editor) {
                editor.addButton('image link', {
                    title: 'Enviar Imagem',
                    icon: 'image',
                    onclick: function () {
                        $('.tiny-image-upload').fadeIn('fast');
                    }
                });
            },
            link_title: false,
            target_list: false,
            theme_advanced_blockformats: 'h1,h2,h3,h4,h5,p,pre',
            media_dimensions: false,
            media_poster: false,
            media_alt_source: false,
            media_embed: false,
            extended_valid_elements: 'a[href|target=_blank|rel]',
            imagemanager_insert_template: '<img src="{$url}" title="{$title}" alt="{$title}" />',
            image_dimensions: false,
            relative_urls: false,
            remove_script_host: false
        });

        /**
         * Coloca mascara no campo data
         */
        $('.date-time').mask('00/00/0000 00:00:00');

        /**
         * Envia o formulario por ajax
         */
        $('.form').not('.ajax-off').submit(function () {
            var form = $(this);
            var action = form.attr('action').split('/');

            form.ajaxSubmit({
                url: 'ajax/' + action[0] + '.php',
                data: {key: action[0], action: action[1], id: action[2]},
                dataType: 'json',
                beforeSubmit: function () {
                    $('.icon-load').fadeIn("fast");
                    $('.btn-load').addClass("disabled");
                    $('.alert').fadeOut('fast');
                },
                success: function (data) {
                    $('.btn-load').removeClass("disabled");
                    $('.icon-load').fadeOut("fast");
                    /** Verifica se o alert é fixo */
                    if (data.alert) {
                        var alert_fix = form.find('.alert');
                        if (alert_fix.length) {
                            alert_fix.html(data.alert);
                            $('.alert').fadeIn('slow');
                        } else {
                            notifications(data.alert);
                        }
                    }
                    /** Redireciona se receber o comando */
                    if (data.redirect) {
                        window.setTimeout(function () {
                            window.location.href = data.redirect.url;
                        }, data.redirect.timer || 5000);
                    }
                    /** TinyMCE */
                    if (data.tinyMCE) {
                        tinyMCE.activeEditor.insertContent(data.tinyMCE);
                        $('.ws-image-upload').fadeOut('slow', function () {
                            $('.ws-image-upload .image-default').attr('src', '../tim.php?src=uploads/no_image.jpg&w=500&h=300');
                        });
                    }
                    /** Adiciona as imagens adicionais */
                    if (data.images) {
                        $(data.images).appendTo($('form').find('.additional-images'));
                        $('img').fadeIn(400);
                    }

                    if (data.content) {
                        form.find('.j_content').fadeTo('300', '0.5', function () {
                            $(this).html(data.content).fadeTo('300', '1');
                        });
                    }
                    /** Limpa o input FILES */
                    if (!data.error) {
                        form.find('input[type="file"]').val('');
                    }
                }
            });
            return false;
        });

        /**
         * Deletar item (funciona para imóveis, páginas e slides)
         */
        $('.delete').on('click', function () {
            var id = $(this).data('id');
            var key = $(this).data('url');

            $('button.delete-confirm').click(function () {
                $('.icon-load').fadeIn("fast");
                $('.btn-load').addClass("disabled");
                $.post({
                    url: 'ajax/' + key + '.php',
                    data: {id: id, action: 'delete', key: key},
                    dataType: 'json',
                    success: function (data) {
                        if (data.alert[0] === 'success') {
                            $('.item-' + id).fadeOut('fast', function () {
                                $('.btn-load').removeClass("disabled");
                                $('.icon-load').fadeOut("fast");
                                $('#modal-delete-' + key).modal('toggle');
                                document.location.reload(true);
                            });
                        } else {
                            $('.icon-load').fadeOut("fast", function () {
                                $('.btn-load').removeClass("disabled");
                                $('.icon-load').fadeOut("fast");
                                $('#modal-delete-' + key).find('.alert-error').html(
                                    '<i class="fa fa-exclamation fa-fw"></i>Erro ao executar esta operação, tente novamente!'
                                );
                            });
                        }
                    }
                });
            });
        });

        /**
         * Deleta uma das imagens adicionais da galeria
         */
        $('.additional-images').on('click', 'img', function () {
            var img = $(this);
            var id = $(this).attr('id');
            var key = $('.form').attr('action').split('/')[0];
            var del = confirm('Deseja mesmo remover esta imagem ?');
            if (del === true) {
                $.post({
                    url: 'ajax/' + key + '.php',
                    data: {key: key, action: 'delete-image', id: id},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if (data.alert[0] === 'success') {
                            img.fadeOut('fast', function () {
                                img.remove();
                            });
                        } else {
                            notifications(data.alert);
                        }
                    }
                });
            }
        });

        /**
         * notifications: Executa as notificações
         * @param {array} data: Recebe os parametros por json
         * data[1]: Seta o estilo da notificação (opcional)
         * Por padrão, o estilo é alert
         */
        function notifications(data) {

            if (data[0]) {
                $.each(data, function (key, value) {
                    notify_alert(data[key]);
                })
            } else {
                notify_alert(data);
            }
        }

        /**
         * Monta o alerta de notificação
         * @param {array} data: Recebe os parâmetros
         * data[0]: Seta a mensagem de notificação
         * data[2]: Seta o tempo de exibição (opcional)
         * Por padrão o tempo é 5000
         */
        function notify_alert(data) {
            var msg = data.msg;

            if (!$(".alert-notify-box").length) {
                $("body").prepend("<div class='alert-notify-box'></div>");
            }

            $(".alert-notify-box").prepend(msg);
            $(".alert:first").stop().animate({"left": "0", "opacity": "1"}, 500, function () {
                $(this).find(".alert-notify-time").animate({"width": "100%"}, data.timer || 5000, "linear", function () {
                    $(this).parent(".alert").animate({"left": "100%", "opacity": "0"}, 500, function () {
                        $(this).remove();
                    });
                });
            });

            $("body").on('click', ".alert", function () {
                $(this).animate({"left": "100%", "opacity": "0"}, 500, function () {
                    $(this).remove();
                });
            });
        }
    }
); // End of use strict
