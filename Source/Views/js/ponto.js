$(function () {

    var ajaxResponseBaseTime = 7;
    var ajaxResponseRequestError = "<div class='message error icon-warning'>Desculpe mas não foi possível processar sua requisição...</div>";

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

    function buscarCli() {
        var form = $("#form-pesq");
        $.ajax({
            url: form.attr("data-action"),
            method: "POST",
            data: form.serialize(),
            success: function (response) {
                var messageWarning = 'message warning icon-warning';
                var messageError = 'message error icon-warning';
                var messageSuccess = 'message success icon-warning';
                var messageInfo = 'message info icon-warning';
                if (response == true) {
                    if ($('.result-line').is(':visible')) {
                        $('.result-line').hide()
                    };
                    if ($('.empty-line').is(':hidden')) {
                        $('.empty-line').show()
                    };
                    $('.empty-line').text("Nenhum registro encontrado");
                } else {
                    $('.result').html('<div></div>');
                    $('.result-line').removeAttr("hidden");
                    $('.empty-line').hide();
                    if (response.includes(messageWarning) || response.includes(messageError) || response.includes(messageSuccess) || response.includes(messageInfo)) {
                        ajaxMessage(response, ajaxResponseBaseTime);
                    } else {
                        $('.result').html(response);
                        $('.result-line').removeAttr("hidden");
                        $('.empty-line').hide();
                    }

                }
                //message

            }

        });
    }

    function gerarPontos(link) {
        var form = $("#app_form_ponto");
        $.ajax({
            url: form.attr("data-action"),
            method: "POST",
            data: form.serialize(),
            success: function (response) {
                var messageWarning = 'message warning icon-warning';
                var messageError = 'message error icon-warning';
                var messageSuccess = 'message success icon-check-square-o';
                var messageInfo = 'message info icon-info';
                var messageDone = 'PONTO REGISTRADO COM SUCESSO';
                if (response.includes(messageWarning) || response.includes(messageError) || response.includes(messageSuccess) || response.includes(messageInfo)) {
                    ajaxMessage(response, ajaxResponseBaseTime);
                }

                if (response.includes(messageDone)) {
                    // Esconde todos os elementos do formulário
                    $("form").hide();

                    $("#visualizar-link").attr("href", link);
                    console.log()

                    // Mostra a mensagem de sucesso e os links
                    $("#success-message-container").show();

                }
            }
        });
    }

    function verPontos() {
        var form = $("#folhas_form_ponto");
        $.ajax({
            url: form.attr("data-action"),
            method: "POST",
            data: form.serialize(),
            success: function (response) {
                var messageWarning = 'message warning icon-warning';
                var messageError = 'message error icon-warning';
                var messageSuccess = 'message success icon-check-square-o';
                var messageInfo = 'message info icon-info';
                var messageDone = 'ponto\/folhas';
                if (response.includes(messageWarning) || response.includes(messageError) || response.includes(messageSuccess) || response.includes(messageInfo)) {
                    ajaxMessage(response, ajaxResponseBaseTime);
                }
                if (response.includes(messageDone)) {
                    window.location.href = response;
                } else {
                    load.fadeOut(200);
                }
            }

        });
    }


    function coletarDados() {
        var dados = [];

        var ponto1Data = {
            id_ponto1: $('#id_ponto1').text().trim(),
            total_ponto1: $('#total-geral').text().trim(),
            extras_ponto1: $('#extras-geral').text().trim(),
            banco_ponto1: $('#banco-geral').text().trim()
        };

        dados.push(ponto1Data);

        $('.tab-folha tbody tr').each(function () {
            var $row = $(this);
            var rowData = {
                id_ponto2: $row.find('td:nth-child(1)').text().trim(),
                dia: $row.find('td:nth-child(2)').text().trim(),
                hora_ini: $row.find('td:nth-child(3)').text().trim(),
                intervalo_ini: $row.find('td:nth-child(4)').text().trim(),
                intervalo_fim: $row.find('td:nth-child(5)').text().trim(),
                hora_fim: $row.find('td:nth-child(6)').text().trim(),
                total_ponto2: $row.find('td:nth-child(7)').text().trim(),
                banco_ponto2: $row.find('td:nth-child(8)').text().trim(),
                extras_ponto2: $row.find('td:nth-child(9)').text().trim(),
                obs: $row.find('td:nth-child(10) select').val(),
                checkbox: $row.find('td:nth-child(11) input.edit-free').is(':checked') ? 1 : 0
            };
            dados.push(rowData);
        });

        return dados;
    }


    $(document).ready(function () {

        $('.ferias-info').on('mouseenter', function (event) {
            let tooltipText = $(this).attr('data-tooltip');
            tooltipText = tooltipText.replace(/\n/g, '<br>'); // Substitui \n por <br>
            const tooltipDiv = $('<div class="tooltip-text"></div>').html(tooltipText); // Usa html() em vez de text()

            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            tooltipDiv.css({
                top: (rect.top - tooltipDiv.outerHeight() - 5) + 'px',
                left: (rect.left + (rect.width / 2) - (tooltipDiv.outerWidth() / 2)) + 'px',
                opacity: 1,
                visibility: 'visible'
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $('.ferias-info').on('mouseleave', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });

        function setActiveButton(activeUrl) {
            $('.ponto-card-link').each(function () {
                var $link = $(this);
                var href = $link.attr('href');

                if (href === activeUrl) {
                    $link.find('.ponto-card').addClass('active');
                } else {
                    $link.find('.ponto-card').removeClass('active');
                }
            });
        }

        // Call the function with the current URL
        setActiveButton(window.location.href);


        // Evento de mudança para o checkbox pai
        $("#select-all").change(function () {
            var isChecked = $(this).is(":checked");
            $(".app_func-item input[type='checkbox']").each(function () {
                if (!$(this).is(":disabled")) { // Ignora checkboxes desabilitados
                    $(this).prop("checked", isChecked);
                    var $input = $(this).closest(".app_func-item").find(".date-range");
                    $input.prop("disabled", !isChecked); // Habilita/desabilita o input date-range
                    if (!isChecked) {
                        $input.val(''); // Limpa o valor se desabilitado
                    }
                }
            });
        });

        // Evento de mudança para os checkboxes individuais
        $(".app_func-item input[type='checkbox']").change(function () {
            var allChecked = $(".app_func-item input[type='checkbox']").not(":disabled").length === $(".app_func-item input[type='checkbox']:checked").not(":disabled").length;
            $("#select-all").prop("checked", allChecked);

            var $input = $(this).closest(".app_func-item").find(".date-range");
            if ($(this).is(":checked")) {
                $input.prop("disabled", false);
            } else {
                $input.prop("disabled", true).val(''); // Desabilita e limpa o valor
            }
        });


        // Evento de mudança para os selects de competência
        $("#mes, #ano").change(function () {
            // Desmarca o checkbox pai
            $("#select-all").prop("checked", false);

            if ($("#mes").val() !== "" && $("#ano").val() !== "") {
                var mesSelecionado = $("#mes").val();
                var anoSelecionado = $("#ano").val();
                var url = $("#app_mes_ano").data("action");

                // Limpa qualquer texto "(Ponto já gerado)" existente antes de fazer a requisição AJAX
                $(".app_func-item").each(function () {
                    var label = $(this).find("label");
                    label.html(label.html().replace(/<span class="ponto-verificado">.*<\/span>/, ""));
                    $(this).find("input[type='checkbox']").prop("disabled", false); // Habilita todos os checkboxes
                });

                $.ajax({
                    url: url, // Crie um endpoint para verificar os pontos
                    method: 'POST',
                    data: { mes: mesSelecionado, ano: anoSelecionado },
                    success: function (response) {
                        var pontosGerados = JSON.parse(response);
                        $(".app_func-item").each(function () {
                            var funcId = $(this).find("input[type='checkbox']").val();
                            if (pontosGerados[funcId]) {
                                var label = $(this).find("label");
                                label.append('<span class="ponto-verificado"> (Ponto já gerado)</span>');
                                $(this).find("input[type='checkbox']").prop("disabled", true); // Desabilita o checkbox
                            }
                        });
                    }
                });
                $(".app_func_container").show();
            } else {
                $(".app_func_container").hide();
            }
        });

        // Desabilita todos os inputs de intervalo de data por padrão
        $(".date-range").prop("disabled", true);

        // Habilita ou desabilita o input de intervalo de data com a mudança do checkbox
        $(".app_func-item input[type='checkbox']").on("change", function () {
            var $input = $(this).closest(".app_func-item").find(".date-range");
            if ($(this).is(":checked")) {
                $input.prop("disabled", false);
            } else {
                $input.prop("disabled", true).val(''); // Desabilita e limpa o valor
            }
        });

        $("#form-pesq").submit(function (e) {
            e.preventDefault();
            buscarCli();
        });

        $("#app_form_ponto").submit(function (e) {
            e.preventDefault();
            var mes = $("#mes option:selected").data('value');
            var ano = $("#ano option:selected").data('value');
            var url = $("#visualizar-link").data('link');
            var link = url + mes + "/" + ano;
            gerarPontos(link);
        });

        $("#folhas_form_ponto").submit(function (e) {
            e.preventDefault();
            verPontos();
        });

        // Enviar dados para o servidor
        $('#save-data').click(function () {
            var dados = coletarDados();
            var url = $(this).data('action');

            $.ajax({
                url: url,
                method: 'POST',
                data: { tabelaDados: JSON.stringify(dados) },
                success: function (response) {
                    var messageWarning = 'message warning icon-warning';
                    var messageError = 'message error icon-warning';
                    var messageSuccess = 'message success icon-check-square-o';
                    var messageInfo = 'message info icon-info';
                    var messageDone = 'ponto\/folhas';
                    if (response.includes(messageWarning) || response.includes(messageError) || response.includes(messageSuccess) || response.includes(messageInfo)) {
                        ajaxMessage(response, ajaxResponseBaseTime);
                    }
                    if (response.includes(messageDone)) {
                        window.location.href = response;
                    }

                },
                error: function () {
                    alert('Erro ao salvar dados.');
                }
            });
        });

        // Define o filtro padrão como "todos" ao carregar a página
        $("#arq-tab").data("filter", "todos");

        // Aplica a filtragem inicial
        filterTable();

        // Função de filtragem por categoria
        $(".filter-btn").on("click", function () {
            // Remove a classe active de todos os doc-card
            $(".doc-card").removeClass("active");

            // Adiciona a classe active ao doc-card do botão clicado
            $(this).find(".doc-card").addClass("active");

            // Obtém o valor do filtro selecionado
            var filterValue = $(this).data("filter");

            // Atualiza o atributo data-filter na tabela para a filtragem por texto
            $("#arq-tab").data("filter", filterValue);

            // Aplica o filtro na tabela
            filterTable();

            // Obtém o título do botão clicado e atualiza o título exibido
            var selectedTitle = $(this).attr("title");
            $("#titulo-filtrado").text(selectedTitle);
        });

        // Função de filtragem por texto
        $("#filtrar-arq").on("input", function () {
            filterTable();
        });

        // Função para filtrar a tabela
        function filterTable() {
            // Obtém o valor do filtro de categoria atual
            var categoryFilter = $("#arq-tab").data("filter");

            // Obtém o valor do texto de filtragem
            var searchValue = ($("#filtrar-arq").val() || "").toLowerCase();

            var rows = $("#arq-tab tbody tr");
            var hasVisibleRows = false;

            rows.each(function () {
                var row = $(this);
                var rowCategory = row.find("td[data-cat]").data("cat");
                var rowText = row.text().toLowerCase();

                var matchesCategory = (categoryFilter === "todos" ||
                    (categoryFilter === "empresa" && !rowCategory) ||
                    categoryFilter == rowCategory);

                var matchesText = rowText.indexOf(searchValue) > -1;

                if (matchesCategory && matchesText) {
                    row.show();
                    hasVisibleRows = true; // Indica que existe pelo menos uma linha visível
                } else {
                    row.hide();
                }
            });

            // Exibe uma mensagem se não houver linhas visíveis
            if (!hasVisibleRows) {
                $("#arq-tab tbody").append('<tr class="no-results"><td colspan="6">Nenhum resultado encontrado</td></tr>');
            } else {
                // Remove a mensagem de "nenhum resultado" se houver resultados
                $("#arq-tab tbody .no-results").remove();
            }
        }


    });

    $('.phone').on('input', function () {
        var $input = $(this);
        var value = $input.val().replace(/\D/g, ''); // Remove todos os caracteres não numéricos
        var length = value.length;

        // Remove as classes existentes
        $input.removeClass('mask-phone mask-celphone');

        // Aplica a formatação e a classe correta
        if (length === 8) {
            $input.addClass('mask-phone');
            $input.val(value.replace(/(\d{4})(\d{4})/, '$1-$2'));
        } else if (length === 9) {
            $input.addClass('mask-celphone');
            $input.val(value.replace(/(\d{5})(\d{4})/, '$1-$2'));
        } else if (length === 10) {
            $input.addClass('mask-phone');
            $input.val(value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3'));
        } else if (length === 11) {
            $input.addClass('mask-celphone');
            $input.val(value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3'));
        }


    });
});