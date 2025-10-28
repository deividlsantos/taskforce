// JQUERY INIT

$(function () {
    var ajaxResponseBaseTime = 7;
    var ajaxResponseRequestError = "<div class='message error icon-warning'>Desculpe mas não foi possível processar sua requisição...</div>";

    // MOBILE MENU

    $(".mobile_menu").click(function (e) {
        e.preventDefault();

        var menu = $(".dash_sidebar");
        menu.animate({ right: 0 }, 200, function (e) {
            $("body").css("overflow", "hidden");
        });

        menu.one("mouseleave", function () {
            $(this).animate({ right: '-260' }, 200, function (e) {
                $("body").css("overflow", "auto");
            });
        });
    });

    //NOTIFICATION CENTER

    $(".notification_center_open").click(function (e) {
        e.preventDefault();

        var center = $(".notification_center");

        center.css("display", "block").animate({ right: 0 }, 200, function (e) {
            $("body").css("overflow", "hidden");
        });

        center.one("mouseleave", function () {
            $(this).animate({ right: '-320' }, 200, function (e) {
                $("body").css("overflow", "auto");
                $(this).css("display", "none");
            });
        });
    });

    //DATA SET

    $("[data-post]").click(function (e) {
        e.preventDefault();

        var clicked = $(this);
        var data = clicked.data();
        var load = $(".ajax_load");

        if (data.confirm) {
            var deleteConfirm = confirm(data.confirm);
            if (!deleteConfirm) {
                return;
            }
        }

        $.ajax({
            url: data.post,
            type: "POST",
            data: data,
            dataType: "json",
            beforeSend: function () {
                load.fadeIn(200).css("display", "flex");
            },
            success: function (response) {
                //redirect
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    load.fadeOut(200);
                }

                //reload
                if (response.reload) {
                    window.location.reload();
                } else {
                    load.fadeOut(200);
                }

                //message
                if (response.message) {
                    ajaxMessage(response.message, ajaxResponseBaseTime);
                }

                //delete
                if (response.apagado) {
                    ajaxMessage(response.mensagem, ajaxResponseBaseTime);
                    var grid = $(response.grid);
                    grid.find('a[data-id="' + response.id + '"]').closest('tr').remove();
                    grid.trigger('update');
                }
            },
            error: function () {
                ajaxMessage(ajaxResponseRequestError, 5);
                load.fadeOut();
            }
        });
    });

    //FORMS

    $("form:not('.ajax_off')").submit(function (e) {
        e.preventDefault();

        var form = $(this);
        var load = $(".ajax_load");

        if (typeof tinyMCE !== 'undefined') {
            tinyMCE.triggerSave();
        }

        form.ajaxSubmit({
            url: form.attr("action"),
            type: "POST",
            dataType: "json",
            beforeSend: function () {
                load.fadeIn(200).css("display", "flex");
            },
            uploadProgress: function (event, position, total, completed) {
                var loaded = completed;
                var load_title = $(".ajax_load_box_title");
                load_title.text("Enviando (" + loaded + "%)");

                if (completed >= 100) {
                    load_title.text("Aguarde, carregando...");
                }
            },
            success: function (response) {
                //redirect
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    form.find("input[type='file']").val(null);
                    load.fadeOut(200);
                }

                //reload
                if (response.reload) {
                    window.location.reload();
                } else {
                    load.fadeOut(200);
                }

                //message
                if (response.message) {
                    ajaxMessage(response.message, ajaxResponseBaseTime);
                }

                //image by fsphp mce upload
                if (response.mce_image) {
                    $('.mce_upload').fadeOut(200);
                    tinyMCE.activeEditor.insertContent(response.mce_image);
                }
            },
            complete: function () {
                if (form.data("reset") === true) {
                    form.trigger("reset");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error:', textStatus, errorThrown);
                ajaxMessage(ajaxResponseRequestError, 5);
                load.fadeOut();
            }
        });
    });

    /*
     * IMAGE RENDER
     */
    $("[data-image]").change(function (e) {
        var changed = $(this);
        var file = this;

        if (file.files && file.files[0]) {
            var render = new FileReader();

            render.onload = function (e) {
                $(changed.data("image")).fadeTo(100, 0.1, function () {
                    $(this).css("background-image", "url('" + e.target.result + "')")
                        .fadeTo(100, 1);
                });
            };
            render.readAsDataURL(file.files[0]);
        }
    });

    // AJAX RESPONSE

    function ajaxMessage(message, time) {
        console.log(message);
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({ "width": "100%" }, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
        ajaxMessage.effect("bounce");
    }

    // AJAX RESPONSE MONITOR

    $(".ajax_response .message").each(function (e, m) {
        ajaxMessage(m, ajaxResponseBaseTime += 1);
    });

    // AJAX MESSAGE CLOSE ON CLICK

    $(".ajax_response").on("click", ".message", function (e) {
        $(this).effect("bounce").fadeOut(1);
    });
});