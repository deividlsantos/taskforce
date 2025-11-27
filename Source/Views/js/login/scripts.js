// JQUERY INIT

$(function () {

    function backdropModal(modal, baseZIndex = 1050) {
        $(modal).on('show.bs.modal', function () {
            // Conta quantas modais estão abertas no momento
            const openModals = $('.modal.show').length;

            // Calcula o z-index da nova modal e do backdrop com base na pilha
            const backdropZ = baseZIndex + openModals * 20;
            const modalZ = backdropZ + 5;

            // Cria e insere um novo backdrop com z-index ajustado
            const $backdrop = $('<div>')
                .addClass('modal-backdrop fade show')
                .css('z-index', backdropZ);

            $('body').append($backdrop);

            // Ajusta o z-index da modal para ficar acima do backdrop
            $(this).css('z-index', modalZ);
        });

        $(modal).on('hidden.bs.modal', function () {
            const $backdrops = $('.modal-backdrop');
            if ($backdrops.length > 0) {
                $backdrops.last().remove();
            }

            // Limpa o z-index para o padrão
            $(this).css('z-index', '');
        });
    }


    function initAutoNumeric(classe, casas = 2) {
        $(classe).each(function () {
            const existingInstance = AutoNumeric.getAutoNumericElement(this);
            if (existingInstance) {
                existingInstance.remove(); // evita bug no mouseleave
            }

            new AutoNumeric(this, {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: casas,
                minimumValue: '0',
                modifyValueOnWheel: false,
            });
        });
    }

    initAutoNumeric('.num-decimal2');
    initAutoNumeric('.num-decimal3', 3);

    function calculaTotalLinhaMaterial() {
        $('.linha-material').each(function () {
            const linha = $(this);
            const qtdEl = linha.find('.qtde_material')[0];
            const valorEl = linha.find('.vunit_material')[0];
            const totalEl = linha.find('.vtotal_material')[0];

            // Só calcula se os elementos foram inicializados com AutoNumeric
            const anQtd = AutoNumeric.getAutoNumericElement(qtdEl);
            const anVal = AutoNumeric.getAutoNumericElement(valorEl);
            const anTot = AutoNumeric.getAutoNumericElement(totalEl);

            if (anQtd && anVal && anTot) {
                const qtd = anQtd.getNumber();
                const val = anVal.getNumber();
                anTot.set(qtd * val);
            }
        });
        somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
    }

    function formatarDataHoraBr(dataHora) {
        if (!dataHora) return ''; // Retorna vazio se não houver valor

        // Cria objeto Date a partir da string recebida
        var data = new Date(dataHora);

        if (isNaN(data.getTime())) return ''; // Retorna vazio se a data for inválida

        var dia = String(data.getDate()).padStart(2, '0');
        var mes = String(data.getMonth() + 1).padStart(2, '0');
        var ano = String(data.getFullYear()).slice(-2); // pega só os dois últimos dígitos
        var hora = String(data.getHours()).padStart(2, '0');
        var minuto = String(data.getMinutes()).padStart(2, '0');

        return dia + '/' + mes + '/' + ano + ' ' + hora + ':' + minuto;
    }

    function formatarDataHoraIso(dataHoraBr) {
        if (!dataHoraBr) return ''; // Retorna vazio se não houver valor

        // dataHoraBr esperado no formato "dd/mm/aa hh:mm"
        var partes = dataHoraBr.split(' ');
        if (partes.length < 2) return '';

        var data = partes[0].split('/');
        var hora = partes[1].split(':');

        if (data.length < 3 || hora.length < 2) return '';

        var dia = data[0];
        var mes = data[1];
        var ano = data[2].length === 2 ? '20' + data[2] : data[2]; // garante 4 dígitos

        var hh = hora[0];
        var mm = hora[1];

        // Formato válido para datetime-local → "2024-11-01T14:35"
        return ano + '-' + mes.padStart(2, '0') + '-' + dia.padStart(2, '0') +
            'T' + hh.padStart(2, '0') + ':' + mm.padStart(2, '0');
    }

    function formatarDataBr(data) {
        if (!data) return ''; // Retorna string vazia se a data for nula ou indefinida
        var partesData = data.split('-');
        return partesData[2] + '/' + partesData[1] + '/' + partesData[0]; // '01/11/2024'
    }

    function formatarDataIso(data) {
        if (!data) return ''; // Retorna string vazia se a data for nula ou indefinida
        var partesData = data.split('/');
        return partesData[2] + '-' + partesData[1] + '-' + partesData[0]; // '2024-11-01'
    }

    $.tablesorter.addParser({
        id: 'number',
        is: function (s) {
            return false;
        },
        format: function (s) {
            return parseFloat(s.replace(/\./g, '').replace(',', '.'));
        },
        type: 'numeric'
    });

    $.tablesorter.addParser({
        id: 'datetimeBR',
        is: function (s) {
            return /\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}/.test(s) || s.trim() === "";
        },
        format: function (s) {
            if (s.trim() === "") {
                return ""; // deixa string vazia
            }
            var parts = s.split(' ');
            var datePart = parts[0].split('/');
            var timePart = parts[1].split(':');
            return new Date(datePart[2], datePart[1] - 1, datePart[0], timePart[0], timePart[1]).getTime();
        },
        type: 'numeric'
    });

    $.tablesorter.addParser({
        id: 'dateBR',
        is: function (s) {
            return /^\d{2}\/\d{2}\/\d{4}$/.test(s.trim()) || s.trim() === "";
        },
        format: function (s) {
            if (s.trim() === "") {
                return Infinity; // Faz com que os valores vazios fiquem no final da ordenação
            }
            var datePart = s.split('/');
            return new Date(datePart[2], datePart[1] - 1, datePart[0]).getTime();
        },
        type: 'numeric'
    });

    $.tablesorter.addParser({
        id: 'data-status',
        is: function () {
            return false; // só será usado quando definido manualmente
        },
        format: function (s, table, cell) {
            // Lê o valor numérico diretamente do atributo data-status
            const val = parseInt($(cell).attr('data-status'), 10);
            return isNaN(val) ? 9999 : val; // fallback alto caso o valor não seja número
        },
        type: 'numeric'
    });

    function limitarTexto(texto, limite = 40, sufixo = '...') {
        return texto.length > limite ? texto.substring(0, limite) + sufixo : texto;
    }

    function tableSorter(tabela, options, btnPdf) {
        $(tabela).tablesorter($.extend({
            headers: {}
        }, options))
            .bind("sortEnd", function () {
                // Atualiza as classes dos headers para asc/desc visuais
                $(this).find("th").removeClass("asc desc");
                $(this).find("th").each(function () {
                    if ($(this).hasClass("tablesorter-headerAsc")) {
                        $(this).addClass("asc");
                    } else if ($(this).hasClass("tablesorter-headerDesc")) {
                        $(this).addClass("desc");
                    }
                });

                // Atualiza o botão PDF após a ordenação
                if (btnPdf) {
                    atualizarDataIdsPdf(btnPdf, tabela);
                }
            });

        // Também atualiza a ordem no carregamento inicial
        if (btnPdf) {
            atualizarDataIdsPdf(btnPdf, tabela);
        }
    }

    function sortToggle(btn, input) {
        $(btn).on("click", function () {
            const sortInput = $(input);
            const currentValue = sortInput.val();
            const newValue = currentValue === "asc" ? "desc" : "asc";
            sortInput.val(newValue);

            // Atualiza o título do botão
            const button = $(this);
            if (newValue === "asc") {
                button.attr("title", "CRESCENTE");
                button.find("i").attr("class", "fa fa-arrow-down-a-z");

                // Substitui apenas a palavra DECRESCENTE por CRESCENTE, mantendo a tag <i>
                let buttonHtml = button.html();
                if (buttonHtml.includes('DECRE')) {
                    buttonHtml = buttonHtml.replace('DECRE', 'CRESC');
                    button.html(buttonHtml);
                }
            } else {
                button.attr("title", "DECRESCENTE");
                button.find("i").attr("class", "fa fa-arrow-up-z-a");

                // Substitui apenas a palavra CRE por DECRE, mantendo a tag <i>
                let buttonHtml = button.html();
                if (buttonHtml.includes('CRESC')) {
                    buttonHtml = buttonHtml.replace('CRESC', 'DECRE');
                    button.html(buttonHtml);
                }
            }
        });
    }

    sortToggle("#toggle-sort1", "#os2rel-order1-sort");
    sortToggle("#toggle-sort2", "#os2rel-order2-sort");
    sortToggle("#toggle-sort3", "#os2rel-order3-sort");
    sortToggle("#toggle-sort1", "#servicosrel-order1-sort");
    sortToggle("#toggle-sort2", "#servicosrel-order2-sort");
    sortToggle("#toggle-sort3", "#servicosrel-order3-sort");

    sortToggle("#toggle-sort-oslist", "#oslist-order1-sort");

    //** FUNÇÃO PARA GERAR GRID DA TELA DE RELATÓRIOS OS2 */
    function preencherTabelaOs2(dados) {
        const $tbody = $("#os2rel-list tbody");
        $tbody.empty(); // Limpa o conteúdo atual da tabela

        if (!dados.length) {
            $tbody.append("<tr><td colspan='8'>Nenhum dado encontrado</td></tr>");
            return;
        }

        dados.forEach(item => {
            const hoje = new Date().toISOString().split('T')[0];

            // Define a classe com base no status
            let classeStatus = '';
            if (item.status === 'CONCLUÍDO') {
                classeStatus = 'status-concluido'; // verde
            } else if (item.status === 'CANCELADO') {
                classeStatus = 'status-cancelado'; // roxo
            }

            // Aplica cor vermelha apenas se não for C nem D e a data estiver vencida
            const vencido = item.dataexec < hoje && !classeStatus ? "style='color:red;'" : '';

            const linha = $(`<tr data-status="${item.status}" class="linha ${classeStatus}" ${vencido}></tr>`);

            // Colunas: OS, Serviço, Data de Execução, Valor, Status, Pdf1, Pdf2
            linha.append(`<td hidden>${item.id}</td>`);
            linha.append(`<td>${item.id_os1}</td>`);
            linha.append(`<td>${item.id}</td>`);
            linha.append(`<td>${limitarTexto(item.servico)}</td>`);
            linha.append(`<td>${item.colaborador}</td>`);
            linha.append(`<td>${formatarDataBr(item.dataexec)}</td>`);
            linha.append(`<td>${parseFloat(item.vtotal).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td>${item.status}</td>`);

            const dataConcluidos = $('#os2rel-concluidas').is(':checked') ? '1' : '0';
            const dataCancelados = $('#os2rel-canceladas').is(':checked') ? '1' : '0';

            $tbody.append(linha);
        });
    }

    //** FUNÇÃO PARA GERAR GRID DA TELA DE RELATÓRIOS OS2 */
    function preencherTabelaServicos(dados) {
        const $tbody = $("#servicosrel-list tbody");
        $tbody.empty(); // Limpa o conteúdo atual da tabela

        if (!dados.length) {
            $tbody.append("<tr><td colspan='8'>Nenhum dado encontrado</td></tr>");
            return;
        }

        dados.forEach(item => {
            const hoje = new Date().toISOString().split('T')[0];

            // Define a classe com base no status
            let classeStatus = '';
            if (item.status === 'CONCLUÍDO') {
                classeStatus = 'status-concluido'; // verde
            } else if (item.status === 'CANCELADO') {
                classeStatus = 'status-cancelado'; // roxo
            }

            // Aplica cor vermelha apenas se não for C nem D e a data estiver vencida
            const vencido = item.dataexec < hoje && !classeStatus ? "style='color:red;'" : '';

            const linha = $(`<tr data-status="${item.status}" class="linha ${classeStatus}" ${vencido}></tr>`);

            // Colunas: OS, Serviço, Data de Execução, Valor, Status, Pdf1, Pdf2
            linha.append(`<td hidden>${item.id}</td>`);
            linha.append(`<td>${item.id_os1}</td>`);
            linha.append(`<td>${item.os1controle}</td>`);
            linha.append(`<td>${formatarDataBr(item.dataexec)}</td>`);
            linha.append(`<td>${item.segmento_controle}</td>`);
            linha.append(`<td>${limitarTexto(item.segmento_nome)}</td>`);
            linha.append(`<td>${limitarTexto(item.servico)}</td>`);
            linha.append(`<td style="text-align: right;">${item.qtde}</td>`);
            linha.append(`<td style="text-align: right;">${parseFloat(item.vunit).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="text-align: right; padding-right: 10px;">${parseFloat(item.vtotal).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="margin-left: 5px;">${item.status}</td>`);

            const dataConcluidos = $('#servicosrel-concluidas').is(':checked') ? '1' : '0';
            const dataCancelados = $('#servicosrel-canceladas').is(':checked') ? '1' : '0';

            $tbody.append(linha);
        });
    }

    //**FUNÇÃO PARA PREENCHER TABELA DE RELATÓRIO FINANCEIRO **//
    function preencherTabelaFinanceiro(dados) {
        const $tbody = $("#financeirorel-list tbody");
        $tbody.empty(); // Limpa o conteúdo atual da tabela

        if (!dados.length) {
            $tbody.append("<tr><td colspan='10'>Nenhum dado encontrado</td></tr>");
            return;
        }

        dados.forEach(item => {
            const hoje = new Date().toISOString().split('T')[0];

            const linha = $(`<tr></tr>`);

            // Colunas: OS, Serviço, Data de Execução, Valor, Status, Pdf1, Pdf2
            linha.append(`<td hidden>${item.id}</td>`);
            linha.append(`<td>${item.entidade_nome}</td>`);
            linha.append(`<td>${item.id_oper}</td>`);
            linha.append(`<td>${item.titulo}</td>`);
            linha.append(`<td>${item.datacad}</td>`);
            linha.append(`<td>${item.dataven}</td>`);
            linha.append(`<td>${item.databaixa}</td>`);
            linha.append(`<td style="text-align: right;">${(parseFloat(item.valor) || 0).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="text-align: right;">${(parseFloat(item.vdesc) || 0).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="text-align: right;">${(parseFloat(item.voutros) || 0).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="text-align: right;">${(parseFloat(item.vpago) || 0).toFixed(2).replace('.', ',')}</td>`);
            linha.append(`<td style="text-align: right;">${(parseFloat(item.saldo) || 0).toFixed(2).replace('.', ',')}</td>`);

            $tbody.append(linha);
        });
    }

    function criarPaginacao(total, paginaAtual, limit, form) {
        const $paginacao = $("#paginacao");
        $paginacao.empty();

        const totalPaginas = Math.ceil(total / limit);
        if (totalPaginas <= 1) return;

        const range = 2;
        let inicio = Math.max(1, paginaAtual - range);
        let fim = Math.min(totalPaginas, paginaAtual + range);

        // Botão de primeira página «
        if (paginaAtual > 1) {
            const primeiro = $('<button class="btn btn-sm btn-light mx-1">«</button>');
            primeiro.on("click", () => buscarPagina(1, form));
            $paginacao.append(primeiro);
        }

        // Botões numerados
        for (let i = inicio; i <= fim; i++) {
            const isActive = i === Number(paginaAtual);
            const botao = $(`<button class="btn btn-sm mx-1 ${isActive ? 'btn-danger' : 'btn-light'}">${i}</button>`);
            botao.on("click", () => buscarPagina(i, form));
            $paginacao.append(botao);
        }

        // Botão de última página »
        if (paginaAtual < totalPaginas) {
            const ultimo = $('<button class="btn btn-sm btn-light mx-1">»</button>');
            ultimo.on("click", () => buscarPagina(totalPaginas, form));
            $paginacao.append(ultimo);
        }

        $("#active-page").empty().text(`Página ${paginaAtual} de ${totalPaginas}`);
    }

    function armazenarFiltrosPaginacao(paginacao) {
        $("#filtros-paginacao").text(JSON.stringify(paginacao));
    }

    /**
     * 
     * @param {*} pagina 
     * @param {*} form - recebe apenas o prefixo do id - ex: os2rel para #os2rel-form
     */
    function buscarPagina(pagina, form) {
        const filtrosJson = $("#filtros-paginacao").text();
        const dados = JSON.parse(filtrosJson);
        const url = $('#' + form + '-form').attr('action');
        dados.page = pagina;

        $.ajax({
            url: url,
            method: "POST",
            data: dados,
            dataType: "json",
            success: function (response) {
                if (response.os2rel) {
                    preencherTabelaOs2(response.registros);

                    // Atualiza paginação
                    if (response.total && response.paginacao) {
                        criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, "os2rel");

                        // Atualiza os filtros armazenados com a nova página
                        armazenarFiltrosPaginacao(response.paginacao);
                    }
                }

                if (response.servicosrel) {
                    preencherTabelaServicos(response.registros);

                    // Atualiza paginação
                    if (response.total && response.paginacao) {
                        criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, "servicosrel");

                        // Atualiza os filtros armazenados com a nova página
                        armazenarFiltrosPaginacao(response.paginacao);
                    }
                }

                if (response.financeirorel) {
                    preencherTabelaFinanceiro(response.registros);

                    // Atualiza paginação
                    if (response.total && response.paginacao) {
                        criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, response.paginacao.form);

                        // Atualiza os filtros armazenados com a nova página
                        armazenarFiltrosPaginacao(response.paginacao);
                    }
                }

                if (response.baixaslist) {
                    $("#tableBaixasList tbody").empty();
                    preencherTabelaBaixas(response.registros);
                    $("#baixas-totalregistros").empty().text(`Total de Registros: ${response.total}`);

                    // Atualiza paginação
                    if (response.total && response.paginacao) {
                        criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, "baixas-list");

                        // Atualiza os filtros armazenados com a nova página
                        armazenarFiltrosPaginacao(response.paginacao);

                        // Atualiza o hidden #page do form
                        $("#baixas-list-form input[name='page']").val(response.paginacao.page);
                    }
                }
            }
        });
    }

    function toggleStatusRelatorios(relatorio) {
        $(document).on('change', relatorio + '-status', function (e) {
            var status = $(this).val();
            var $chkConcluidas = $(relatorio + '-concluidas');
            var $chkCanceladas = $(relatorio + '-canceladas');

            if (status !== 'todos') {
                // Desabilita ambos
                $chkConcluidas.prop('disabled', true);
                $chkCanceladas.prop('disabled', true);

                // Marca/desmarca conforme o valor selecionado
                $chkConcluidas.prop('checked', status === 'C');
                $chkCanceladas.prop('checked', status === 'D');
            } else {
                // Habilita ambos e desmarca ambos
                $chkConcluidas.prop('disabled', false).prop('checked', false);
                $chkCanceladas.prop('disabled', false).prop('checked', false);
            }
        });
    }

    toggleStatusRelatorios('#os2rel');
    toggleStatusRelatorios('#servicosrel');

    function initializeDynamicColumns(prefix) {
        const $order2 = $(`#${prefix}-order2`).closest('.filtro-item');
        const $order3 = $(`#${prefix}-order3`).closest('.filtro-item');
        const $plusBtn = $(`#add-order-col`);

        function updatePlusMinusButtons() {
            // Remove todos os botões de minus
            $('.btn-minus').hide();

            // Sempre mostrar plus se 3ª div estiver oculta
            if ($order3.is(':hidden')) {
                $plusBtn.show();
            } else {
                $plusBtn.hide();
            }

            // Mostrar botão minus apenas na última visível (que não seja a primeira)
            if ($order3.is(':visible')) {
                $order3.find('.btn-minus').show();
            } else if ($order2.is(':visible')) {
                $order2.find('.btn-minus').show();
            }
        }

        function updateSelectOptions() {
            const selectedValues = [
                $(`#${prefix}-order1`).val(),
                $order2.is(':visible') ? $(`#${prefix}-order2`).val() : null,
                $order3.is(':visible') ? $(`#${prefix}-order3`).val() : null
            ];

            $(`select[id^="${prefix}-order"]`).each(function () {
                const currentSelect = $(this);
                const currentVal = currentSelect.val();

                currentSelect.find('option').each(function () {
                    const option = $(this);
                    const val = option.val();
                    const isUsed = selectedValues.includes(val) && val !== currentVal;
                    option.prop('disabled', isUsed);
                });
            });
        }

        // Botão "+" para adicionar colunas
        $plusBtn.on('click', function () {
            if (!$order2.is(':visible')) {
                $order2.show();
            } else if (!$order3.is(':visible')) {
                $order3.show();
            }
            updatePlusMinusButtons();
            updateSelectOptions();
        });

        // Botão "−" para remover colunas
        $('.btn-minus').on('click', function () {
            if ($order3.is(':visible')) {
                $order3.hide();
                $(`#${prefix}-order3`).val('');
            } else if ($order2.is(':visible')) {
                $order2.hide();
                $(`#${prefix}-order2`).val('');
            }
            updatePlusMinusButtons();
            updateSelectOptions();
        });

        // Atualiza quando um select muda
        $(`select[id^="${prefix}-order"]`).on('change', function () {
            updateSelectOptions();
        });

        // Inicial
        $order2.hide();
        $order3.hide();
        updatePlusMinusButtons();
        updateSelectOptions();
    }

    function safeAttr(value) {
        return (value !== undefined && value !== null) ? value : '';
    }

    // Função para controlar o estado do select cliente baseado no checkbox inventário da tela de cadastro de equipamentos
    function toggleClienteField() {
        const $inventarioCheckbox = $('#equipamentosForm').find('#inventario');
        const $clienteSelect = $('#equipamentosForm').find('#id_cli');

        if ($inventarioCheckbox.is(':checked')) {
            // Se inventário estiver marcado: desabilita e remove obrigatoriedade
            $clienteSelect.prop('disabled', true);
            $clienteSelect.removeAttr('required');
            $clienteSelect.val(''); // Limpa a seleção atual
        } else {
            // Se inventário não estiver marcado: habilita e torna obrigatório
            $clienteSelect.prop('disabled', false);
            $clienteSelect.attr('required', 'required');
        }
    }

    // Executa a função no carregamento da página para definir estado inicial
    toggleClienteField();

    // Adiciona evento de mudança no checkbox inventário
    $('#equipamentosForm').find('#inventario').on('change', function () {
        toggleClienteField();
    });

    function updateSelectLocalOptions(origem, destino) {
        var origemValue = $(origem).val();
        var destinoValue = $(destino).val();

        // Primeiro: reabilita somente as opções que NÃO estão marcadas como sem estoque
        $(origem + ' option, ' + destino + ' option').each(function () {
            if (!$(this).data('no-stock')) {
                $(this).prop('disabled', false);
            }
        });

        // Disable selected option in the other select
        if (origemValue) {
            $(destino + ' option[value="' + origemValue + '"]').prop('disabled', true);
        }

        if (destinoValue) {
            $(origem + ' option[value="' + destinoValue + '"]').prop('disabled', true);
        }
    }

    $(document).on('change', '#localOrigemSelect, #localDestinoSelect', function () {
        updateSelectLocalOptions('#localOrigemSelect', '#localDestinoSelect');
    });

    $(document).on('change', '#usuarioOrigemSelect, #usuarioDestinoSelect', function () {
        updateSelectLocalOptions('#usuarioOrigemSelect', '#usuarioDestinoSelect');
    });

    $(document).on('change', '#entrada', function () {
        const isEntradaChecked = $('#entrada').is(':checked');

        if (isEntradaChecked) {
            // Desabilita os selects de origem e remove obrigatoriedade
            $('#usuarioOrigemSelect').prop('disabled', true).removeAttr('required').val('');
            $('#localOrigemSelect').prop('disabled', true).removeAttr('required').val('');
            // Desabilita o checkbox saída
            $('#saida').prop('disabled', true).prop('checked', false);
            $(".fornecedor-mov-row").prop('hidden', false); // Exibe a linha de fornecedor
        } else {
            // Habilita os selects de origem e restaura obrigatoriedade
            $('#usuarioOrigemSelect').prop('disabled', false).attr('required', 'required');
            $('#localOrigemSelect').prop('disabled', false).attr('required', 'required');
            // Habilita o checkbox saída
            $('#saida').prop('disabled', false);
            $(".fornecedor-mov-row").prop('hidden', true); // Oculta a linha de fornecedor
        }
    });

    $(document).on('change', '#saida', function () {
        const isSaidaChecked = $('#saida').is(':checked');

        if (isSaidaChecked) {
            // Desabilita os selects de destino e remove obrigatoriedade
            $('#usuarioDestinoSelect').prop('disabled', true).removeAttr('required').val('');
            $('#localDestinoSelect').prop('disabled', true).removeAttr('required').val('');
            // Desabilita o checkbox entrada
            $('#entrada').prop('disabled', true).prop('checked', false);
        } else {
            // Habilita os selects de destino e restaura obrigatoriedade
            $('#usuarioDestinoSelect').prop('disabled', false).attr('required', 'required');
            $('#localDestinoSelect').prop('disabled', false).attr('required', 'required');
            // Habilita o checkbox entrada
            $('#entrada').prop('disabled', false);
        }
    });

    // Função para resetar o estado da modal quando ela for aberta
    function resetModalState() {
        // Habilita todos os elementos
        $('#usuarioOrigemSelect').prop('disabled', false).attr('required', 'required');
        $('#localOrigemSelect').prop('disabled', false).attr('required', 'required');
        $('#usuarioDestinoSelect').prop('disabled', false).attr('required', 'required');
        $('#localDestinoSelect').prop('disabled', false).attr('required', 'required');
        $('#entrada').prop('disabled', false);
        $('#saida').prop('disabled', false);

        // Reabilita todas as opções dos selects
        $('#usuarioOrigemSelect option, #usuarioDestinoSelect option').prop('disabled', false);
        $('#localOrigemSelect option, #localDestinoSelect option').prop('disabled', false);

        // Limpa valores dos selects
        $('#usuarioOrigemSelect').val('');
        $('#localOrigemSelect').val('');
        $('#usuarioDestinoSelect').val('');
        $('#localDestinoSelect').val('');

        // Desmarca os checkboxes
        $('#entrada').prop('checked', false);
        $('#saida').prop('checked', false);

        // Oculta a linha de fornecedor
        $(".fornecedor-mov-row").prop('hidden', true);
    }

    // Adicione esta chamada ao evento de abertura da sua modal
    // Substitua '#suaModal' pelo seletor real da sua modal
    $(document).on('show.bs.modal', '#modalMovFerramenta', function () {
        resetModalState();
    });


    $(document).ready(function () {
        if ($('#servicosrel-order1').length) {
            initializeDynamicColumns('servicosrel');
        }
        if ($('#os2rel-order1').length) {
            initializeDynamicColumns('os2rel');
        }
    });

    function atualizaTabelaChkGrupos(dados) {
        let saveLink = $("#form-chkgrupo").prop("action");

        console.log(dados);

        const $tbody = $("#chkgrupo-list tbody");
        $tbody.empty(); // Limpa o conteúdo atual da tabela

        if (!dados.length) {
            $tbody.append("<tr><td colspan='100%'>Nenhum dado encontrado</td></tr>");
            return;
        }

        dados.forEach(item => {

            var botaoEdit = '<button type="button" class="btn btn-secondary list-edt edit-chkgrupo"><i class="fa fa-pen"></i></button>';
            var botaoConfirm = '<button type="button" data-id="' + item.id_encode + '" data-url="' + saveLink + '" class="btn btn-success confirm-edit-chkgrupo" hidden><i class="fa fa-check"></i></button>';
            var botaoDelete = '<button type="button" class="btn btn-secondary list-del chkgrupo-delete" data-id="' + item.id_encode + '"><i class="fa fa-trash"></i></button>';
            var botaoCancel = '<button type="button" class="btn btn-danger cancel-edit-chkgrupo" hidden><i class="fa fa-xmark"></i></button>';

            const linha = $(`<tr></tr>`);
            linha.append(`<td>${item.descricao}</td>`);
            linha.append(`<td>${botaoEdit}${botaoConfirm}</td>`);
            linha.append(`<td>${botaoDelete}${botaoCancel}</td>`);
            $tbody.append(linha);
        });

        $("#form-chkgrupo").find("input[name='descricao']").val('');
    }

    $(document).on("click", ".chkgrupo-delete", function (e) {
        let url = $("#form-chkgrupo").data("delete"); //checklist/excluirGrupo
        let item = $(this).closest("tr");
        let id = $(this).data('id');

        if (confirm('Tem certeza que deseja excluir este item?')) {
            $.ajax({
                url: url,
                data: { id: id },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.status == 'success') {
                        var tbody = $('#chkgrupo-list tbody');
                        item.remove();

                        // Verifica se a tabela ficou vazia após a remoção
                        if (tbody.find('tr').length === 0) {
                            tbody.append('<tr><td colspan="100%">NENHUM REGISTRO ENCONTRADO</td></tr>');
                        }

                        ajaxMessage(response.message, 5);
                    } else {
                        ajaxMessage(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
                }
            });
        }
    });

    $(document).on("click", ".edit-chkgrupo", function (e) {
        e.preventDefault();

        let row = $(this).closest("tr");
        let descCell = row.find("td:eq(0)");

        // Armazena os valores antigos nos atributos data da linha
        row.data("oldValues", {
            descricao: descCell.text()
        });

        let oldValues = row.data("oldValues");

        // Substitui os textos por inputs                
        descCell.html(`<input type="text" class="form-control" value="${oldValues.descricao}" style="height: 30px; width: 100%;">`);

        // Oculta o botão de edição e mostra os de confirmação/cancelamento
        row.find(".edit-chkgrupo").hide();
        row.find(".confirm-edit-chkgrupo").prop("hidden", false);
        row.find(".chkgrupo-delete").hide();
        row.find(".cancel-edit-chkgrupo").prop("hidden", false);
    });

    $(document).on("click", ".cancel-edit-chkgrupo", function () {
        let row = $(this).closest("tr");

        // Recupera os valores antigos armazenados nos atributos data
        let oldValues = row.data("oldValues");

        row.find("td:eq(0)").text(oldValues.descricao);

        // Restaura os botões
        row.find(".edit-chkgrupo").show();
        row.find(".confirm-edit-chkgrupo").prop("hidden", true);
        row.find(".chkgrupo-delete").show();
        row.find(".cancel-edit-chkgrupo").prop("hidden", true);
    });

    $(document).on("click", ".confirm-edit-chkgrupo", function () {
        let row = $(this).closest("tr");
        let descricao = row.find("td:eq(0) input").val();
        let id = $(this).data('id');
        let url = $(this).data('url');

        $.ajax({
            url: url,
            type: "POST",
            data: {
                id: id,
                descricao: descricao
            },
            dataType: "json",
            success: function (response) {
                // Atualiza as células com os novos valores
                row.find("td:eq(0)").text(descricao);

                ajaxMessage(response.mensagem, 5);
                row.find(".edit-chkgrupo").show();
                row.find(".confirm-edit-chkgrupo").prop("hidden", true);
                row.find(".chkgrupo-delete").show();
                row.find(".cancel-edit-chkgrupo").prop("hidden", true);
            },
            error: function () {
                alert("Erro ao salvar os dados. Tente novamente.");
            }
        });
    });

    $('#novo-chkitem').click(function () {
        $('#modalChkItem').modal('show');
        $('#form-chkitem').trigger('reset');
        $("#chkitem-sectit").text("Novo");
    });

    $(".chkitem-edit").click(function (e) {
        let id = $(this).data('id');
        let url = $(this).data('url');

        $('#modalChkItem').modal('show');
        $('#form-chkitem').trigger('reset');

        $("#chkitem-sectit").text("Editar");
        $('#chkitem-id').val(id);

        $.ajax({
            url: url,
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $("#chkitem-grupo").val(response.item[0].id_chkgrupo);
                    $("#chkitem-descricao").val(response.item[0].descricao);
                } else {
                    ajaxMessage(response.message, 5);
                }
            },
            error: function () {
                ajaxMessage("<div class='message error icon-warning'>Erro ao carregar os dados do item.</div>", 5);
            }
        });
    });

    $(document).on("click", ".os2eqp-open-modalchk", function () {

        let row = $(this).closest("tr");
        let eqpDesc = row.find("td:eq(0)").text();
        let id_os2 = $(this).data('id_os2');
        let id_os2_2 = $(this).data('id_os2_2');
        let url = $("#form-equipamentosos2modal").data('exibechkmodal');

        $("#modalOs2Chk .chk-eqp-desc").text(eqpDesc);
        $("#modalOs2Chk .chk-eqp-os2").text(id_os2);
        $("#modalOs2Chk #chk-eqp-pdf").attr('data-idos2_2', id_os2_2);
        $("#modalOs2Chk").modal('show');

        // Guarda os IDs como variáveis globais para usar no POST
        $('#modalOs2Chk').data('id_os2', id_os2);
        $('#modalOs2Chk').data('id_os2_2', id_os2_2);

        $.get(url, { id_os2: id_os2, id_os2_2: id_os2_2 }, function (response) {
            $('#checklist-container').html(response);
        });
    });

    backdropModal('#modalOs2Chk', 1055);
    backdropModal('#modalOpr', 1055);
    backdropModal('#modalEqpMedicao', 1055);
    backdropModal('#modalConfirmSolicitacao', 1055);
    backdropModal('#modalFinSrc', 1055);
    backdropModal('#modalNovoplconta', 1055);
    backdropModal('#modalNovoOpr', 1055);

    //** INÍCIO JS MODAL CHKLIST TELA DE EQUIPAMENTOS */

    // Modal functionality
    const chklstModal = $('#chklstModalOverlay');
    const chklstOpenBtn = $('#chklstOpenBtn');
    const chklstCloseBtn = $('#chklstCloseBtn');
    const chklstCancelBtn = $('#chklstCancelBtn');
    const chklstConfigForm = $('#chklstConfigForm');

    // Abrir modal
    chklstOpenBtn.on('click', function () {
        chklstModal.addClass('chklst-active');
        $('body').css('overflow', 'hidden');
    });

    // Fechar modal
    function chklstCloseModal() {
        chklstModal.removeClass('chklst-active');
        $('body').css('overflow', 'auto');
    }

    chklstCloseBtn.on('click', chklstCloseModal);
    chklstCancelBtn.on('click', chklstCloseModal);

    // Fechar ao clicar no overlay
    chklstModal.on('click', function (e) {
        if (e.target === this) {
            chklstCloseModal();
        }
    });

    // Fechar com ESC
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && chklstModal.hasClass('chklst-active')) {
            chklstCloseModal();
        }
    });

    // Submit do formulário de configurações
    chklstConfigForm.on('submit', function (e) {
        e.preventDefault();

        // Coleta os dados dos checkboxes
        const configuracoes = [];
        $('input[name="configuracoes[]"]:checked').each(function () {
            configuracoes.push($(this).val());
        });

        const seguranca = [];
        $('input[name="seguranca[]"]:checked').each(function () {
            seguranca.push($(this).val());
        });

        const manutencao = [];
        $('input[name="manutencao[]"]:checked').each(function () {
            manutencao.push($(this).val());
        });

        // Aqui você pode processar os dados como precisar
        // console.log('Configurações:', configuracoes);
        // console.log('Segurança:', seguranca);
        // console.log('Manutenção:', manutencao);

        // Exemplo: adicionar campos hidden ao formulário principal
        $('#equipamentosForm').append(
            '<input type="hidden" name="config_extras" value="' + JSON.stringify({
                configuracoes: configuracoes,
                seguranca: seguranca,
                manutencao: manutencao
            }) + '">'
        );

        alert('Configurações salvas com sucesso!');
        chklstCloseModal();
    });

    //** FIM JS MODAL CHKLIST TELA DE EQUIPAMENTOS */

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

    $(document).on("click", "[data-post]", function (e) {
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

                //pdf os2
                if (data.post && data.post.includes('os2rel/pdfos2')) {
                    if (!response.error) {
                        const newWindow = window.open();
                        newWindow.document.open();
                        newWindow.document.write(response.html); // Use a propriedade 'html' do objeto retornado
                        newWindow.document.close();
                        load.fadeOut(200);
                    }
                }

                //pdf servicos
                if (data.post && data.post.includes('servicosrel/pdfservicos')) {
                    if (!response.error) {
                        const newWindow = window.open();
                        newWindow.document.open();
                        newWindow.document.write(response.html); // Use a propriedade 'html' do objeto retornado
                        newWindow.document.close();
                        load.fadeOut(200);
                    }
                }

                //pdf relatorio financeiro
                if (data.post && data.post.includes('financeirorel/pdf')) {
                    if (!response.error) {
                        const newWindow = window.open();
                        newWindow.document.open();
                        newWindow.document.write(response.html); // Use a propriedade 'html' do objeto retornado
                        newWindow.document.close();
                        load.fadeOut(200);
                    }
                }

                //pdf chk
                if (data.post && data.post.includes('ordens/checklistpdf')) {
                    if (response.success && response.pdf_url) {
                        // Abrir PDF em nova aba
                        window.open(response.pdf_url, '_blank');
                        load.fadeOut(200);
                    } else {
                        ajaxMessage('Erro ao gerar PDF', 5);
                        load.fadeOut(200);
                    }
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

                //piscar botão
                if (response.piscar) {
                    piscarElemento(response.piscar, 20);
                }

                //grupo cheklist
                if (response.checklist) {
                    if (response.grupos.length > 0) {
                        atualizaTabelaChkGrupos(response.grupos);
                    }
                    ajaxMessage(response.mensagem, ajaxResponseBaseTime);
                }

                //addplconta
                if (response.plcontamodal) {
                    $(response.form).append(`<option value="${response.plconta.id}">${response.plconta.descricao}</option>`);
                    $(response.form).val(response.plconta.id).trigger('change');
                    $('.close').trigger('click');
                }

                //addopr
                if (response.modaloperacao) {
                    $('#' + response.form).append(`<option value="${response.operacao.id}">${response.operacao.id} - ${response.operacao.descricao}</option>`);
                    $('#' + response.form).val(response.operacao.id).trigger('change');
                    $('.close').trigger('click');
                }

                //addcli
                if (response.idcli) {
                    $(response.form).append(`<option value="${response.idcli}">${response.nomecli}</option>`);
                    $(response.form).val(response.idcli).trigger('change');
                    //console.log(response.form);
                    if (response.form == "#cliente-os") {
                        $("#cliente-obra").append(`<option value="${response.idcli}">${response.nomecli}</option>`);
                        $("#cliente-obra").val(response.idcli);
                    }
                    $('.close').trigger('click');
                }

                //relatorio tarefas
                if (response.os2rel) {
                    $("#os2rel-list tbody").empty();
                    preencherTabelaOs2(response.registros);
                    $("#os2rel-totalregistros").empty().text(`Total de Registros: ${response.total}`);
                    $("#active-page").empty();
                    criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, "os2rel");
                    armazenarFiltrosPaginacao(response.paginacao);
                    const urlPdf = response.filtros.url_pdf;
                    const pdf2 = `<button type="button" class="btn btn-info" id="os2rel-pdf"
                                                data-post="${urlPdf}"
                                                data-dados=''>
                                    <i class="fa fa-file-pdf"></i> PDF</button>`;

                    $('#os2rel-btns').empty().append(pdf2);
                    $('#os2rel-pdf').attr('data-dados', JSON.stringify(response.filtros));
                }

                if (response.servicosrel) {
                    $("#servicosrel-list tbody").empty();
                    preencherTabelaServicos(response.registros);
                    $("#servicosrel-totalregistros").empty().text(`Total de Registros: ${response.total}`);
                    $("#active-page").empty();
                    criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, "servicosrel");
                    armazenarFiltrosPaginacao(response.paginacao);
                    const urlPdf = response.filtros.url_pdf;
                    const pdf2 = `<button type="button" class="btn btn-info" id="servicosrel-pdf"
                                                data-post="${urlPdf}"
                                                data-dados=''>
                                    <i class="fa fa-file-pdf"></i> PDF</button>`;
                    $('#servicosrel-btns').empty().append(pdf2);
                    $('#servicosrel-pdf').attr('data-dados', JSON.stringify(response.filtros));
                }

                if (response.financeirorel) {
                    $("#financeirorel-list tbody").empty();
                    preencherTabelaFinanceiro(response.registros);
                    $("#financeirorel-totalregistros").empty().text(`Total de Registros: ${response.total}`);
                    $("#active-page").empty();
                    criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, response.paginacao.form);
                    armazenarFiltrosPaginacao(response.paginacao);
                    const urlPdf = response.filtros.url_pdf;
                    const pdf2 = `<button type="button" class="btn btn-info" id="financeirorel-pdf"
                                                data-post="${urlPdf}"
                                                data-dados=''>
                                    <i class="fa fa-file-pdf"></i> PDF</button>`;
                    $('#financeirorel-btns').empty().append(pdf2);
                    $('#financeirorel-pdf').attr('data-dados', JSON.stringify(response.filtros));
                }

                if (response.retornobaixas) {
                    // Desabilitar botões com classe btnBaixar2
                    $('.btnBaixar2').find('button').prop('disabled', true);

                    // Desabilitar todos os botões e inputs dentro de #telabaixas-content
                    $('#telabaixas-content').find('button, input').prop('disabled', true);

                    $("#baixas-success").show();

                    $("#baixas-success button.baixas-lote-rel").data("id", response.baixaid);
                    $("#baixas-success button.baixas-lote-rel").data("url", response.url);
                    $("#baixas-success #id-baixas-lote").text(response.baixaid);
                    $("#baixas-success #dia-baixas-lote").text(response.databaixa);
                }

                if (response.baixaslist) {
                    $("#tableBaixasList tbody").empty();
                    preencherTabelaBaixas(response.registros); // CORRETO!
                    $("#baixas-totalregistros").empty().text(`Total de Registros: ${response.total}`);
                    $("#active-page").empty();
                    criarPaginacao(response.total, response.paginacao.page, response.paginacao.limit, 'baixas-list');
                    armazenarFiltrosPaginacao(response.paginacao);

                    // Atualiza o hidden #page do form
                    $("#baixas-list-form input[name='page']").val(response.paginacao.page);
                }

                if (response.chksave) {
                    $('#equipOs2-list tbody tr').each(function () {
                        var $row = $(this);
                        var $button = $row.find('.os2eqp-open-modalchk');

                        if ($button.length > 0) {
                            var buttonDataId = $button.data('id_os2_2');

                            if (buttonDataId == response.os2_2) {
                                if (buttonDataId == response.os2_2) {
                                    var $row = $(this).closest('tr');
                                    if (response.temchk === true) {
                                        $row.find('.os2eqp-edit').prop('disabled', true);
                                    } else {
                                        $row.find('.os2eqp-edit').prop('disabled', false);
                                    }
                                }
                            }
                        }
                    });
                }

                //servico via ordensform
                if (response.novosrv) {
                    var targetSelect = $('select[name="' + response.select + '"]');
                    targetSelect.append(`<option
                        value="${safeAttr(response.id_servico)}"
                        data-valor="${safeAttr(response.valor)}"
                        data-tempo="${safeAttr(response.tempo)}"
                        data-medicao="${safeAttr(response.medicao)}"
                        data-unidade="${safeAttr(response.unidade)}"
                        data-recorrencia="${safeAttr(response.recorrencia)}"
                        data-diarecorrencia="${safeAttr(response.diarecorrencia)}"
                        data-datalegal="${safeAttr(response.datalegal)}">${response.nome}</option>`);

                    targetSelect.val(response.id_servico).trigger('change');

                    $('.close').trigger('click');
                }


                //osagenda
                if (response.ordem) {
                    $('.close').trigger('click');
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

    $(document).on("click", ".baixas-lote-rel", function () {
        let id = $(this).data("id");
        let url = $(this).data("url");

        $.ajax({
            url: url, // URL para o endpoint do relatório
            method: 'POST',
            data: {
                id: id // Envia os IDs para o endpoint
            },
            dataType: 'json',
            success: function (response) {
                if (!response.error) {
                    const newWindow = window.open();
                    newWindow.document.open();
                    newWindow.document.write(response.html);
                    newWindow.document.close();
                }
            },
            error: function (xhr, status, error) {
                ajaxMessage('Erro na requisição: ' + error, 5);
            }
        });

    });

    $(document).on("click", "#btnFiltrarBaixas", function (e) {
        e.preventDefault();

        // Reset página para 1 ao filtrar
        $("#baixas-list-form input[name='page']").val(1);

        // Submete o formulário que já será capturado pelo seu sistema de submit
        $("#baixas-list-form").submit();
    });

    function preencherTabelaBaixas(registros) {
        const $tbody = $("#tableBaixasList tbody");
        $tbody.empty();

        if (!registros || registros.length === 0) {
            $tbody.append('<tr><td colspan="4">Nenhum registro encontrado!</td></tr>');
            return;
        }

        registros.forEach(b => {
            const tipo = b.tipo === "R" ? "Receitas" : "Despesas";
            const tr = `
                <tr>
                    <td>${b.id}</td>
                    <td>${b.data_baixa}</td>
                    <td>${tipo}</td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm baixas-lote-rel" 
                            data-id="${b.id}" data-url="${b.url}">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            $tbody.append(tr);
        });
    }

    $(document).on("click", ".baixas-lote-list", function () {
        $('#modalBaixasList').modal('show');

        let page = 1;
        let limit = 15;
        let url = $(this).data("url");

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                page: page,
                limit: limit
            },
            dataType: 'json',
            success: function (response) {

                $("#tableBaixasList tbody").empty();
                preencherTabelaBaixas(response.registros);
                $("#baixas-totalregistros").empty().text(`Total de Registros: ${response.total}`);

                criarPaginacao(response.total, response.page, response.limit, "baixas-list");

                // Atualiza os filtros armazenados com a nova página
                armazenarFiltrosPaginacao(response.paginacao);

            },
            error: function (xhr, status, error) {
                ajaxMessage('Erro na requisição: ' + error, 5);
            }
        });
    });

    /*
     * IMAGE RENDER
     */
    $("[data-image]").change(function (e) {
        var changed = $(this);
        var file = this;
        var label = changed.prev("label");

        if (file.files && file.files[0]) {
            var render = new FileReader();

            render.onload = function (e) {
                $(changed.data("image")).fadeTo(100, 0.1, function () {
                    label.css("background-image", "url('" + e.target.result + "')")
                        .fadeTo(100, 1);
                });
            };
            render.readAsDataURL(file.files[0]);
        }
    });

    // PISCA ELEMENTO
    function piscarElemento(selector, vezes, cor = 'red', tempo = 100) {
        let $el = $(selector);
        let originalBg = $el.css('background-color');
        let count = 0;

        function piscar() {
            if (count >= vezes) {
                $el.css('background-color', originalBg); // garante que termine na cor original
                return;
            }
            $el.css('background-color', cor);
            setTimeout(() => {
                $el.css('background-color', originalBg);
                count++;
                setTimeout(piscar, tempo);
            }, tempo);
        }

        piscar();
    }

    // PISCA CONTÍNUO (para casos como licença vencida)
    function piscarElementoContinuo(selector, corFundo = 'red', tempo = 400) {
        let $el = $(selector);
        let originalBg = $el.css('background-color');
        let ligado = false;

        // Evita criar mais de um intervalo pro mesmo elemento
        if ($el.data('piscar-ativo')) return;

        let intervalo = setInterval(() => {
            // Alterna a cor de fundo do elemento principal
            $el.css('background-color', ligado ? originalBg : corFundo);

            // Se dentro do elemento existir algo com a classe .licenca-text,
            // alterna a cor do texto entre preto e vermelho
            $el.find('.licenca-text').css('color', ligado ? 'black' : 'red');
            $el.find('.licenca-text').css('font-size', ligado ? '1em' : '1.050em');

            ligado = !ligado;
        }, tempo);

        // Marca como ativo para não duplicar intervalos
        $el.data('piscar-ativo', true);
    }

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

    $(document).on("click", "#voltar-link", function (e) {
        e.preventDefault();
        location.reload(); // Recarrega a página para restaurar o estado inicial
    });

    $('.only-int').on('input', function () {
        // Remove tudo que não for número
        this.value = this.value.replace(/\D/g, '');

        // Remove zero à esquerda, caso exista
        this.value = this.value.replace(/^0+/, '');

        // Se o campo ficar vazio, coloca "0" (não deixa ficar vazio)
        if (this.value === '') {
            this.value = '0';
        }
    });

    /*
    * SCRITP MÓDULO CARTÃO DE PONTO
    */

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
                    //console.log()

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

        $('.licenca-vencida').each(function () {
            piscarElementoContinuo(this, 'yellow', 500);
        });

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

        $("#save-data2").click(function () {
            $("#save-data").trigger('click');
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

    /*
    * FIM CARTÃO DE PONTO
    */


    /**
     * Input de Procura
     * @param {id do input} inputSelector 
     * @param {id da tabela} tableSelector 
     */
    function filtrarLista(inputSelector, tableSelector, linhaTela = "") {
        $(inputSelector).on("input", function () {
            var valorProcurado = $(this).val().toLowerCase();
            var linhasVisiveis = 0;

            // Aplica a classe 'linhaTela' de forma dinâmica
            $(tableSelector + " tbody tr" + linhaTela).each(function () {
                var linha = $(this);
                var textoLinha = linha.text().toLowerCase();
                var operadores = (linha.data("tooltip") || "").replace(/<[^>]*>/g, "").toLowerCase();

                if (textoLinha.indexOf(valorProcurado) > -1 || operadores.indexOf(valorProcurado) > -1) {
                    linha.show();
                    linhasVisiveis++;
                } else {
                    linha.hide();
                }
            });

            // Remove a mensagem de "Nenhum resultado" se existir
            $(tableSelector + " tbody .nenhum-resultado").remove();

            // Se não houver linhas visíveis, exibe a mensagem
            if (linhasVisiveis === 0) {
                $(tableSelector + " tbody").append(
                    "<tr class='nenhum-resultado'><td colspan='100%'>Nenhum resultado encontrado</td></tr>"
                );
            }
        });
    }

    function checkClienteOs() {
        if ($('#cliente-os').val() !== "") {
            $('#abre-obra').prop('disabled', false);
        } else {
            $('#abre-obra').prop('disabled', true);
            $('#abre-obra').prop('checked', false);
            $('#obra-container').hide();
        }
    }

    function checkAbreObra() {
        if ($('#abre-obra').is(':checked')) {
            $('#obra-container').show();
        } else {
            $('#obra-container').hide();
            $("#obra").val("");
        }
    }

    function filterObras() {
        var selectedCliente = $('#cliente-os').val();
        $('#obra option').each(function () {
            var obraEnt = $(this).data('ent');
            if (obraEnt == selectedCliente || $(this).val() == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // function filterObrasList() {
    //     var selectedCliente = $('#cliente-os').val();
    //     $('#obra_lst tbody tr').each(function () {
    //         var obraEnt = $(this).data('ent');
    //         if (obraEnt == selectedCliente || obraEnt == "") {
    //             $(this).show();
    //         } else {
    //             $(this).hide();
    //         }
    //     });
    // }

    function enviardados(dados, url) {
        var form = $('<form>', {
            'action': url, // A URL configurada no botão
            'method': 'POST'
        });

        // Adiciona os ids ao formulário como campos ocultos
        $.each(dados, function (index, item) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'tabelaDados[' + index + '][id]',
                'value': item.id
            }));
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'tabelaDados[' + index + '][tipo]',
                'value': item.tipo
            }));
        });

        // Adiciona o formulário ao corpo e faz o submit
        form.appendTo('body').submit();
    }


    $(document).ready(function () {
        $('#port_banco').select2();
        $('#emp_dev').select2();
        $('#servicosrel-cliente').select2();

        $('#preos_cliente').select2({
            dropdownParent: $('#modalPreOs')
        });
        $('#preos_servico').select2({
            dropdownParent: $('#modalPreOs')
        });
        $('#preos_operador').select2({
            dropdownParent: $('#modalPreOs')
        });
        $('#preos_mat').select2({
            dropdownParent: $('#modalPreOs')
        });

        $("#os-cli").select2();
        $("#os-tarefa").select2();
        $("#os-operador").select2();
        $("#os-segmento").select2();



        $('#ent_user').addClass('select-readonly');

        if ($('#tipo_user').val() == "2" || $('#tipo_user').val() == "3") {
            $('#ent_user').removeClass('select-readonly');
        }

        $("#tipo_user").on('change', function () {
            if ($(this).val() == "2" || $(this).val() == "3") {
                $('#ent_user').removeClass('select-readonly');
            } else {
                $('#ent_user').addClass('select-readonly');
            }
        });

        $("#ent_user").on('change', function () {
            if ($(this).val() > 0 && $("#id_users").val() == "") {

                var texto = $('#ent_user option:selected').text();
                var nome = texto.split(' ')[0];

                $("#nome_user").val(nome);
            }
        });
    });



    $(document).ready(function () {
        totaisTelaBaixa();

        $('#baixar-list').on('click', '.deleteBx', function () {
            // Remover a linha correspondente
            $(this).closest('tr').remove();
            totaisTelaBaixa();
        });

        $('#baixar-list').on('input', '.saldoBx, .descontoBx, .outrosBx, .vbaixaBx', function () {
            totaisTelaBaixa();
        });

        $("#btnBaixar").on('click', function () {
            var dadosMarcados = [];
            var url = $(this).data('action');

            $('.check-contas:checked').each(function () {
                var id = $(this).data('id');
                var tipo = $(this).data('tipo');
                dadosMarcados.push({
                    id: id,
                    tipo: tipo
                });
            });

            if (dadosMarcados.length === 0) {
                alert('Selecione ao menos um registro para baixar.');
                return;
            }

            enviardados(dadosMarcados, url);
        });

        $("#btnEstornar").on('click', function () {
            var dadosMarcados = [];
            var url = $(this).data('action');

            $('.check-contas:checked').each(function () {
                var id = $(this).data('id');
                var tipo = $(this).data('tipo');
                dadosMarcados.push({
                    id: id,
                    tipo: tipo
                });
            });

            if (dadosMarcados.length === 0) {
                alert('Selecione ao menos um registro para estornar.');
                return;
            }

            enviarEstorno(dadosMarcados, url);
        });

        $("#btn-estorna-parcial").on('click', function (event) {
            let idsMarcados = [];
            let url = $(this).data('action');

            if (confirm('Confirma o estorno das baixas selecionadas?')) {

                $("#saldo-table tbody tr").each(function () {
                    let checkbox = $(this).find('.check-baixa');
                    let id = checkbox.closest('tr').data('id');
                    let tipo = checkbox.closest('tr').data('tipo');

                    if (checkbox.is(':checked')) {
                        idsMarcados.push({ id: id, tipo: tipo });
                    }
                });


                $.ajax({
                    type: "POST",
                    url: url,
                    data: { dados: idsMarcados },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            location.reload();
                        } else {
                            ajaxMessage(response.message, 5);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr, status, error);
                        ajaxMessage('Erro na requisição. Tente novamente.');
                    }
                });

            }

        });

        function enviarEstorno(dados, url) {
            $.ajax({
                type: "POST",
                url: url,
                data: { tabelaDados: dados },
                dataType: "json",
                success: function (response) {
                    // Limpa o corpo da tabela antes de adicionar novos dados
                    $('#estornar-body').empty();

                    let totalRegistros = 0;
                    let totalSaldo = 0;

                    // Itera sobre os dados de estorno na resposta
                    $.each(response.estornar, function (index, item) {

                        const row = $('<tr></tr>');
                        const apagar = $('<td><button type="button" class="btn btn-acao-small deleteBx"><i class="fa-regular fa-rectangle-xmark vermelho"></i></button></td>');

                        let razao = '';
                        if (item.tipo === 'receita') {
                            const cliente = response.cliente.find(c => c.id === item.id_entc);
                            if (cliente) {
                                razao = cliente.nome;
                            }
                        } else if (item.tipo === 'despesa') {
                            const fornecedor = response.fornecedor.find(f => f.id === item.id_entf);
                            if (fornecedor) {
                                razao = fornecedor.nome;
                            }
                        }

                        // Adiciona as células à linha
                        row.append($('<td hidden><input name="idEs_' + item.id + '" value="' + item.id + '"></td>'));
                        row.append($('<td hidden><input name="tipoEs_' + item.id + '" value="' + item.tipo + '"></td>')); row.append($('<td></td>').text(item.titulo));
                        row.append($('<td></td>').text(razao));
                        row.append($('<td></td>').text(formatarValor(item.valor)));
                        row.append(apagar);

                        // Adiciona a linha ao corpo da tabela
                        $('#estornar-body').append(row);
                    });
                    totaisTelaEstorno();

                },
                error: function (xhr, status, error) {
                    console.error(xhr, status, error);
                }
            });
        }

        $('#estornar-list').on('click', '.deleteBx', function () {
            // Remover a linha correspondente
            $(this).closest('tr').remove();
            totaisTelaEstorno();
        });

        function totaisTelaEstorno() {
            let totalRegistros = 0;
            let valor = 0;

            // Percorre todas as linhas da tabela
            $('#estornar-list tbody tr').each(function () {
                totalRegistros++;
                let valorTd = $(this).find('td').eq(4).text();

                valorConvertido = parseFloat(valorTd.replace(/\./g, '').replace(',', '.'));
                valor += valorConvertido;
            });

            // Atualiza os totais no footer
            $('#totalRegistrosEs').text(totalRegistros);
            $('#totalEstorno').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        $('#modal-estornar-submit').on('click', function (event) {
            event.preventDefault();
            var qtde = $('#totalRegistrosEs').text();
            var total = $('#totalEstorno').text();
            var alerta = qtde + ' lançamentos. ' + ' Valor total R$ ' + total;

            if (qtde == 1) {
                alerta = qtde + ' lançamento no valor de R$ ' + total;
            }

            var confirmar = confirm(alerta + '. Tem certeza que deseja estornar?');

            if (confirmar) {
                $("#form-contasestornar").submit();
            }
        });

        $("#btnExcluir").on('click', function (event) {
            var dadosMarcados = [];
            var url = $(this).data('action');

            $('.check-contas:checked').each(function () {
                var id = $(this).data('id');
                var tipo = $(this).data('tipo');
                dadosMarcados.push({
                    id: id,
                    tipo: tipo
                });
            });

            var qtde = dadosMarcados.length;
            var alerta = "1 lançamento selecionado";

            if (qtde > 1) {
                alerta = qtde + " lançamentos selecionados";
            }

            var confirmar = confirm(alerta + ' para exclusão. Confirma?');

            if (confirmar) {
                enviardados(dadosMarcados, url);
            }
        });


        function totaisTelaBaixa() {
            let totalRegistros = 0;
            let totalSaldo = 0;
            let totalDesconto = 0;
            let totalOutros = 0;
            let totalVbaixa = 0;
            let totalLiquido = 0;

            // Percorre todas as linhas da tabela
            $('#baixar-list tbody tr.registrosBx').each(function () {
                totalRegistros++; // Contar o registro se a linha existir
                let saldoText = $(this).find('.saldoBx').text();
                let vbaixaInput = $(this).find('.vbaixaBx');
                let descontoInput = $(this).find('.descontoBx');
                let outrosInput = $(this).find('.outrosBx');
                let liquido = $(this).find('.liquidoBx');

                // Verifica se saldoText não está vazio antes de tentar substituir
                let saldo = parseFloat(saldoText.replace(/\./g, '').replace(',', '.')) || 0;

                // Verifica se os inputs existem e têm valor
                let desconto = descontoInput.length ? parseFloat(descontoInput.val().replace(/\./g, '').replace(',', '.')) || 0 : 0;
                let outros = outrosInput.length ? parseFloat(outrosInput.val().replace(/\./g, '').replace(',', '.')) || 0 : 0;
                let vbaixa = vbaixaInput.length ? parseFloat(vbaixaInput.val().replace(/\./g, '').replace(',', '.')) || 0 : 0;

                let vliquido = (saldo + outros) - (vbaixa + desconto);
                liquido.text(vliquido.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                totalSaldo += saldo;
                totalDesconto += desconto;
                totalOutros += outros;
                totalVbaixa += vbaixa;
                totalLiquido += vliquido;
            });

            // Atualiza os totais no footer
            $('#totalRegistrosBx').text(totalRegistros);
            $('#totalSaldoBx').text(totalSaldo.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#totalDescontoBx').text(totalDesconto.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#totalOutrosBx').text(totalOutros.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#totalVbaixaBx').text(totalVbaixa.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#totalLiquidoBx').text(totalLiquido.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            // Habilita ou desabilita o botão
            $('#btnBaixar2').prop("disabled", totalRegistros === 0);
            $('#btnBaixar3').prop("disabled", totalRegistros === 0);
            if (totalRegistros == 0) {
                $("#allcopyBx").off("click");
            } else {
                $("#allcopyBx").on("click");
            }

        }

        $("#allcopyBx").on("click", function () {
            $("#vbaixa").val('');
            $("tbody tr").each(function () {
                const saldo = $(this).find('.saldoBx').text().trim();
                $(this).find('.vbaixaBx').val(saldo);
            });
            totaisTelaBaixa();
        });

        $('.inputBx-container span').on('click', function () {
            var saldo = $(this).closest('tr').find('.saldoBx').text().trim();
            $(this).closest('tr').find('.vbaixaBx').val(saldo);
            totaisTelaBaixa();
        });
    });

    $(document).ready(function () {

        // Recupera os filtros salvos e os aplica na interface
        function carregarFiltros() {
            var tipoSalvo = localStorage.getItem('tipoFiltro');
            var situacaoSalva = localStorage.getItem('situacaoFiltro');
            var dataInicialSalva = localStorage.getItem('dataInicialFiltro');
            var dataFinalSalva = localStorage.getItem('dataFinalFiltro');
            var tipoDataSalvo = localStorage.getItem('tipoDataFiltro');

            if (tipoSalvo && tipoSalvo != 'null') {
                $('#filtrar-tipo').val(tipoSalvo);
            } else {
                $('#filtrar-tipo').val('todos');
            }
            if (situacaoSalva && situacaoSalva != 'null') {
                $('#filtrar-situacao').val(situacaoSalva);
            } else {
                $('#filtrar-tipo').val('todos');
            }
            if (dataInicialSalva) {
                $('#filtrar-datai').val(dataInicialSalva);
            }
            if (dataFinalSalva) {
                $('#filtrar-dataf').val(dataFinalSalva);
            }
            if (tipoDataSalvo) {
                $('#filtrar-tipo-data').val(tipoDataSalvo);
            }
        }

        function aplicarFiltros() {
            var tipoFiltro = $('#filtrar-tipo').val();
            var situacaoFiltro = $('#filtrar-situacao').val();
            var dataInicial = $('#filtrar-datai').val();
            var dataFinal = $('#filtrar-dataf').val();
            var tipoDataFiltro = $('#filtrar-tipo-data').val();

            $('.check-contas:checked').prop('checked', false);
            $('#check-all:checked').prop('checked', false);

            // Salva os filtros escolhidos no localStorage
            localStorage.setItem('tipoFiltro', tipoFiltro);
            localStorage.setItem('situacaoFiltro', situacaoFiltro);
            localStorage.setItem('dataInicialFiltro', dataInicial);
            localStorage.setItem('dataFinalFiltro', dataFinal);
            localStorage.setItem('tipoDataFiltro', tipoDataFiltro);

            // Verifica se a data inicial é maior ou igual à data final
            if (dataInicial && dataFinal) {
                var dataIniMoment = moment(dataInicial, 'YYYY-MM-DD');
                var dataFimMoment = moment(dataFinal, 'YYYY-MM-DD');

                if (dataIniMoment.isAfter(dataFimMoment)) {
                    alert('A data inicial deve ser menor ou igual a data final.');
                    return; // Interrompe a execução da função
                }
            }

            var totalReceita = 0;
            var totalDespesa = 0;

            $('table.tabela-resumo tbody tr').each(function () {
                $(this).addClass('linhaTela');
                var tipo = $(this).find('td').eq(0).text();
                var baixado = $(this).find('td').eq(9).text();
                var vencimento = $(this).find('td').eq(8).text();
                var lancamento = $(this).find('td').eq(1).text();
                var valorReceita = $(this).find('td').eq(6).text().replace(".", "").replace(",", ".");;
                var valorDespesa = $(this).find('td').eq(7).text().replace(".", "").replace(",", ".");;

                var dataVencimento = moment(vencimento, 'DD/MM/YYYY');
                var dataLancamento = moment(lancamento, 'DD/MM/YYYY');
                var dataIni = dataInicial ? moment(dataInicial, 'YYYY-MM-DD') : null;
                var dataFim = dataFinal ? moment(dataFinal, 'YYYY-MM-DD') : null;

                var mostrar = true;

                // Filtragem por tipo
                if (tipoFiltro !== 'todos') {
                    if (tipoFiltro === 'receita' && tipo !== 'receita') {
                        mostrar = false;
                    } else if (tipoFiltro === 'despesa' && tipo !== 'despesa') {
                        mostrar = false;
                    }
                } else {
                    $(".check-contas").prop('disabled', true);
                    $("#check-all").prop('disabled', true);
                }

                // Filtragem por situação
                var statusBaixado = (baixado === 'S') ? 'baixado' : (baixado === 'N' || baixado === '') ? 'aberto' : '';
                if (situacaoFiltro !== 'todos' && situacaoFiltro !== statusBaixado) {
                    mostrar = false;
                }

                // Verifica o tipo de data selecionada
                if (tipoDataFiltro === 'datacad') {
                    if (dataIni && dataFim && (dataLancamento.isBefore(dataIni) || dataLancamento.isAfter(dataFim))) {
                        mostrar = false;
                    }
                } else if (tipoDataFiltro === 'dataven') {
                    if (dataIni && dataFim && (dataVencimento.isBefore(dataIni) || dataVencimento.isAfter(dataFim))) {
                        mostrar = false;
                    }
                }

                if (mostrar) {
                    $(this).show();
                    $(this).addClass('linhaTela');
                    if (valorReceita) {
                        totalReceita += parseFloat(valorReceita.replace(/[^\d,.-]/g, '').replace(',', '.'));
                    }
                    if (valorDespesa) {
                        totalDespesa += parseFloat(valorDespesa.replace(/[^\d,.-]/g, '').replace(',', '.'));
                    }
                } else {
                    $(this).hide();
                    $(this).removeClass('linhaTela');
                }
            });
            atualizarTotal();
            verificarCheckboxes();
        }

        function verificarCheckboxes() {
            var tipoSelecionado = $('#filtrar-tipo').val();
            var situacaoSelecionada = $('#filtrar-situacao').val();

            if (situacaoSelecionada != 'todos' && tipoSelecionado !== 'todos') {
                $('.check-contas').prop('disabled', false);
                $('#check-all').prop('disabled', false);
            } else {
                $('.check-contas:checked').prop('checked', false);
                $('.check-contas').prop('disabled', true);
                $('#check-all').prop('checked', false);
                $('#check-all').prop('disabled', true);
            }
        }

        $('#filtrar-tipo, #filtrar-situacao').on('change', function () {
            verificarCheckboxes();
            atualizarTotal();
        });

        function atualizarTotal() {
            var total = 0;
            var quantidade = 0;

            // Percorre cada checkbox marcado
            $('.check-contas:checked').each(function () {
                quantidade++;
                // Verifica se é uma receita ou despesa
                var linha = $(this).closest('tr'); // Encontra a linha associada ao checkbox
                var valorReceita = linha.find('td.moedareal').first().text().replace(/\./g, '').replace(',', '.'); // Extrai o valor de receita
                var valorDespesa = linha.find('td.moedareal').last().text().replace(/\./g, '').replace(',', '.'); // Extrai o valor de despesa

                // Verifica qual tipo de valor foi encontrado e soma ao total
                if (valorReceita) {
                    total += parseFloat(valorReceita);
                } else if (valorDespesa) {
                    total += parseFloat(valorDespesa);
                }
            });

            // Atualiza o valor total formatado usando a função formatarValor
            $('#total-sum').text(formatarValor(total));
            $('#quantidade-marcada').text(quantidade);

            var tipoSelecionado = $('#filtrar-tipo').val();
            var situacaoSelecionada = $('#filtrar-situacao').val();

            if (quantidade == 0) {
                $("#btnBaixar").prop('disabled', true);
                $("#btnExcluir").prop('disabled', true);
                $("#btnEstornar").prop('disabled', true);
            } else if (quantidade > 0 && tipoSelecionado != 'todos' && situacaoSelecionada == 'baixado') {
                $("#btnEstornar").prop('disabled', false);
            } else if (quantidade > 0 && tipoSelecionado != 'todos' && situacaoSelecionada == 'aberto') {
                $("#btnBaixar").prop('disabled', false);
                $("#btnExcluir").prop('disabled', false);
            }
        }



        // Dispara a função sempre que um checkbox é marcado/desmarcado
        $('.check-contas').on('change', function () {
            var allChecked = $('.check-contas:visible').length === $('.check-contas:visible:checked').length;
            $('#check-all').prop('checked', allChecked);
            atualizarTotal();
        });

        $(document).on('change', '#check-all', function () {
            var isChecked = $(this).is(':checked');
            $('.check-contas:visible').prop('checked', isChecked);
            atualizarTotal();
        });

        // Carrega os filtros salvos quando a página é carregada
        carregarFiltros();

        // Chama a função ao alterar os filtros
        $('#filtrar-tipo, #filtrar-situacao, #filtrar-tipo-data').on('change', function () {
            aplicarFiltros();
        });

        // Chama a função ao clicar no botão de filtrar
        $('#filtrar-periodo').on('click', function () {
            aplicarFiltros();
        });

        filtrarLista('#filtrarPag', '#pag-rec', '.linhaTela');

        $('#filtrar-periodo').trigger('click');
    });

    $(document).ready(function () {
        $('.toggle-password').click(function () {
            var target = $(this).data('target');
            var input = $(target);
            var icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        tableSorter("#ent-list");
        tableSorter("#ent-list-inativos");
        tableSorter("#pag-rec", {
            headers: {
                6: { sorter: 'number' },
                7: { sorter: 'number' }
            }
        });

        if ($('#os-tipo').length) {
            tableSorter("#ordens-list", {
                headers: {
                    2: { sorter: 'data-status' },
                    5: { sorter: 'datetimeBR', empty: 'bottom' },
                    7: { sorter: 'number' }
                }
            });
        } else {
            tableSorter("#ordens-list", {
                headers: {
                    2: { sorter: 'data-status' },
                    4: { sorter: 'datetimeBR', empty: 'bottom' },
                    6: { sorter: 'number' }
                }
            });
        }

        tableSorter("#materiais-list");
        tableSorter("#obras-list");
        tableSorter("#observacoes-list");
        tableSorter("#servico-list");
        tableSorter("#setor-list");
        tableSorter("#custogeral-list");
        tableSorter("#operacao-list");
        tableSorter("#plconta-list");
        tableSorter("#turno-list");
        tableSorter("#arq-tab");
        tableSorter("#equip-list");
    });




    $(document).ready(function () {
        var currentUrl = window.location.href;

        // Itera sobre todos os links do submenu
        $('.submenu a').each(function () {
            var link = $(this).attr('href');

            // Verifica se a URL atual corresponde ao link do submenu
            if (currentUrl === link) {
                $(this).addClass('active'); // Adiciona a classe 'active' ao link do submenu
                $(this).closest('.submenu').show(); // Mantém o submenu aberto
                $(this).closest('.sidebar-item-pai').addClass('active'); // Adiciona a classe 'active' ao item pai
            }
        });

        $('.sidebar-item a').each(function () {
            var link = $(this).attr('href');

            if (currentUrl === link) {
                $(this).addClass('active'); // Adiciona a classe 'active' ao link direto
                $(this).closest('.sidebar-item').addClass('active'); // Adiciona a classe 'active' ao item
            }
        });

        // Clique no item-pai para abrir/fechar submenu
        $('.sidebar-item-pai > a').on('click', function (e) {
            e.preventDefault(); // Prevenir o comportamento padrão do link

            var $submenu = $(this).siblings('.submenu'); // Selecionar o submenu

            // Mostrar ou esconder o submenu com animação de slide
            $submenu.slideToggle(300);

            // Alternar a classe 'active' no menu clicado
            $(this).parent().toggleClass('active');
        });

        $('#abrirModalPag').click(function () {
            $('#form-contaspag')[0].reset();
            $('#campos-parcelas-pag').empty();
            $('#modalPag').modal('show');
        });

        $('#abrirModalRec').click(function () {
            $('#form-contasrec')[0].reset();
            $('#campos-parcelas-rec').empty();
            $('#modalRec').modal('show');
        });
        //$('#abrirModalPag').trigger('click');

        modovencimento('#switch-intervalo-pag', '#intervalo-pag');
        modovencimento('#switch-intervalo-rec', '#intervalo-rec');

        function modovencimento(label, input) {
            $(label).on('click', function () {
                var labelText = $(this).contents().filter(function () {
                    return this.nodeType === 3; // Seleciona apenas o nó de texto (tipo 3)
                });

                // Alterna o texto do nó de texto
                if (labelText.text().trim() === 'Intervalo') {
                    labelText.replaceWith('Data');
                } else {
                    labelText.replaceWith('Intervalo');
                }
                $(input).trigger('input');
            });
        }

        disabledbutton('#form-novaos', '#novaos-submit');
        disabledbutton('#form-ordens', '#grava-ordem');
        disabledbutton('#form-novocli', '#novocli-submit');
        disabledbutton('#form-contas', '#edit-submit');
        disabledbutton('#form-contas-pag', '#modal-pag-submit');
        disabledbutton('#form-contas-rec', '#modal-rec-submit');

        /**
         * FUNÇÃO PARA DESABILITAR O BOTÃO DE SUBMIT APÓS O PRIMEIRO CLIQUE
         * EVITA ENVIOS DUPLICADOS
         * @param {*} form
         * @param {*} button
         */
        function disabledbutton(form, button) {
            $(form).on('submit', function (e) {
                e.preventDefault();
                var $submitButton = $(button);
                $submitButton.prop('disabled', true);
                setTimeout(function () {
                    $submitButton.prop('disabled', false);
                }, 5000);
            });
        }


        $('.parcelas-info').on('mouseenter', function (event) {
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

        $('.parcelas-info').on('mouseleave', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });

        function filtrarEntidade(inputSelector) {
            let status = $("input[name='ent-status']:checked").val();
            let tabelaId = (status === 'ativos' || status === undefined) ? '#ent-list' : '#ent-list-inativos';

            filtrarLista(inputSelector, tabelaId);
        }

        $("#filtrarEnt").on('input', function () {
            filtrarEntidade('#filtrarEnt');
        });

        $(document).on("change", "input[name='ent-status']", function () {
            let status = $(this).val();
            if (status === "ativos") {
                $(".ent-ativos").show();
                $(".ent-inativos").hide();
            } else {
                $(".ent-ativos").hide();
                $(".ent-inativos").show();
            }

            filtrarEntidade("#filtrarEnt");
        });

        // Inicializa com "ativos" selecionado
        $("input[name='ent-status'][value='ativos']").prop("checked", true).trigger("change");


        filtrarLista('#filtrarServico', '#servico-list');
        filtrarLista('#filtrarObras', '#obras-list');
        filtrarLista('#filtrarMateriais', '#materiais-list');
        filtrarLista('#filtrarCustogeral', '#custogeral-list');
        filtrarLista('#filtrarOperacao', '#operacao-list');
        filtrarLista('#filtrarOrdens', '#ordens-list');
        filtrarLista('#filtrarSetor', '#setor-list');
        filtrarLista('#filtrarTipo', '#tipo-list');
        filtrarLista('#filtrarPlconta', '#plconta-list');
        filtrarLista('#filtrarEquip', '#equip-list');
        // Chamando a função para cada lista
        filtrarLista("#filtrar", "#sett_usr_lst");
        filtrarLista("#filtrarFunc", "#func_lst");
        filtrarLista("#filtrarSrvModal", "#srv_lst");
        filtrarLista("#filtrarObservacoes", "#observacoes-list");
        filtrarLista("#filtrarEmp1", "#emp1-list");
        filtrarLista("#filtrarEmp2", "#emp2-list");
        filtrarLista("#filtrarCliModal", "#cli_lst");
        filtrarLista("#filtrarObraListModal", "#obra_lst");
        filtrarLista("#filtrarOprModal", "#opr_lst");
        filtrarLista("#filtrarEqpModalMedicao", "#eqpmed_list");

        $('#inptcpf, #inptcnpj, .cli-form, .func-form, .port-form').hide();

        $('#equipamento').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue === 'VEÍCULO') {
                $('.linha-geral').hide();
                $('.linha-veiculo').show();
            } else {
                $('.linha-geral').show();
                $('.linha-veiculo').hide();
            }
        });

        $('#equipamento').trigger('change');

        $('#ent_fisjur').change(function () {
            var selectedValue = $(this).val();
            if (selectedValue == '1') {
                $('label[for="ent_cpfcnpj"]').text('CPF:');
                $('label[for="ent_inscrg"]').text('RG:');
                $('label[for="ent_nome"]').text('Nome:');
                $('label[for="ent_fantasia"]').text('Apelido:');
                $('#inptcnpj').hide();
                $('#inptcpf').show();
            } else if (selectedValue == '2') {
                $('label[for="ent_cpfcnpj"]').text('CNPJ:');
                $('label[for="ent_inscrg"]').text('Inscrição Estadual:');
                $('label[for="ent_nome"]').text('Razão Social:');
                $('label[for="ent_fantasia"]').text('Fantasia:');
                $('#inptcpf').hide();
                $('#inptcnpj').show();
            }
        });

        $('#ent_fisjur').trigger('change');

        $('#ent_tipo').change(function () {
            var selectedValue = $(this).val();

            // Esconde todas as divs inicialmente
            $('.cli-form, .func-form, .port-form').hide();

            // Mostra a div correspondente com base no valor selecionado
            if (selectedValue == '1' || selectedValue == '2') {
                $('.cli-form').show();
            } else if (selectedValue == '3') {
                $('.func-form').show();
            } else if (selectedValue == '4') {
                $('.port-form').show();
            }
        });

        $('#ent_tipo').trigger('change');

        function showTooltip(element) {
            $(element).css({ opacity: 1, visibility: 'visible' });

            // Oculta o tooltip após 4 segundos
            setTimeout(function () {
                $(element).css({ opacity: 0, visibility: 'hidden' });
            }, 4000);
        }

        // Verifica CPF
        $("#ent_cpf").on("blur", function () {
            let cpf = $(this).val();
            let url = $(this).data('url');
            let ent = $("#ent_tipo").val();

            if (cpf.length > 3) {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: { valor: cpf, tipo: "cpf", ent: ent },
                    success: function (response) {
                        if (response === "existente") {
                            showTooltip("#tooltipCpf");
                        }
                    }
                });
            }
        });

        // Verifica CNPJ
        $("#ent_cnpj").on("blur", function () {
            let cnpj = $(this).val();
            let url = $(this).data('url');
            let ent = $("#ent_tipo").val();

            if (cnpj.length > 3) {
                $.ajax({
                    url: url,
                    method: "POST",
                    data: { valor: cnpj, tipo: "cnpj", ent: ent },
                    success: function (response) {
                        if (response === "existente") {
                            showTooltip("#tooltipCnpj");
                        }
                    }
                });
            }
        });

        $('#parcelas-rec, #intervalo-rec, #parcelas-pag, #intervalo-pag').on('input', function () {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });

        function calcularSomaParcelas(total, inputAtivo, modalSelector) {
            var somaDesbloqueados = 0;
            var somaTravados = 0;
            var contagemDesbloqueados = 0;
            var inputsDesbloqueados = [];
            var campoIntervalo = modalSelector === "#modalRec" ? "#intervalo-rec" : "#intervalo-pag";
            var totalpracalculos = parseFloat(total.replace(/\./g, "").replace(",", "."));
            if (isNaN(totalpracalculos)) {
                alert("Valor total inválido.");
                return;
            }

            $(modalSelector + ' .input-parcela input').each(function () {
                var valorParcela = $(this).val().replace(/\./g, "").replace(",", ".");
                var valorNumerico = parseFloat(valorParcela);

                if (!isNaN(valorNumerico)) {
                    if (this === inputAtivo) {
                        somaTravados += valorNumerico;
                    } else if ($(this).hasClass('desbloqueado')) {
                        somaDesbloqueados += valorNumerico;
                        contagemDesbloqueados++;
                        inputsDesbloqueados.push(this);
                    } else if ($(this).hasClass('travado')) {
                        somaTravados += valorNumerico;
                    }
                }
            });

            if (somaTravados > totalpracalculos) {
                alert('A soma dos valores travados ultrapassa o valor total permitido!');
                $(campoIntervalo).trigger("input");
                return;
            }

            var reparcelar = totalpracalculos - somaTravados;
            if (contagemDesbloqueados > 0) {
                var valorPorParcela = reparcelar / contagemDesbloqueados;
                var resultados = [];
                var somaArredondada = 0;

                for (let i = 0; i < contagemDesbloqueados; i++) {
                    var valorArredondado = parseFloat(valorPorParcela.toFixed(2));
                    resultados.push(valorArredondado);
                    somaArredondada += valorArredondado;
                }

                var diferenca = reparcelar - somaArredondada;
                resultados[contagemDesbloqueados - 1] += diferenca;

                inputsDesbloqueados.forEach(function (input, index) {
                    var valorFinal = resultados[index];
                    var valorFormatado = formatarValor(valorFinal);
                    $(input).val(valorFormatado);
                });
            }
        };

        gerarParcelas("#documento-pag", "#vtotal-pag", "#parcelas-pag", "#intervalo-pag", "#datacad-pag", "#campos-parcelas-pag", "#entrada-pag", "#switch-intervalo-pag");
        gerarParcelas("#documento-rec", "#vtotal-rec", "#parcelas-rec", "#intervalo-rec", "#datacad-rec", "#campos-parcelas-rec", "#entrada-rec", "#switch-intervalo-rec");

        function gerarParcelas(doc, vtotal, parcelas, campointervalo, datacad, camposparcelas, campoEntrada, label) {
            var camposInput = doc + ', ' + vtotal + ', ' + parcelas + ', ' + campointervalo + ', ' + campoEntrada;

            $(camposInput).on('input', function () {
                var valorTotal = $(vtotal).val().replace(/[.,]/g, ''); // Obtém o valor total
                var qtdParcelas = $(parcelas).val(); // Obtém a quantidade de parcelas
                var intervalo = parseInt($(campointervalo).val()); // Obtém o intervalo em dias
                var diasEntrada = parseInt($(campoEntrada).val()); // Quantidade de dias para a primeira parcela
                var dataInicial = new Date(); // Usa a data atual
                var documento = $(doc).val();

                // Limpa os campos de parcelas anteriores
                $(camposparcelas).empty();

                // Verifica se todos os campos têm valores válidos
                if (!isNaN(valorTotal) && !isNaN(qtdParcelas) && qtdParcelas > 0 && !isNaN(intervalo) && !isNaN(diasEntrada)) {
                    valorTotal = parseFloat(valorTotal) / 100; // Converte para número correto
                    var valoresParcelas = [];
                    var somaArredondada = 0;

                    // Distribui os valores das parcelas
                    for (var i = 1; i <= qtdParcelas; i++) {
                        var valorParcela = (valorTotal / qtdParcelas).toFixed(2); // Calcula e arredonda
                        valoresParcelas.push(parseFloat(valorParcela)); // Armazena o valor
                        somaArredondada += parseFloat(valorParcela); // Acumula a soma
                    }

                    // Ajusta a última parcela para compensar qualquer diferença
                    var diferenca = valorTotal - somaArredondada;
                    valoresParcelas[qtdParcelas - 1] += parseFloat(diferenca.toFixed(2)); // Ajusta a última parcela

                    // Calcula a data da primeira parcela
                    var vencimentoPrimeiraParcela = new Date(dataInicial);

                    // Verifica o texto da label para determinar o comportamento do vencimento
                    var textoLabel = $(label).text().trim().toLowerCase();
                    if (textoLabel === 'data') {
                        var diaVencimento = parseInt($(campointervalo).val());

                        // Verifica se o dia de vencimento é anterior ou igual ao dia atual
                        if (diaVencimento <= dataInicial.getDate()) {
                            // Se for, avança para o próximo mês
                            vencimentoPrimeiraParcela.setMonth(vencimentoPrimeiraParcela.getMonth() + 1);
                        }

                        // Define o dia de vencimento para o valor especificado
                        vencimentoPrimeiraParcela.setDate(diaVencimento);
                    } else {
                        // Parcelas subsequentes respeitam o intervalo a partir da data da primeira parcela
                        vencimentoPrimeiraParcela.setDate(vencimentoPrimeiraParcela.getDate() + diasEntrada);
                    }

                    for (var i = 1; i <= qtdParcelas; i++) {
                        var vencimento = new Date(vencimentoPrimeiraParcela); // Começa com a data da primeira parcela

                        if (textoLabel === 'data') {
                            vencimento.setMonth(vencimento.getMonth() + (i - 1)); // Aumenta o mês para parcelas subsequentes
                        } else {
                            // Parcelas subsequentes respeitam o intervalo
                            if (i > 1) {
                                vencimento.setDate(vencimento.getDate() + (intervalo * (i - 1)));
                            }
                        }

                        $(camposparcelas).append(
                            '<div class="parcela-container fcad-form-row">' +
                            '<div class="fcad-form-group coluna50">' +
                            '<label class="pagtitulolab">_________</label>' +
                            '<input class="pagtitulo2" type="text" value="' + documento + '/' + i + '-' + qtdParcelas + '" name="parctitulo_' + i + '" readonly>' +
                            '</div>' +
                            '<div class="fcad-form-group">' +
                            '<label>Parcela ' + i + ':</label>' +
                            '<div class="input-parcela">' +
                            '<input class="desbloqueado mask-money" name="parc_' + i + '" type="text" value="' + (valoresParcelas[i - 1]).toFixed(2).replace(".", ",") + '">' +
                            '<i class="fa fa-unlock cadeado"></i>' + // Ícone de cadeado aberto
                            '</div>' +
                            '</div>' +
                            '<div class="fcad-form-group">' +
                            '<label>Vencimento:</label>' +
                            '<input type="date" value="' + vencimento.toISOString().split('T')[0] + '" name="parcven_' + i + '">' +
                            '</div>' +
                            '</div>'
                        );
                    }

                    // Alterna entre cadeado aberto e fechado ao clicar no ícone
                    $('.cadeado').on('click', function () {
                        var input = $(this).siblings('input');
                        if ($(this).hasClass('fa-unlock')) {
                            $(this).removeClass('fa-unlock').addClass('fa-lock');
                            input.removeClass('desbloqueado').addClass('travado');
                        } else {
                            $(this).removeClass('fa-lock').addClass('fa-unlock');
                            input.removeClass('travado').addClass('desbloqueado');
                        }
                    });

                    // Chama a função de soma e ajuste quando o input perde o foco
                    $('.input-parcela input').on('blur', function () {
                        var total = $(vtotal).val();
                        var modalSelector = "#modalPag";
                        if (camposparcelas != "#campos-parcelas-pag") {
                            modalSelector = "#modalRec";
                        }
                        calcularSomaParcelas(total, this, modalSelector);
                    });

                    $(".mask-money").mask('000.000.000.000.000,00', { reverse: true, placeholder: "0,00" });
                }
            });
        }

        $('#parcelas-rec, #intervalo-rec, #entrada-rec, #parcelas-pag, #intervalo-pag, #entrada-pag').prop('disabled', true);

        $('#documento-rec').on('input', function () {
            var documentoVal = $(this).val().trim();

            // Habilita ou desabilita os campos de parcelas e intervalo baseado no preenchimento do campo documento
            if (documentoVal !== '') {
                $('#parcelas-rec, #intervalo-rec, #entrada-rec').prop('disabled', false);
            } else {
                $('#parcelas-rec, #intervalo-rec, #entrada-rec').prop('disabled', true);
            }
        });

        $('#documento-pag').on('input', function () {
            var documentoVal = $(this).val().trim();

            // Habilita ou desabilita os campos de parcelas e intervalo baseado no preenchimento do campo documento
            if (documentoVal !== '') {
                $('#parcelas-pag, #intervalo-pag, #entrada-pag').prop('disabled', false);
            } else {
                $('#parcelas-pag, #intervalo-pag, #entrada-pag').prop('disabled', true);
            }
        });

    });



    //FORMULÁRIO MODAL LANÇAMENTOS
    $(document).ready(function () {

        $('table.tabela-resumo tbody tr').each(function () {
            var despesa = $(this).find('td:nth-child(6)').text().trim(); // Coluna Despesa
            var receita = $(this).find('td:nth-child(5)').text().trim(); // Coluna Receita

            if (despesa !== "") {
                $(this).addClass('fluxo-negativo'); // Letra vermelha se Despesa estiver preenchida
            } else if (receita !== "") {
                $(this).addClass('fluxo-positivo'); // Letra azul se Receita estiver preenchida
            }
        });

        selectByCod('#pag_cod-for', '#pag_fornecedor');
        selectByCod('#pag_cod-port', '#pag_portador');
        selectByCod('#pag_cod-oper', '#pag_operacao');
        selectByCod('#pag_cod-plconta', '#pag_plconta');

        function selectByCod(input, select) {
            // Quando o valor do campo Cód.For. for alterado
            $(input).on('blur', function () {
                var codigo = $(this).val(); // Pega o valor digitado no input de Cód.For.
                var found = false; // Variável para verificar se o código foi encontrado

                var option = select + ' option';
                // Procura a opção no select de Fornecedor que tenha o mesmo valor que o código digitado
                $(option).each(function () {
                    if ($(this).val() == codigo) {
                        $(this).prop('selected', true); // Seleciona a opção correspondente
                        found = true; // Marca que o código foi encontrado
                        return false; // Encerra o loop
                    }
                });

                // Se o código não for encontrado, seleciona a opção com valor "0"
                if (!found) {
                    $(select).val('0');
                }
            });

            $(select).on('change', function () {
                var selectedCode = $(this).val(); // Pega o código da opção selecionada

                // Atualiza o valor do input Cód.For. com o código selecionado
                $(input).val(selectedCode);
            });
        }

        // Abre a modal ao clicar no botão
        $('.abrirModalEdit').on('click', function () {

            // Cria a instância do modal do Bootstrap
            var modalEdit = new bootstrap.Modal(document.getElementById('modalEdit'), {
                backdrop: 'static',
                keyboard: false
            });

            let id = $(this).data('id');
            let url = $(this).data('url');
            let tipo = $(this).data('tipo');
            let btnSrcPlconta = $("#modalEdit").find("#btn-plconta");

            if (tipo === 'receita') {
                btnSrcPlconta.data('div', 'plcontarec-div');
            } else if (tipo === 'despesa') {
                btnSrcPlconta.data('div', 'plcontapag-div');
            }

            $('#form-contas')[0].reset();
            $('#form-contas select').each(function () {
                $(this).val($(this).find('option:first').val());
            });
            $('.fornEdt, .cliEdt, .autorizanteEdt').prop('hidden', true);
            $('#fornecedor-edit, #cliente-edit').removeAttr('required');

            $.ajax({
                type: 'POST',
                url: url,
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    $('#modalEdit').removeClass('border-red');
                    $('#id-edit').val(response.id);
                    $('#tipo-edit').val(tipo);
                    $('#titulo-edit').val(response.titulo);
                    $('#dataven-edit').val(response.dataven);
                    $('#documento-edit').val(response.documento);
                    $('#competencia-edit').val(response.competencia);
                    $('#plconta-edit').val(response.id_plconta);

                    $('#plconta-edit option').each(function () {
                        var optionTipo = $(this).data('tipo');
                        if ((tipo === 'receita' && optionTipo !== 'R') || (tipo === 'despesa' && optionTipo !== 'D')) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });

                    $('#plconta-edit').val(response.id_plconta);

                    if (tipo == 'despesa') {
                        $('.fornEdt').prop('hidden', false);
                        $('#fornecedor-edit').val(response.id_entf).attr('required', true);
                        $('.autorizanteEdt').prop('hidden', false);
                        $('#autorizante-edit').val(response.autorizante);
                        $('#modalEdit').addClass('border-red');
                    } else if (tipo == 'receita') {
                        $('.cliEdt').prop('hidden', false);
                        $('#cliente-edit').val(response.id_entc).attr('required', true);
                    }

                    $('#operacao-edit').val(response.id_oper);
                    $('#obs1-edit').val(response.obs1);
                    $('#obs2-edit').val(response.obs2);
                    $('#valor-edit').val(response.valor);
                    $('#vdesc-edit').val(response.vdesc);
                    $('#voutros-edit').val(response.voutros);
                    $('#vparcial-edit').val(response.vparcial);
                    $('#saldo-edit').val(response.saldo);

                    var saldoTable = $('#saldo-table tbody');
                    saldoTable.empty();

                    if (response.tabelasaldo && response.tabelasaldo.length > 0) {
                        response.tabelasaldo.forEach(function (item) {
                            saldoTable.append(`
                        <tr data-id="${item.id}" data-tipo="${tipo}">
                            <td>${item.valor}</td>
                            <td>${item.vpago}</td>
                            <td>${item.voutros}</td>
                            <td>${item.vdesc}</td>
                            <td>${item.saldo}</td>
                            <td>${item.datapag}</td>
                            <td><input type="checkbox" class="check-baixa" /></td>
                        </tr>
                    `);
                        });
                    } else {
                        saldoTable.append('<tr><td colspan="6">Nenhuma baixa para esse título.</td></tr>');
                    }

                    // **Abre a modal corretamente**
                    modalEdit.show();
                    $('.accordion-header').blur();
                },
                error: function () {
                    alert('Erro ao buscar dados.');
                }
            });

            // Fecha a modal usando Bootstrap
            $('#modalEdit .btn-close, #modalEdit .close').on('click', function () {
                modalEdit.hide();
            });
        });
    });

    function toggleBotaoEstorno() {
        let temMarcado = $("#saldo-table tbody .check-baixa:checked").length > 0;
        $("#btn-estorna-parcial").toggle(temMarcado);
    }

    $(document).on("change", "#saldo-todos", function () {
        let marcado = $(this).is(':checked');
        $("#saldo-table tbody .check-baixa").prop('checked', marcado);
        toggleBotaoEstorno();
    });

    // se o usuário desmarcar algum checkbox, desmarca o "selecionar todos"
    $(document).on('change', '#saldo-table tbody .check-baixa', function () {
        if (!$(this).is(':checked')) {
            $('#saldo-todos').prop('checked', false);
        } else {
            // se todos os checkboxes individuais estiverem marcados, marca o "selecionar todos"
            let todosMarcados = true;
            $('#saldo-table tbody .check-baixa').each(function () {
                if (!$(this).is(':checked')) {
                    todosMarcados = false;
                    return false; // sai do loop
                }
            });
            $('#saldo-todos').prop('checked', todosMarcados);
        }
        toggleBotaoEstorno();
    });

    $("body").on('change', '.os1-status, .os1-status-modal', function () {
        var os1Status = $(this).val();
        var status = $(this).data('status');
        var agendaModal = false;

        if ($(this).is('.os1-status-modal')) {
            var agendaModal = true;
        } else {
            var agendaModal = false;
        }

        if (os1Status == '7') {

            if (confirm('Deseja realmente cancelar essa OS?')) {
                var os = $(this).data('os1');
                var url = $(this).data('url');
                var load = $(".ajax_load");

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        os: os,
                        status: os1Status,
                        cancelar: true,
                        agendaModal: agendaModal
                    },
                    dataType: 'json',
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

                    },
                    error: function (xhr, status, error) {
                        ajaxMessage(ajaxResponseRequestError, 5);
                        load.fadeOut();
                    }
                });
            } else {
                $(this).val(status);
            }
        }
    });


    // MAKS

    $(".mask-number").mask('000000');
    $(".mask-date").mask('00/00/0000');
    $(".mask-daymonth").mask('00/00');
    $(".mask-datetime").mask('00/00/0000 00:00');
    $(".mask-month").mask('00/0000', { reverse: true });
    $(".mask-doc").mask('000.000.000-00', { reverse: true });
    $(".mask-fone").mask('(00) 0000-0000');
    $(".mask-cel").mask('(00) 0 0000-0000');
    $(".mask-cnpj").mask('00.000.000/0000-00', { reverse: true });
    $(".mask-cep").mask('00000-000', { reverse: true });
    $(".mask-card").mask('0000  0000  0000  0000', { reverse: true });
    $(".mask-money").mask('000.000.000.000.000,00', { reverse: true, placeholder: "0,00" });
    $(".mask-money2").mask('000000000000000,00', { reverse: true, placeholder: "0,00" });

    $(".mask-money").on('blur', function () {
        var value = $(this).val();

        // Verifica se o valor tem uma vírgula, caso contrário, adiciona ",00"
        if (value && value.indexOf(',') === -1) {
            $(this).val(value + ",00");
        }
    });

    $(document).on('keydown', '.mask-money2', function (e) {
        if (e.key === '-' || e.keyCode === 189 || e.keyCode === 109) {
            e.preventDefault();
        }
    });

    $(document).on('input', '.mask-money2', function () {
        let val = $(this).val();

        // remove qualquer sinal de "-"
        if (val.includes('-')) {
            $(this).val(val.replace(/-/g, ''));
        }
    });

    $(document).on('blur', '.mask-money2', function () {
        let valor = $(this).val();

        // só pra garantir
        valor = valor.replace(/-/g, '');

        // se vazio ou inválido, define como 0,00
        if (!valor.trim()) {
            $(this).val('0,00');
        }
    });


    /**
 * ORDENS DE SERVIÇO
 */

    function toggleIntervalo() {
        if ($('#active-recorrente').is(':checked')) {
            $('#recorrencia-container').show(); // Mostrar campo se o checkbox estiver marcado
        } else {
            $('#recorrencia-container').hide(); // Esconder campo se o checkbox estiver desmarcado
        }
    }

    function toggleRecorrencia() {
        var select = $("#recorrencia");
        var datafixa = $("#datafixa-container");
        var intervalo = $("#intervalo-container");

        if (select.val() == "1") {
            datafixa.hide();
            intervalo.hide();
        } else if (select.val() == "2") {
            datafixa.hide();
            intervalo.hide();
        } else if (select.val() == "3") {
            datafixa.show();
            intervalo.hide();
        } else if (select.val() == "4") {
            datafixa.show();
            intervalo.hide();
        } else if (select.val() == "5") {
            datafixa.show();
            intervalo.hide();
        } else if (select.val() == "6") {
            datafixa.show();
            intervalo.hide();
        } else if (select.val() == "7") {
            datafixa.show();
            intervalo.hide();
        } else {
            datafixa.hide();
            intervalo.hide();
        }
    }

    $(document).ready(function () {

        toggleIntervalo();
        toggleRecorrencia();
        $("#recorrencia").on('change', function () {
            toggleRecorrencia();
        });
        $('#active-recorrente').on('change', function () {
            toggleIntervalo();
        });
        btnDelTarefaToogle();
        btnDelMatToogle();

        novalinha(".novatarefa", "#linha-tarefa", "#container-linhas2", '#tarefaseq', '.deltarefa');
        novalinha(".novomat", "#linha-material", "#container-linhas3", '#materialseq', '.deletemat');
        novalinha(".novatarefa-add", "#linha-aditivo", "#container-linhasaditivo", '#addseq', '.deltarefa-add');

        deleteLinha("#container-linhasaditivo", '#addseq', '.deltarefa-add');
        deleteLinha("#container-linhas2", '#tarefaseq', '.deltarefa');
        deleteLinha("#container-linhas3", '#materialseq', '.deletemat');

        // Ativa a ordenação ao modificar dataexec ou horaexec
        $(document).on('click', '#ordenar', function () {
            ordenarLinhas("#container-linhas2", "#tarefaseq", ".deltarefa");
            recalcularSequencia("#container-linhas2", "#tarefaseq", ".deltarefa");
        });

        $(document).on('click', '#ordenar-add', function () {
            ordenarLinhas("#container-linhasaditivo", "#addseq", ".deltarefa-add");
            recalcularSequencia("#container-linhasaditivo", "#addseq", ".deltarefa-add");
        });

        var urlOrdens = window.location.pathname;

        if (urlOrdens.includes("/ordens/form")) {
            // Ativa as funções ao carregar a página
            ordenarLinhas("#container-linhas2", "#tarefaseq", ".deltarefa");
            recalcularSequencia("#container-linhas2", "#tarefaseq", ".deltarefa");
        }

        function novalinha(botaonovo, idlinha, div, seq, btndeletelinha) {
            $(botaonovo).on('click', function (e) {
                e.preventDefault(); // Evita o comportamento padrão do botão

                // Clona a última linha da tabela
                let novaLinha = $(div + ' .ordens-form:last').clone();
                novaLinha.find('input, textarea').val(''); // Limpa os inputs e textareas da linha clonada
                novaLinha.find('select').prop('selectedIndex', 0); // Reseta os selects para a primeira opção
                if (novaLinha.find("#qtd_servico").length) {
                    novaLinha.find('#qtd_servico').val('1'); // Qtde de Tarefas default
                }
                if (novaLinha.find("#qtd_aditivo").length) {
                    novaLinha.find('#qtd_aditivo').val('1'); // Qtde de Tarefas default
                }
                // Remove a classe "original" da nova linha
                novaLinha.removeClass('original');

                novaLinha.find('.fcad-form-group').removeClass('os2-item-disabled');

                novaLinha.find('select[name^="OS2_servico_"]').removeClass('select-readonly');
                novaLinha.find('select[name^="add_servico_"]').removeClass('select-readonly');

                novaLinha.find('.deltarefa').prop('hidden', false);
                novaLinha.find('.deltarefa-add').prop('hidden', false);

                novaLinha.find('input[name^="OS2_numero_"]').attr('data-status', '0');
                novaLinha.find('input[name^="OS2_numero_"]').css('border', 'none');
                novaLinha.find('input[name^="OS2_numero_"]').closest('.fcad-form-group').find('label').css('color', 'transparent');

                novaLinha.find('input[name^="add_numero_"]').attr('data-status', '0');
                novaLinha.find('input[name^="add_numero_"]').css('border', 'none');
                novaLinha.find('input[name^="add_numero_"]').closest('.fcad-form-group').find('label').css('color', 'transparent');

                novaLinha.find('select[name^="OS2_operador_"]').removeClass('select-readonly');
                novaLinha.find('select[name^="add_operador_"]').removeClass('select-readonly');

                novaLinha.find('select[name^="OS2_recorrencia_"]').data('loaded', true);
                novaLinha.find('select[name^="add_recorrencia_"]').data('loaded', true);

                novaLinha.find(".unidade-servico").text(''); // Limpa o campo de unidade de serviço

                novaLinha.find('.accordion-collapse').removeClass('show').css('height', '0');
                novaLinha.find('[data-bs-toggle="collapse"]').attr('aria-expanded', 'false');

                novaLinha.find('.btn-srv-search').prop('hidden', false);
                novaLinha.find('.btn-srv-novo').prop('hidden', false);

                // Adiciona a nova linha ao final do div
                novaLinha.insertAfter(div + ' .ordens-form:last');

                //* MEDIÇÕES NA NOVA LINHA */
                novaLinha.find('.medicaoOs2').hide();
                novaLinha.find('.btn-os2-medicao').removeData('tarefamedicao');
                novaLinha.find('.btn-os2-medicao').prop('disabled', true);
                novaLinha.find('.btn-os2-medicao').css('color', '#fff');
                novaLinha.find('.btn-os2-medicao').closest(".fcad-form-group").attr('data-tooltip', "Primeiro salve a OS!")
                novaLinha.find('.btn-os2-medicao').closest(".fcad-form-group").addClass('medicao-desabilitado');
                novaLinha.find('.medicao-os2-totalfeito').text('0').css("font-weight", "bold");
                novaLinha.find('.medicao-os2-totalcontratado').text('');

                //* MATERIAIS E EQUIPAMENTOS NA NOVA LINHA */
                novaLinha.find('.item-modal').hide();
                novaLinha.find('.btn-os2-itensModal').data('total', 0);
                novaLinha.find('.btn-os2-itensModal').attr('data-total', 0);
                novaLinha.find('.btn-os2-itensModal').css('color', '#fff');
                novaLinha.find('.btn-os2-itensModal').removeData('tarefa');
                novaLinha.find('.btn-os2-itensModal:not([data-bs-toggle="collapse"])').prop('disabled', true);
                novaLinha.find('.btn-os2-itensModal:not([data-bs-toggle="collapse"])').closest(".fcad-form-group").attr('data-tooltip', "Primeiro salve a OS!");
                novaLinha.find('.btn-os2-itensModal:not([data-bs-toggle="collapse"])').closest(".fcad-form-group").addClass('medicao-desabilitado');

                //* BOTÃO ALTERAR STATUS *//
                novaLinha.find('.btn-os2-att-status').hide();


                novaLinha.find('.recorrencia').hide();
                novaLinha.find('.datafixa').hide();

                initAutoNumeric('.num-decimal2');
                initAutoNumeric('.num-decimal3', 3);

                // Atualiza a sequência e os atributos 'name' de todas as linhas            
                ordenarLinhas("#container-linhas2", seq, btndeletelinha);
                recalcularSequencia(div, seq, btndeletelinha);
            });
            btnDelTarefaToogle();
            btnDelMatToogle();
        }

        $(document).on('input', '.qtde_material, .vunit_material', calculaTotalLinhaMaterial);

        function novalinha2(btn) {
            $('body').on('click', btn, function () {
                var $parentDiv = $(this).closest('.mat-accordion-item'); // Encontra a div pai mais próxima
                var $div = $parentDiv.find('[id^=container-accordion-os2os3-]'); // Pega a div da tarefa específica
                var $novaLinha = $div.find('.ordens-form:last').clone(); // Pega a última linha de material            

                $novaLinha.find('input').val(''); // Limpa os inputs da linha clonada
                $novaLinha.find('select').val(''); // Reseta o valor dos selects

                $novaLinha.insertAfter($div.find('.ordens-form:last')); // Adiciona a nova linha ao final do div

                recalcularSequencia($div, "#materialseq", "");
            });
            btnDelMatToogle();
        }

        novalinha2(".novomattarefa");

        $('[id^=container-accordion-os2os3-]').each(function () {
            var $materialseq = $(this).find('#materialseq');
            var $btnDelete = $(this).find('.deletemattarefa');

            recalcularSequencia($(this), $materialseq, '.deletemattarefa');
        });

        $('body').on('click', '.deletemattarefa', function () {
            var $div = $(this).closest('[id^=container-accordion-os2os3-]');
            $(this).closest('.ordens-form').remove();
            recalcularSequencia($div, "#materialseq", '.deletemattarefa');
        });

        function deleteLinha(div, seq, btndeletelinha) {
            // Evento de exclusão da linha
            $(document).on('click', btndeletelinha, function () {
                // Exclui a linha clicada
                $(this).closest('.ordens-form').remove();

                if (btndeletelinha == '.deltarefa') {
                    ordenarLinhas("#container-linhas2", seq, btndeletelinha);
                }
                recalcularSequencia(div, seq, btndeletelinha);
            });
        }

        // Função para recalcular a sequência de todas as linhas e ajustar os atributos 'name'
        function recalcularSequencia(div, seq, btndeletelinha) {
            // Atualiza a sequência para cada linha existente
            $(div).find('.ordens-form').each(function (index) {
                // Atualiza o valor de sequência
                $(this).find(seq).val(index + 1);
                $(this).find(".divdelete").data('seq', index + 1);

                // Atualiza os atributos 'name' dos inputs e selects
                $(this).find('input, select, textarea').each(function () {
                    let nameAttr = $(this).attr('name');
                    if (nameAttr) {
                        let newName = nameAttr.replace(/_\d+$/, '_' + (index + 1));
                        $(this).attr('name', newName);
                    }
                });

                let newSeq = index + 1;
                $(this).find('[id^="obs-accordion-"]').attr('id', `obs-accordion-${newSeq}`);
                $(this).find('[data-bs-target^="#obs-accordion-"]').attr('data-bs-target', `#obs-accordion-${newSeq}`);
                $(this).find('[aria-controls^="obs-accordion-"]').attr('aria-controls', `obs-accordion-${newSeq}`);
                $(this).find('textarea[name^="OS2_obs_"]').attr('name', `OS2_obs_${newSeq}`);

                if (div == "#container-linhas2") {
                    // Atualiza o atributo 'id' do botão de medição
                    $(this).find('.btn-os2-medicao').each(function () {
                        let idAttr = $(this).attr('id');
                        if (idAttr) {
                            let newId = idAttr.replace(/_\d+$/, '_' + (index + 1));
                            $(this).attr('id', newId);
                        }
                    });
                }

                btnDelTarefaToogle();
                btnDelMatToogle()
            });
        }

        // Função para ordenar as linhas e atualizar sequência
        function ordenarLinhas(div, seq, btndeletelinha) {
            let linha = ".linhatarefa";
            if (div == "#container-linhasaditivo") {
                linha = ".linhaaditivo";
            }
            let linhas = $(div).find(linha);

            // Ordena as linhas por data e hora
            linhas.sort(function (a, b) {
                let dataA = $(a).find('#dataexec').val();
                let horaA = $(a).find('#horaexec').val();
                let dataB = $(b).find('#dataexec').val();
                let horaB = $(b).find('#horaexec').val();

                return new Date(`${dataA} ${horaA}`) - new Date(`${dataB} ${horaB}`);
            });

            // Insere as linhas ordenadas no div e atualiza sequência
            $(div).empty().append(linhas);
            recalcularSequencia(div, seq, btndeletelinha);

            // Atribui a classe 'original' somente à primeira linha
            $(div).find(linha).removeClass('original');
            $(div).find(linha + ':first').addClass('original');

            btnDelTarefaToogle();
            btnDelMatToogle();
        }


        // Chama a função para recalcular a sequência ao carregar a página

        recalcularSequencia("#container-linhas3", '#materialseq', '.deletemat');


        // Função para verificar se a combinação já existe
        function verificarTarefaDuplicada(operador, servico, linhaAtual, container) {
            let duplicado = false;
            let linhaDuplicada = null;

            // Iterar por todas as linhas existentes na div das tarefas
            $(container).each(function () {
                if ($(this).is(linhaAtual)) {
                    return true; // Continuar para a próxima iteração
                }

                var selectOperador = '.selectOperador';
                var selectServico = '.selectServico';

                if (container == "#container-linhasaditivo #linha-aditivo") {
                    selectOperador = '.selectAddOperador';
                    selectServico = '.selectAddServico';
                }

                let operadorExistente = $(this).find(selectOperador).val();
                let servicoExistente = $(this).find(selectServico).val();

                // Verificar se a combinação operador/serviço já existe
                if (operadorExistente == operador && servicoExistente == servico) {
                    duplicado = true;
                    linhaDuplicada = $(this);
                    return false; // Sair do loop
                }
            });

            return duplicado ? linhaDuplicada : false;
        }

        function btnDelTarefaToogle() {
            $("#container-linhas2").find('.linhatarefa').each(function () {
                if ($(this).hasClass('original')) {
                    $(this).find(".deltarefa").hide(); // Esconde o botão na linha .original
                } else {
                    $(this).find(".deltarefa").show(); // Mostra o botão nas outras linhas
                }
            });

            $("#container-linhasaditivo").find('.linhaaditivo').each(function () {
                if ($(this).hasClass('original')) {
                    $(this).find(".deltarefa-add").hide(); // Esconde o botão na linha .original
                } else {
                    $(this).find(".deltarefa-add").show(); // Mostra o botão nas outras linhas
                }
            });
        }

        function btnDelMatToogle() {
            $('.divdelete').each(function () {
                if ($(this).data('seq') == 1) {
                    $(this).find("button").hide(); // Esconde o botão na linha .original
                } else {
                    $(this).find("button").show(); // Mostra o botão nas outras linhas
                }
            });

        }

        function verificarMaterialDuplicado(material, linhaAtual, div) {
            let duplicado = false;
            let linhaDuplicada = null;

            $(div + ' [id^=linha-material]').each(function () {
                if ($(this).is(linhaAtual)) {
                    return true;
                }

                let materialExistente = $(this).find('.selectMaterial').val();

                if (materialExistente == material) {
                    duplicado = true;
                    linhaDuplicada = $(this);
                    return false;
                }
            });

            return duplicado ? linhaDuplicada : false;
        }

        $(document).on('change', '.selectOperador, .selectServico', function () {
            // Captura a linha específica (pai mais próximo do select alterado)
            var linhaAtual = $(this).closest('.ordens-form');

            // Pega os valores dos selects dentro dessa linha
            var operador = linhaAtual.find('.selectOperador').val();
            var servico = linhaAtual.find('.selectServico').val();
            var container = "#container-linhas2 #linha-tarefa";

            var linhaDuplicada = verificarTarefaDuplicada(operador, servico, linhaAtual, container);

            if (linhaDuplicada) {
                alert('O serviço escolhido já foi atribuído para esse operador!');
                linhaAtual.find('.selectServico').val('');
                linhaAtual.find('.selectOperador').val('');
                linhaAtual.find('.selectOperador').focus();
                linhaAtual.find('input[name^="OS2_tempo_"]').val('');
                return;
            }
        });

        $(document).on('change', '.selectAddOperador, .selectAddServico', function () {
            // Captura a linha específica (pai mais próximo do select alterado)
            var linhaAtual = $(this).closest('.ordens-form');

            // Pega os valores dos selects dentro dessa linha
            var operador = linhaAtual.find('.selectAddOperador').val();
            var servico = linhaAtual.find('.selectAddServico').val();
            var container = "#container-linhasaditivo #linha-aditivo";

            var linhaDuplicada = verificarTarefaDuplicada(operador, servico, linhaAtual, container);

            if (linhaDuplicada) {
                alert('O serviço escolhido já foi atribuído para esse operador!');
                linhaAtual.find('.selectAddServico').val('');
                linhaAtual.find('.selectAddOperador').val('');
                linhaAtual.find('.selectAddOperador').focus();
                linhaAtual.find('input[name^="add_tempo_"]').val('');
                return;
            }
        });

        $(document).on('change', '.selectServico', function (e) { //ao alterar o select de serviço
            changeServicos(this, ".linhatarefa", "OS2"); //chama a função pra verificar se existe recorrencia personalizada
            let selectServico = $(this); //pego o select de serviço - o elemento select
            let modais = selectServico.closest(".ordens-form").find(".item-modal");
            if (!selectServico || selectServico.val() === "" || selectServico.val() === "0" || selectServico.val() === null) {
                modais.each(function () {
                    $(this).hide();
                });
            } else {
                modais.each(function () {
                    $(this).show();
                });
            }

            if (e.originalEvent) { // Verifica se o evento foi disparado manualmente pelo usuário
                preencheDataLegal(this);
            }
        });

        $(document).on('change', '.selectAddServico', function (e) { //ao alterar o select de serviço
            changeServicos(this, ".linhaaditivo", "add"); //chama a função pra verificar se existe recorrencia personalizada
            let selectServico = $(this); //pego o select de serviço - o elemento select
            let modais = selectServico.closest(".ordens-form").find(".item-modal");
            if (!selectServico || selectServico.val() === "" || selectServico.val() === "0" || selectServico.val() === null) {
                modais.each(function () {
                    $(this).hide();
                });
            } else {
                modais.each(function () {
                    $(this).show();
                });
            }

            if (e.originalEvent) { // Verifica se o evento foi disparado manualmente pelo usuário
                preencheDataLegal(this);
            }
        });

        function preencheDataLegal(select) {
            let selectClass = $(select).attr('class'); // Obtém a classe do elemento select            

            let nome = "add";
            let linha = ".linhaaditivo";
            if (selectClass == "selectServico") {
                nome = "OS2";
                linha = ".linhatarefa";
            }

            let selectedOption = $(select).find('option:selected');
            let dataLegal = selectedOption.data('datalegal');
            let inputField = $(select).closest(linha).find('input[name^="' + nome + '_datalegal_"]');

            if (dataLegal !== undefined) {
                inputField.val(dataLegal); // Preenche o input com o valor do data-datalegal
            } else {
                inputField.val(''); // Limpa o campo se não houver data-datalegal
            }
        }

        function changeServicos(select, linha, name) {
            let selectServico = $(select); //pego o select de serviço - o elemento select
            let servicoSelecionado = $(select).find('option:selected').val(); //pego o serviço selecionado            
            let cliente = $("#cliente-os").find('option:selected').val(); // pego o cliente selecionado
            if (!cliente && selectServico.is(":focus")) {
                let tooltipText = "Por favor, selecione um cliente antes de escolher o serviço.";
                const tooltipDiv = $('<div class="tooltip-text"></div>').html(tooltipText);

                $('body').append(tooltipDiv);

                const rect = selectServico[0].getBoundingClientRect();
                tooltipDiv.css({
                    top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 5,
                    left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2,
                    opacity: 1,
                    visibility: 'visible'
                });

                setTimeout(function () {
                    tooltipDiv.remove();
                }, 2000); // Remove o tooltip após 3 segundos

                return; // Interrompe a execução se o cliente não estiver selecionado
            }
            let url = $(select).data('url'); //pego a url do select de serviço 'recorrencias/verifica'
            let tarefaRow = $(select).closest($(linha)); // Encontra a linha da tarefa            

            if (cliente !== "" && servicoSelecionado !== "") { // Verifica se ambos os selects têm valor válido            
                $.ajax({ //faço uma requisição ajax pra buscar no banco de dados se existe recorrencia personalizada pra esse cliente e serviço
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: { cliente: cliente, servico: servicoSelecionado },
                    success: function (response) {
                        //console.log(response);
                        if (response.status) {
                            preencherValoresRecorrenciasDefault(selectServico, servicoSelecionado, tarefaRow, name, true, response.recorrencia, response.dia); // Chama a função para preencher os valores de recorrência
                        } else {
                            preencherValoresRecorrenciasDefault(selectServico, servicoSelecionado, tarefaRow, name); // Chama a função para preencher os valores de recorrência
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Erro na requisição:', status, error);
                    }
                });
            }
        }

        /**         
         * @param {*} selectServico ENVIO O ELEMENTO SELECT DO SERVIÇO
         * @param {*} servicoSelecionado ENVIO O VALOR DO SERVIÇO SELECIONADO
         * @param {*} tarefaRow ENVIO A LINHA DA TAREFA
         * @param {*} name OS2 OU ADD (TAREFA NORMAL OU TAREFA ADITIVA)
         * @param {*} status ENVIO O STATUS DA RESPOSTA AJAX - PADRÃO É FALSE
         * @param {*} recorrencia SE O STATUS DA RESPOSTA AJAX FOR TRUE ENVIO O VALOR DA RECORRÊNCIA
         * @param {*} diaRecorrencia SE EXISTIR UM DIA DE RECORRÊNCIA ENVIO O VALOR DO DIA
         * @returns
         * @description A FUNÇÃO VERIFICA SE O SERVIÇO SELECIONADO TEM MEDIÇÃO E RECORRÊNCIA, SE SIM, MOSTRA AS DIVS RESPECTIVAS
         * E PREENCHE OS VALORES DE RECORRÊNCIA E DIA DE RECORRÊNCIA, SE NÃO, ESCONDE AS DIVS
         * E RESSETA OS VALORES
         * @author LUTCHY
         * @since 2025-04-02
         * @version 1.0
         */
        function preencherValoresRecorrenciasDefault(selectServico, servicoSelecionado, tarefaRow, name, status = false, recorrencia = null, diaRecorrencia = null) {
            let MedicaoDiv = tarefaRow.find(".medicaoOs2"); // Encontra a div de medição
            let recorrenciaDiv = tarefaRow.find(".recorrencia"); // Encontra a div de recorrência
            let recorrenciaSelect = recorrenciaDiv.find("select[name^='" + name + "_recorrencia_']"); // Seleciona o select de recorrência com base no atributo name
            let dataFixaDiv = tarefaRow.find(".datafixa"); // Encontra a div de data fixa
            let dataFixaInput = dataFixaDiv.find("input[name^='" + name + "_datafixa_']"); // Busca o input de data fixa dentro do contêiner

            if (servicoSelecionado) { //se o serviço selecionado não for vazio
                let selectedOption = selectServico.find('option:selected'); //pega o elemento option selecionado
                let precisaMedicao = selectedOption.data('medicao') == "1"; //verifica se o serviço selecionado precisa de medição
                let temRecorrencia = selectedOption.data('recorrencia') && selectedOption.data('recorrencia') !== "0"; //verifica se o serviço selecionado tem recorrência
                let diaRecorrenciaServico = selectedOption.data('diarecorrencia'); //pega o valor do dia da recorrência que está setado no select do serviço

                if (precisaMedicao) { //se precisar de medição
                    MedicaoDiv.show(); //então mostra a div de medição
                } else {
                    MedicaoDiv.hide(); //senão esconde
                }

                if (temRecorrencia) { //se o serviço selecionado tem recorrência
                    recorrenciaDiv.show(); //mostra a div de recorrência

                    // Se a página ainda estiver carregando, não alteramos recorrência e data-fixa
                    if (recorrenciaSelect.data('loaded') == false) {
                        return;
                    }

                    if (status) {
                        recorrenciaSelect.val(recorrencia); // Preenche o campo com o valor do dia                        
                        recorrenciaSelect.trigger('change'); //dispara o evento de mudança do select de recorrência pra exibir ou não a div de data fixa
                        if (diaRecorrencia) {
                            dataFixaInput.val(diaRecorrencia); // Preenche o campo com o valor do dia informado da funçao                            
                        } else {
                            dataFixaInput.val(diaRecorrenciaServico); // Preenche o campo com o valor do dia
                        }
                    } else {
                        recorrenciaSelect.val(selectedOption.data('recorrencia')); //coloca o valor padrão do select de recorrência
                        recorrenciaSelect.trigger('change'); //dispara o evento de mudança do select de recorrência pra exibir ou não a div de data fixa
                        dataFixaInput.val(diaRecorrenciaServico); //coloca o dia de recorrência padrão
                    }
                } else { //se o serviço não tiver recorrência
                    recorrenciaDiv.hide(); //esconde a div de recorrência
                    recorrenciaSelect.val(''); //reseta o valor do select de recorrência
                    dataFixaDiv.hide(); //esconde a div de data fixa
                }
            } else { //se o serviço for vazio
                MedicaoDiv.hide(); //esconde a div de medição
                dataFixaDiv.hide();//esconde a div de data fixa
            }
        }


        $(document).on('click', '#conclui-ordem', function () {

            let id = $(this).data('id'); //pega o id da OS
            let selectStatus = $("#status"); // pega o select de status
            let url = $(this).data('url'); //ordens/gerarecorrencia
            let conclui = $(this).data('conclui') === 'X'; //pega o valor do botão concluir
            let plcpadrao = $(this).data('plcpadrao'); //pega o plano de conta padrão
            let oprpadrao = $(this).data('oprpadrao'); //pega a operação padrão            

            if (!confirm('Deseja realmente concluir a OS ' + id + '?')) {
                return; // Interrompe a execução se o usuário cancelar
            }

            // Pega o status de cada tarefa em #container-linhas2
            let statusTarefas = [];
            $('#container-linhas2 .linhatarefa').each(function () {
                let status = $(this).find('.tarefanumero0').attr('data-status');
                if (status) {
                    statusTarefas.push(status);
                }
            });

            // Pega o status de cada tarefa aditiva em #div-os2-aditivo
            let statusTarefasAditivo = [];
            $('#div-os2-aditivo .linhaaditivo').each(function () {
                let status = $(this).find('.tarefanumero0').attr('data-status');
                if (status) {
                    statusTarefasAditivo.push(status);
                }
            });

            // Verifica se existe pelo menos um status diferente de 'D' ou 'C'
            const algumNaoConcluidoOuCancelado = [...statusTarefas, ...statusTarefasAditivo].some(
                s => s !== 'D' && s !== 'C'
            );

            if (algumNaoConcluidoOuCancelado) {
                if (!confirm('Existem tarefas não concluídas. Se confirmar, todas as tarefas serão automaticamente concluídas. Deseja continuar mesmo assim?')) {
                    return;
                }
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {
                    if (response.status == 'error') {
                        ajaxMessage(response.message, 5);
                        return;
                    }

                    if (response.status == 'success') {
                        alert(response.message);
                    }

                    if (conclui) {
                        // Resetando alterações na modal
                        //** Esconde campos desnecessários pra modal de lançamento financeiro */
                        $("#plconta-rec, #novoclirec, #datacad-rec, #competencia-rec, #documento-rec, #cliente, #obs1-rec, #obs2-rec").closest(".fcad-form-group").show();
                        //** Estiliza posição de campos restantes */
                        $("#vtotal-rec").closest(".fcad-form-group").removeClass("coluna100 direita");
                        $("#operacao-rec").closest(".fcad-form-group").removeClass("coluna100 direita").addClass("coluna30");
                        $(".grupo-parcelas .fcad-form-group").addClass("coluna15");
                        $("#modalRec .modal-dialog").css("max-width", ""); // Remove largura máxima customizada

                        let documento = "OS#" + response.os1.id;

                        //incluir pra esconder #plconta-rec
                        $("#novoclirec, #datacad-rec, #competencia-rec, #documento-rec, #cliente, #obs1-rec, #obs2-rec, #plconta-rec, #operacao-rec").closest(".fcad-form-group").hide();
                        $("#modalRec .btn-close, #modalRec .btn-fecharmodal").css("display", "none");
                        $("#vtotal-rec").closest(".fcad-form-group").addClass("coluna100 direita");
                        $("#operacao-rec").closest(".fcad-form-group").removeClass("coluna30").addClass("coluna100 direita");
                        $(".grupo-parcelas .fcad-form-group").removeClass("coluna15");

                        $("#plconta-rec").val(plcpadrao)
                        $("#cliente").val(response.os1.id_cli);
                        $("#vtotal-rec").val(formatarValor(response.os1.vtotal));
                        $("#documento-rec").val(documento);
                        $("#parcelas-rec").val("1");
                        $("#entrada-rec").val("0");
                        $("#intervalo-rec").val("0");
                        $("#operacao-rec").val(oprpadrao);

                        $("#documento-rec").trigger('input');
                        $("#parcelas-rec").trigger("input");

                        $("#modalRec").on("keydown.preventSubmit", function (e) {
                            if (e.key === "Enter") {
                                e.preventDefault();
                            }
                        });

                        $("#modalRec").on("show.bs.modal", function () {
                            $(this).find(".modal-dialog").css("max-width", "500px"); // Define a largura máxima da modal
                        });

                        // Adiciona input hidden se não existir
                        if ($("#modalRec").find("#no-reload").length === 0) {
                            $("#modalRec .modal-body").append('<input type="hidden" id="no-reload" name="no_reload" value="1">');
                        }

                        // Adiciona input hidden se não existir
                        if ($("#modalRec").find("#id_os1").length === 0) {
                            $("#modalRec .modal-body").append('<input type="hidden" id="id_os1" name="id_os1" value="' + response.os1.id + '">');
                        }

                        // Adiciona o atributo data-bs-dismiss no botão de submit da modal
                        $("#modal-rec-submit").attr("data-bs-dismiss", "modal");

                        // Abre a modal
                        $("#modalRec").modal("show");

                        $("#modalRec").off("hidden.bs.modal").on("hidden.bs.modal", function () {
                            selectStatus.val("5").trigger("change");
                            $("#grava-ordem").trigger("click");

                            $("#modalRec").off("keydown.preventSubmit");
                        });
                    } else {
                        selectStatus.val("5").trigger("change");
                        $("#grava-ordem").trigger("click");
                    }
                },
                error: function () {
                    console.log('Erro ao concluir a ordem.');
                }
            });
        });

        $(document).on('click', '#estornar-ordem', function () {
            let id = $(this).data('id'); //pega o id da OS
            let url = $(this).data('url'); //ordens/estornar
            let verifica = $(this).data('verifica'); //verifica se existe financeiro vinculado

            if (confirm('Deseja realmente estornar a OS ' + id + '?')) {
                $.post(verifica, { id: id }, function (response) {
                    if (response.flagrec) {
                        alert('Não é possível continuar. Existem receitas relacionadas a essa OS já baixadas. REALIZE O ESTORNO DESSAS RECEITAS PRIMEIRO! Documento do(s) título(s): ' + response.documento);
                        return; // Não prossegue se o usuário cancelar
                    }

                    // Continua com o estorno
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                window.location.reload();
                            }
                        },
                        error: function () {
                            alert('002 - Erro ao estornar a ordem.');
                        }
                    });
                }, 'json').fail(function () {
                    alert('001 - Erro ao estornar a ordem.');
                });
            }
        });

        $(document).on('click', '#duplica-ordem', function () {
            let id = $(this).data('id'); //pega o id da OS
            let url = $(this).data('url'); //ordens/duplicar

            if (!confirm('Deseja realmente duplicar a OS ' + id + '?')) {
                return; // Interrompe a execução se o usuário cancelar
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function (response) {

                    ajaxMessage(response.message, 5); // Exibe a mensagem de retorno

                    if (response.status == 'error') {
                        ajaxMessage(response.message, 5);
                        return;
                    }

                    if (response.status == 'success') {
                        window.location.href = response.redirect; // Redireciona para a nova OS duplicada
                    }
                }
                ,
                error: function () {
                    console.log('Erro ao duplicar a ordem.');
                }
            });
        });

        function changeRecorrencias(selectRecorrencia, selectServico) {
            let container = $(selectRecorrencia).closest(".ordens-form"); // Encontra o contêiner mais próximo
            let dataFixa = container.find(".datafixa"); // Busca a .datafixa dentro do contêiner
            let dataFixaInput = container.find(".datafixa input"); // Busca o input de data fixa dentro do contêiner
            var servicoSelecionado = container.find(selectServico + ' option:selected'); // Pega o option do serviço selecionado

            if ($(selectRecorrencia).val() > 2) {
                dataFixa.show(); // Exibe a datafixa se o valor for maior que 2
                if ($(selectRecorrencia).data('loaded') == true) {
                    dataFixaInput.val(servicoSelecionado.data('diarecorrencia'));
                }
            } else {
                dataFixa.hide(); // Esconde caso contrário                
                dataFixaInput.val(''); // Limpa o valor do input de data fixa
            }
        }


        $(document).on('change', 'select[name^="OS2_recorrencia_"]', function () {
            changeRecorrencias(this, '.selectServico'); // Chama a função para alterar a recorrência
        });

        $(document).on('change', 'select[name^="add_recorrencia_"]', function () {
            changeRecorrencias(this, '.selectAddServico'); // Chama a função para alterar a recorrência
        });

        // $(document).on('change', '.selectAddServico', function () {
        //     var servicoSelecionado = $(this).val();
        //     var tarefaRow = $(this).closest('.linhaaditivo');
        //     var MedicaoDiv = tarefaRow.find(".medicaoOs2");
        //     var url = MedicaoDiv.data('url');

        //     if (servicoSelecionado) {
        //         var selectedOption = $(this).find('option:selected');
        //         var precisaMedicao = selectedOption.data('medicao') == "1";

        //         if (precisaMedicao) {
        //             MedicaoDiv.show();
        //         } else {
        //             MedicaoDiv.hide();
        //         }
        //     } else {
        //         MedicaoDiv.hide();
        //     }
        // });

        function createLineMedicao(data) {
            var botaoEdit = '<button type="button" class="btn btn-secondary list-edt edit-medicao-retaguarda"><i class="fa fa-pen"></i></button>';
            var botaoConfirm = '<button type="button" data-id="' + data.id + '" data-url="' + data.edit + '" class="btn btn-success confirm-edit" hidden><i class="fa fa-check"></i></button>';
            var botaoDelete = '<button type="button" class="btn btn-secondary list-del medicaoOs-delete" data-url="' + data.delete + '"><i class="fa fa-trash"></i>';
            var botaoCancel = '<button type="button" class="btn btn-danger cancel-edit" hidden><i class="fa fa-xmark"></i></button>';

            let qtde = parseFloat(data.qtde);
            let qtdeDecimal = qtde - Math.floor(qtde);
            let operador = data.operador;
            let equipamento = data.equipamento;
            let obs = data.obs;

            let hiddenEqp = '';
            if (data.servicosComEquipamentos != 'X') {
                hiddenEqp = 'hidden';
            }

            if (qtdeDecimal == 0) {
                qtde = qtde.toFixed(0);
            }

            var newRow = '<tr style="font-size: 0.8em;">' +
                '<td style="width: 15%;">' + formatarDataHoraBr(data.datai) + '</td>' +
                '<td style="width: 15%;">' + formatarDataHoraBr(data.dataf) + '</td>' +
                '<td style="width: 9%;">' + qtde + '</td>' +
                '<td style="width: 27%;" data-id="' + data.id_operador + '">' + limitarTexto(operador, 15) + '</td>' +
                '<td style="width: 27%;" data-id="' + data.id_equipamento + '" ' + hiddenEqp + '>' + limitarTexto(equipamento, 15) + '</td>' +
                '<td style="width: 40%;" data-text="' + obs + '">' + limitarTexto(obs, 25) + '</td>' +
                '<td style="width: 8%;">' + botaoEdit + botaoConfirm + '</td>' +
                '<td style="width: 8%;">' + botaoDelete + botaoCancel + '</td>' +
                '</tr>';
            return newRow;
        }

        $(document).on('keyup', '#qtd_servico', function () {
            var qtd = $(this).val();

            var spanMedicaoTotal = $(this).closest('.linhatarefa').find('.medicao-os2-totalcontratado');

            spanMedicaoTotal.text(qtd);
        });

        $(document).on('keyup', '#qtd_aditivo', function () {
            var qtd = $(this).val();

            var spanMedicaoTotal = $(this).closest('.linhaaditivo').find('.medicao-os2-totalcontratado');

            spanMedicaoTotal.text(qtd);
        });

        function abrirModalPesquisa(btn, modal, url = false) {

            $(document).off('click', btn).on('click', btn, function () {
                var select = $(this).closest("label").next("select");
                if (!select.length) {
                    select = $(this).parent().find("select");
                }

                if (select.length) {
                    $(modal).data('target-select', select);

                    if (url) {
                        $.ajax({
                            url: url,
                            method: 'post',
                            dataType: 'json',
                            success: function (response) {
                                preencherTabelaServicosBusca(response);
                                $(modal).modal('show');
                            },
                            error: function (xhr, status, error) {
                                console.error('Erro ao carregar o conteúdo da modal:', status, error);
                            }
                        });
                    } else {
                        $(modal).modal('show');
                    }
                }
            });
        }

        function preencherTabelaServicosBusca(dados) {
            const $tbody = $("#srv_lst tbody");
            $tbody.empty(); // Limpa o conteúdo atual da tabela

            if (!dados.length) {
                $tbody.append("<tr><td colspan='100%'>Nenhum serviço cadastrado</td></tr>");
                return;
            }

            dados.forEach(item => {
                const linha = $(`
                    <tr>
                        <td style="width: 10%;">
                            <button type="button" 
                                    data-id="${item.id}" 
                                    class="btn btn-info btn-pick-srv" 
                                    id="btn-srv-${item.id}">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </td>
                        <td style="width: 90%;">
                            <label for="obs-${item.id}">${item.nome}</label>
                        </td>
                    </tr>
                `);

                $tbody.append(linha);
            });
        }

        function setarSelect(btn, modal) {
            $(document).off('click', btn).on('click', btn, function () {
                var id = $(this).data('id');
                var select = $(modal).data('target-select');

                if (select && select.length) {
                    select.val(id);
                }
                $(modal).modal('hide');
                $(select).trigger('change');
                preencheDataLegal(select);
            });
        }

        function abrirModalPesquisaFinanceiro(btn, modal) {
            $(document).off('click', btn).on('click', btn, function () {
                let select = $(this).closest("label").next("select");
                $(modal).find('.listas-fin-src').hide();

                // pega o valor do data-div do botão clicado
                let targetDiv = $(this).data('div');

                // exibe apenas a div correspondente
                if (targetDiv) {
                    let $div = $(modal).find('#' + targetDiv).show();
                    let tableId = $div.find("table").attr("id");

                    // limpa o input de filtro sempre que abrir
                    $(modal).find("#filtrarFinSrc").val("");

                    // Desliga eventos antigos do filtro
                    $("#filtrarFinSrc").off("input");

                    // Liga o filtro só para a tabela visível
                    filtrarLista("#filtrarFinSrc", "#" + tableId);
                }


                if (!select.length) {
                    select = $(this).parent().find("select");
                }

                if (select.length) {
                    $(modal).data('target-select', select);
                    $(modal).modal('show');
                }
            });
        }

        function setarSelectFinanceiro(btn, modal) {
            $(document).off('click', btn).on('click', btn, function () {
                let id = $(this).data('id');
                let select = $(modal).data('target-select');

                if (select && select.length) {
                    select.val(id);
                }
                $(modal).modal('hide');
                $(select).trigger('change');
            });
        }

        abrirModalPesquisaFinanceiro('.btn-fin-src', '#modalFinSrc');
        setarSelectFinanceiro('.btn-pickf-for', '#modalFinSrc');

        abrirModalPesquisa('.btn-srv-search', '#modalSrv', $('#form-ordens').data('buscaurl'));
        setarSelect('.btn-pick-srv', '#modalSrv');

        abrirModalPesquisa('.btn-opr-search', '#modalOpr');
        setarSelect('.btn-pick-opr', '#modalOpr');

        $(document).on('click', '#findcli', function () {
            $("#modalCliente").modal('show');
        });

        $(document).on('click', '.btn-oper-med-src', function () {
            $("#modalOpr").modal('show');
            const $modal = $("#modalOpr");
            const $buttons = $modal.find('.btn-pick-opr');
            const originalClass = 'btn-pick-opr';
            const tempClass = 'btn-pick-med-opr';

            // Substituir classes temporariamente
            $buttons.removeClass(originalClass).addClass(tempClass);

            // Evento para restaurar as classes quando a modal fechar
            $modal.off('hidden.bs.modal.pickOpr').on('hidden.bs.modal.pickOpr', function () {
                $buttons.removeClass(tempClass).addClass(originalClass);
            });
        });

        $("body").on('click', '.btn-pick-med-opr', function () {
            var id = $(this).data('id');
            var select = $('#operador_medicao');

            if (select && select.length) {
                select.val(id).trigger('change');
            }
            $("#modalOpr").modal('hide');
        });

        $(document).on('click', '.btn-eqp-med-src', function () {
            $("#modalEqpMedicao").modal('show');
        });

        $("body").on('click', '.btn-pick-eqp-med', function () {
            var id = $(this).data('id');
            var select = $('#eqp_medicao');

            if (select && select.length) {
                select.val(id).trigger('change');
            }
            $("#modalEqpMedicao").modal('hide');
        });

        $(document).on('click', '.btn-srv-novo', function () {
            var select = $(this).closest("label").next("select");
            if (!select.length) {
                select = $(this).parent().find("select");
            }

            $("#modalNovosrv").modal('show');
            $('#modalNovosrv').find('#target-select').val(select.attr('name'));
            $('#modalNovosrv').trigger('reset');

        });

        $("body").on('click', '.btn-pick-cli', function () {
            var id = $(this).data('id');
            var select = $('#cliente-os');

            if (select && select.length) {
                select.val(id).trigger('change');
            }
            $("#modalCliente").modal('hide');
        });

        $(document).on('click', '#findobra', function () {
            $('#filtrarObraListModal').val("");
            $('#obra_lst tbody').empty();

            let idCliente = $('#cliente-os').val();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: idCliente
                },
                success: function (response) {
                    if (response.status == "success") {
                        response.segmentos.forEach(function (segmento) {
                            let controle = '';
                            if (segmento.controle && segmento.controle.trim() !== '') {
                                controle = segmento.controle + ' - ';
                            }
                            var newRow = '<tr data-ent="' + idCliente + '">' +
                                '<td style="width: 10%;">' +
                                '<button type="button" data-id="' + segmento.id + '" class="btn btn-info btn-pick-obra" id="btn-obra-' + obra.id + '">' +
                                '<i class="fa fa-check"></i>' +
                                '</button>' +
                                '</td>' +
                                '<td style="width: 90%;"><label for="obs-' + segmento.id + '">' + controle + segmento.nome + '</td>' +
                                '</tr>';
                            $('#obra_lst tbody').append(newRow);
                        });
                    } else {
                        $('#obra_lst tbody').append('<tr><td colspan="13">Nenhum registro encontrado</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao carregar obras:', error);
                }
            });

            $("#modalObraList").modal('show');
        });

        $("body").on('click', '.btn-pick-obra', function () {
            var id = $(this).data('id');
            var select = $('#obra');

            if (select && select.length) {
                select.val(id).trigger('change');
            }
            $("#modalObraList").modal('hide');
        });

        $(document).on('click', '.btn-os2-medicao', function () {
            var id = $(this).data('tarefamedicao');
            var url = $(this).data('url'); // medicao/atualiza

            // Limpar inputs
            $("#data_inicio_medicao").val("");
            $("#data_fim_medicao").val("");
            $("#qtde_medicao").val("");
            $("#operador_medicao").val("");
            $("#obs_medicao").val("");

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (response) {
                    //                    console.log(response);
                    var tbody = $('#medicao-list tbody');
                    tbody.empty();

                    $("#tarefa-titulo-medicao").text(id);
                    $("#tarefa_id_medicao").val(id);
                    $("#nomeservico").text(response.servico);
                    $("#span-total").text(response.total);
                    $("#span-medido").text(response.medido);
                    $("#span-pendente").text(response.pendente);

                    if (response.status == "success") {
                        response.medicao.forEach(function (medicao) {
                            var newRow = createLineMedicao(medicao);
                            tbody.append(newRow);
                        });
                    } else {
                        tbody.append('<tr><td data-vazio="vazio" colspan="3">Nenhuma medição encontrada</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao carregar medição:', error);
                }
            });
            $('#modalMedicaoOs').modal('show');
        });

        $(document).on('click', '.medicaoOs-delete', function () {
            var url = $(this).data('url');
            var item = $(this).closest('tr');

            if (confirm('Tem certeza que deseja excluir este item?')) {
                $.ajax({
                    url: url,
                    data: { retaguarda: true },
                    type: 'POST',
                    dataType: 'json', // Define o tipo de dados esperados na resposta
                    success: function (response) {
                        if (response.status == 'success') {
                            var tbody = $('#medicao-list tbody');
                            item.remove();

                            //cor para o botão de medição usada em atualizaValoresTelaMedicao
                            response.colorbtn = "";

                            if (tbody.find('tr td').length == 0) {
                                tbody.append('<tr><td data-vazio="vazio" colspan="3">Nenhuma medição encontrada</td></tr>');
                                response.colorbtn = "white";
                            } else {
                                response.colorbtn = "chartreuse";
                            }

                            atualizaValoresTelaMedicao(response, tbody);

                            $("#span-total").text(response.total + " " + response.unidade);
                            $("#span-medido").text(response.medido + " " + response.unidade);
                            $("#span-pendente").text(response.pendente + " " + response.unidade);

                            ajaxMessage(response.message, 5);
                        } else {
                            ajaxMessage(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        });

        $(document).ready(function () {
            $(document).on("click", ".edit-medicao-retaguarda", function (e) {
                e.preventDefault();

                let row = $(this).closest("tr");
                let dateCells = row.find("td:eq(0), td:eq(1)");
                let quantityCell = row.find("td:eq(2)");
                let operadorCell = row.find("td:eq(3)");
                let eqpCell = row.find("td:eq(4)");
                let obsCell = row.find("td:eq(5)");

                // Armazena os valores antigos nos atributos data da linha
                row.data("oldValues", {
                    date1: formatarDataHoraIso(dateCells.eq(0).text()),
                    date2: formatarDataHoraIso(dateCells.eq(1).text()),
                    quantity: quantityCell.text(),
                    operador: operadorCell.data('id'),
                    eqp: eqpCell.data('id'),
                    obsFull: obsCell.data('text'),
                    obsFmt: obsCell.text()
                });

                let oldValues = row.data("oldValues");
                let operadorSelect = $("#operador_medicao").clone().removeAttr('id').val(oldValues.operador);
                operadorSelect.attr('id', 'operador_medicao_edit');

                let eqpSelect = $("#eqp_medicao").clone().removeAttr('id').val(oldValues.eqp);
                eqpSelect.attr('id', 'eqp_medicao_edit');

                // Substitui os textos por inputs
                dateCells.eq(0).html(`<input type="datetime-local" class="form-control" value="${oldValues.date1}">`);
                dateCells.eq(1).html(`<input type="datetime-local" class="form-control" value="${oldValues.date2}">`);
                quantityCell.html(`<input type="number" class="form-control" value="${oldValues.quantity}">`);
                operadorCell.html(operadorSelect);
                eqpCell.html(eqpSelect);
                obsCell.html(`<input type="text" class="form-control" value="${oldValues.obsFull}">`);

                // Oculta o botão de edição e mostra os de confirmação/cancelamento
                row.find(".list-edt").hide();
                row.find(".confirm-edit").prop("hidden", false);
                row.find(".medicaoOs-delete").hide();
                row.find(".cancel-edit").prop("hidden", false);

                $("tr").not(row).find(".edit-medicao-retaguarda, .medicaoOs-delete, .confirm-edit, .cancel-edit").prop("disabled", true);
            });

            $(document).on("click", ".cancel-edit", function () {
                let row = $(this).closest("tr");

                // Recupera os valores antigos armazenados nos atributos data
                let oldValues = row.data("oldValues");

                row.find("td:eq(0)").text(formatarDataHoraBr(oldValues.date1));
                row.find("td:eq(1)").text(formatarDataHoraBr(oldValues.date2));
                row.find("td:eq(2)").text(oldValues.quantity);
                row.find("td:eq(3)").text($("#operador_medicao option[value='" + oldValues.operador + "']").text());
                row.find("td:eq(4)").text($("#eqp_medicao option[value='" + oldValues.eqp + "']").text());
                row.find("td:eq(5)").text(oldValues.obsFmt);

                // Restaura os botões
                row.find(".list-edt").show();
                row.find(".confirm-edit").prop("hidden", true);
                row.find(".medicaoOs-delete").show();
                row.find(".cancel-edit").prop("hidden", true);

                $(".edit-medicao-retaguarda, .medicaoOs-delete, .confirm-edit, .cancel-edit").prop("disabled", false);
            });

            $(document).on("click", ".confirm-edit", function () {
                let row = $(this).closest("tr");
                let date1 = row.find("td:eq(0) input").val();
                let date2 = row.find("td:eq(1) input").val();
                let quantity = row.find("td:eq(2) input").val();
                let tarefaId = $("#tarefa_id_medicao").val();
                let operador = row.find("td:eq(3) select").val();
                let eqp = row.find("td:eq(4) select").val();
                let obs = row.find("td:eq(5) input").val();
                let id = $(this).data('id');
                let url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: id,
                        tarefaId: tarefaId,
                        datai: date1,
                        dataf: date2,
                        qtde: quantity,
                        operador: operador,
                        eqp: eqp,
                        obs: obs,
                        retaguarda: true
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 'error') {
                            ajaxMessage(response.message, 5);
                            row.find(".cancel-edit").trigger("click"); // Simula o clique no botão cancelar para reverter as mudanças
                            return;
                        } else {
                            let nome = $("#operador_medicao option[value='" + operador + "']").text();
                            let eqpDesc = $("#eqp_medicao option[value='" + eqp + "']").text();
                            // Atualiza as células com os novos valores
                            row.find("td:eq(0)").text(formatarDataBr(date1));
                            row.find("td:eq(1)").text(formatarDataBr(date2));
                            row.find("td:eq(2)").text(quantity);
                            row.find("td:eq(3)").text(limitarTexto(nome, 15));
                            row.find("td:eq(4)").text(limitarTexto(eqpDesc, 15));
                            row.find("td:eq(5)").text(limitarTexto(obs, 25));

                            ajaxMessage(response.message, 5);
                            row.find(".list-edt").show();
                            row.find(".confirm-edit").prop("hidden", true);
                            row.find(".medicaoOs-delete").show();
                            row.find(".cancel-edit").prop("hidden", true);

                            response.colorbtn = "chartreuse";

                            atualizaValoresTelaMedicao(response, "");

                            $("#span-total").text(response.total + " " + response.unidade);
                            $("#span-medido").text(response.medido + " " + response.unidade);
                            $("#span-pendente").text(response.pendente + " " + response.unidade);

                            $(".edit-medicao-retaguarda, .medicaoOs-delete, .confirm-edit, .cancel-edit").prop("disabled", false);
                        }
                    },
                    error: function () {
                        alert("Erro ao salvar os dados. Tente novamente.");
                    }
                });
            });
        });

        $(document).ready(function () {
            $("#btn-add").click(function () {
                // Capturar valores dos inputs
                let url = $("#form-medicaomodal").data('url'); // /medicao
                let tarefaId = $("#tarefa_id_medicao").val();
                let dataInicio = $("#data_inicio_medicao").val();
                let dataFim = $("#data_fim_medicao").val();
                let qtde = $("#qtde_medicao").val();
                let operador = $("#operador_medicao").val();
                let eqp = $("#eqp_medicao").val();
                let obs = $("#obs_medicao").val();

                // Enviar via AJAX
                $.ajax({
                    url: url, // URL do backend
                    type: "POST",
                    data: {
                        tarefaId: tarefaId,
                        datai: dataInicio,
                        dataf: dataFim,
                        qtde: qtde,
                        operador: operador,
                        eqp: eqp,
                        obs: obs,
                        retaguarda: true
                    },
                    dataType: "json",
                    success: function (response) {
                        //console.log(response);
                        if (response.status == "success") {
                            var tbody = $('#medicao-list tbody');

                            response.colorbtn = "chartreuse";

                            atualizaValoresTelaMedicao(response, tbody);

                            if (tbody.find('tr td').data('vazio') == 'vazio') {
                                tbody.find('tr').remove();
                            }

                            response.medicao.forEach(function (medicao) {
                                var newRow = createLineMedicao(medicao);
                                tbody.append(newRow);
                            });

                            ajaxMessage(response.message, 5);

                            // Limpar inputs
                            $("#data_inicio_medicao").val("");
                            $("#data_fim_medicao").val("");
                            $("#qtde_medicao").val("");
                            $("#operador_medicao").val("");
                            $("#eqp_medicao").val("");
                            $("#obs_medicao").val("");

                            $("#span-total").text(response.total + " " + response.unidade);
                            $("#span-medido").text(response.medido + " " + response.unidade);
                            $("#span-pendente").text(response.pendente + " " + response.unidade);
                        } else {
                            ajaxMessage(response.message, 5);
                        }
                    },
                    error: function () {
                        alert("Ocorreu um erro ao tentar salvar a medição.");
                    }
                });
            });
        });

        function atualizaValoresTelaMedicao(response, tbody) {
            let totalParcial = "";

            if (response.aditivo == "S") {
                $(".linhaaditivo").each(function () {
                    let id = $(this).find('input[name^="add_id_"]').val();

                    if (id == response.tarefaId) {
                        totalParcial = $(this).find('.medicao-os2-totalfeito');
                        let selectServicoAdd = $(this).find('.selectAddServico');
                        let btnMedicaoAdd = $(this).find('.btn-os2-medicao');
                        let btnDelTarefa = $(this).find('.deltarefa-add');

                        if (!selectServicoAdd.hasClass('select-readonly')) {
                            selectServicoAdd.addClass('select-readonly');
                        }

                        btnMedicaoAdd.css('color', response.colorbtn);
                        btnDelTarefa.hide();
                    }
                });
            } else {
                $(".linhatarefa").each(function () {
                    let id = $(this).find('input[name^="OS2_id_"]').val();

                    if (id == response.tarefaId) {
                        totalParcial = $(this).find('.medicao-os2-totalfeito');
                        let selectServico = $(this).find('.selectServico');
                        let btnMedicao = $(this).find('.btn-os2-medicao');
                        let btnDelTarefa = $(this).find('.deltarefa');

                        if (!selectServico.hasClass('select-readonly')) {
                            selectServico.addClass('select-readonly');
                        }

                        btnMedicao.css('color', response.colorbtn);
                        btnDelTarefa.hide();
                    }
                });
            }

            totalParcial.text(response.medido);
        }



        $(document).on('change', '.selectMaterial', function () {
            var linhaAtual = $(this).closest('.ordens-form');
            var divPai = $(this).closest('.accordion-collapse');
            var material = linhaAtual.find('.selectMaterial').val();

            var div = "#" + divPai.attr('id');

            var linhaDuplicada = verificarMaterialDuplicado(material, linhaAtual, div);

            if (linhaDuplicada) {
                alert('Esse produto/material já existe na OS!');
                linhaAtual.find('.selectMaterial').val("").focus();

                // Foca no input #qtd_servico da linha duplicada

                return;
            }
        });


        $(document).on('click', '.deltarefa, .deletemat, .deletemattarefa, .deltarefa-add', function () {
            $(this).closest('.fcad-form-row').remove();
            somatotalOS('.selectServico', '#sumservico', "input[name^='OS2_vtotal_servico_']");
            somatotalOS('.selectAddServico', '#sumAddservico', "input[name^='add_vtotal_servico_']");
            somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
        });

        $('.btn-close, .close').on('click', function () {
            $('#modalNovocli').hide();
        });

        $(document).on('mouseenter', '.tarefanumero0', function (event) {
            var status = $(this).attr('data-status');
            if (status == '0') {
                return;
            }
            let tooltipText = "";

            if (status == 'A') {
                tooltipText = "AGUARDANDO INÍCIO";
            } else if (status == 'I') {
                tooltipText = "EM EXECUÇÃO";
            } else if (status == 'P') {
                tooltipText = "PAUSADA";
            } else if (status == 'C') {
                tooltipText = "CONCLUÍDA";
            } else if (status == 'D') {
                tooltipText = "CANCELADA";
            }

            tooltipText = tooltipText.replace(/\n/g, '<br>'); // Substitui \n por <br>
            const tooltipDiv = $('<div class="tooltip-text2"></div>').html(tooltipText); // Usa html() em vez de text()

            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            tooltipDiv.css({
                top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
                left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', '.tarefanumero0', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });

        $(document).on('mouseenter', '.blinking-text', function (event) {
            tooltipText = $(this).attr('data-tooltip');
            const tooltipDiv = $('<div class="tooltip-text2"></div>').html(tooltipText);

            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            tooltipDiv.css({
                top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
                left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', '.blinking-text', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });

        if ($("#tipo-user").val() != 3) {
            $(document).on('click', '#ordens-list tr', function (event) {
                const existingTooltip = $(this).data('tooltipDiv');
                if (existingTooltip) {
                    existingTooltip.remove();
                    $(this).removeData('tooltipDiv');
                    return;
                }

                const tooltipText = $(this).data('tooltip'); // Pega o conteúdo do data-tooltip

                if (!tooltipText) return;

                //tooltipText = tooltipText.replace(/\n/g, '<br>'); // Substitui \n por <br>
                const tooltipDiv = $('<div class="tooltip-text2"></div>').html(tooltipText); // Usa html() em vez de text()

                $('body').append(tooltipDiv);

                const rect = this.getBoundingClientRect();
                tooltipDiv.css({
                    top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
                    left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
                });

                $(this).data('tooltipDiv', tooltipDiv);
            });

            $(document).on('mouseleave', '#ordens-list tr', function () {
                const tooltipDiv = $(this).data('tooltipDiv');
                if (tooltipDiv) {
                    tooltipDiv.remove();
                    $(this).removeData('tooltipDiv');
                }
            });

            $(document).on('dblclick', '#ordens-list tr', function () {
                const tooltipDiv = $(this).data('tooltipDiv');
                if (tooltipDiv) {
                    tooltipDiv.remove();
                    $(this).removeData('tooltipDiv');
                }
            });
        }
    });

    $(document).ready(function () {
        let form = $("#form-ordens");
        let btnConcluir = $("#conclui-ordem");


        setTimeout(() => {
            let formDataInicial = form.serialize(); // Captura o estado inicial do formulário
            // Função para desabilitar o botão
            function desabilitarConcluir() {
                btnConcluir.prop("disabled", true);
            }

            // Monitora mudanças em qualquer input, select ou textarea dentro do formulário
            form.on("input change", "input, select, textarea", function () {
                desabilitarConcluir();
            });

            // Monitora cliques nos botões que chamam modais ou modificam o formulário
            $(".btn-opr-search, .btn-srv-search, .deltarefa, .btn-os2-medicao").on("click", function () {
                desabilitarConcluir();
            });

            // Monitora a adição de novas linhas (caso exista um botão para adicionar)
            $(document).on("click", ".btn-adicionar-linha", function () {
                desabilitarConcluir();
            });
        }, 500);

    });


    $(document).ready(function () {

        $(document).on('mouseenter', '.btn-os2-att-status', function (event) {
            let tooltipText = $(this).attr('data-tooltip');
            if (!tooltipText) return;

            const tooltipDiv = $('<div style="width: 150px; text-align: center;" class="tooltip-text"></div>').html(tooltipText);
            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            const scrollTop = $(window).scrollTop(); // Corrige a posição com scroll

            tooltipDiv.css({
                top: (rect.top + scrollTop - tooltipDiv.outerHeight() - 5) + 'px',
                left: (rect.left + (rect.width / 2) - (tooltipDiv.outerWidth() / 2)) + 'px',
                opacity: 1,
                visibility: 'visible'
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', '.btn-os2-att-status', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });

        $(document).on('mouseenter', '.medicao-desabilitado', function (event) {
            let tooltipText = $(this).attr('data-tooltip');
            if (!tooltipText) return;

            const tooltipDiv = $('<div style="width: 150px;" class="tooltip-text"></div>').html(tooltipText);
            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            const scrollTop = $(window).scrollTop(); // Corrige a posição com scroll

            tooltipDiv.css({
                top: (rect.top + scrollTop - tooltipDiv.outerHeight() - 5) + 'px',
                left: (rect.left + (rect.width / 2) - (tooltipDiv.outerWidth() / 2)) + 'px',
                opacity: 1,
                visibility: 'visible'
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', '.medicao-desabilitado', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });
    });


    function formatarValor(valor) {
        var numeroFormatado = parseFloat(valor).toFixed(2);
        var partes = numeroFormatado.split('.');
        partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return partes.join(',');
    }

    function aplicaUnidade(linhaId) {

        if (linhaId == '[id^=linha-material]') {
            var select = ".selectMaterial option:selected";
            var campo = "span[name^='OS3_und_material_']";
        } else if (linhaId == '[id^=linha-aditivo]') {
            var select = ".selectAddServico option:selected";
            var campo = "span[name^='add_und_servico_']";
        } else {
            var select = ".selectServico option:selected";
            var campo = "span[name^='OS2_und_servico_']";
        }

        $(linhaId).each(function () {
            var unidade = $(this).find(select).data('unidade');
            var span = $(this).find(campo);
            span.text(unidade);
        });
    }

    function escondeInputs() {
        $('.linhatarefa').each(function () {
            $(this).find('.medicaoOs2').hide();
            $(this).find('.recorrencia').hide();
            $(this).find('.datafixa').hide();
        });

        $('.linhaaditivo').each(function () {
            $(this).find('.medicaoOs2').hide();
            $(this).find('.recorrencia').hide();
            $(this).find('.datafixa').hide();
        });
    }

    function somatotalOS(select, span, vtotal) {
        if (select == '.selectMaterial') {
            var linha = '.linha-material';
        } else if (select == '.selectAddServico') {
            var linha = '.linhaaditivo';
            somaTotalMateriaisPorTarefa(linha);
        }
        else {
            var linha = '.linhatarefa';
            somaTotalMateriaisPorTarefa(linha);
        }
        var soma = 0;
        var totalgeral = 0;
        $(select).each(function () {
            var totalitem = parseFloat($(this).closest(linha).find(vtotal).val().replace(/\./g, '').replace(',', '.')) || 0;
            soma += totalitem;
        });
        $(span).text(formatarValor(soma));
        var totalOS2 = parseFloat($("#sumservico").text().replace(/\./g, '').replace(',', '.')) || 0;
        var totalOS3 = parseFloat($("#summaterial").text().replace(/\./g, '').replace(',', '.')) || 0;
        var totalAdd = parseFloat($("#sumAddservico").text().replace(/\./g, '').replace(',', '.')) || 0;
        var totalOs2Mat = parseFloat($("#sumMatOs2").text().replace(/\./g, '').replace(',', '.')) || 0;
        var totalOs2MatAdd = parseFloat($("#sumMatOs2Add").text().replace(/\./g, '').replace(',', '.')) || 0;
        totalgeral = totalOS2 + totalOS3 + totalAdd + totalOs2Mat + totalOs2MatAdd;
        $(".ordem1 #vtotal").val(formatarValor(totalgeral));
    }

    function atualizaTotalGeral() {
        let totalOS2 = parseFloat($("#sumservico").text().replace(/\./g, '').replace(',', '.')) || 0;
        let totalOS3 = parseFloat($("#summaterial").text().replace(/\./g, '').replace(',', '.')) || 0;
        let totalAdd = parseFloat($("#sumAddservico").text().replace(/\./g, '').replace(',', '.')) || 0;
        let totalOs2Mat = parseFloat($("#sumMatOs2").text().replace(/\./g, '').replace(',', '.')) || 0;
        let totalOs2MatAdd = parseFloat($("#sumMatOs2Add").text().replace(/\./g, '').replace(',', '.')) || 0;

        let totalgeral = totalOS2 + totalOS3 + totalAdd + totalOs2Mat + totalOs2MatAdd;
        totalgeral = totalOS2 + totalOS3 + totalAdd + totalOs2Mat + totalOs2MatAdd;
        $(".ordem1 #vtotal").val(formatarValor(totalgeral));
    }

    function somaTotalMateriaisPorTarefa(linha) {
        let soma = 0;
        if (linha == '.linhatarefa') {
            $(linha).each(function () {
                let totalData = parseFloat($(this).find(".btn-os2mat").data('total')) || 0; // Ensure totalData is a number
                soma += totalData;
            });
            $("#sumMatOs2").text(formatarValor(soma));
        } else if (linha == '.linhaaditivo') {
            $(linha).each(function () {
                let totalData = parseFloat($(this).find(".btn-os2mat").data('total')) || 0; // Ensure totalData is a number
                soma += totalData;
            });
            $("#sumMatOs2Add").text(formatarValor(soma));
        }
    }

    $(document).ready(function () {
        escondeInputs();

        $(".selectServico").each(function () {
            $(this).trigger("change"); // Dispara o evento de mudança para cada select já preenchido
        });

        $("select[name^='OS2_recorrencia_']").each(function () {
            $(this).trigger('change');
        });

        $("select[name^='add_recorrencia_']").each(function () {
            $(this).trigger('change');
        });

        setTimeout(function () {
            $("select[name^='OS2_recorrencia_']").each(function () {
                var selectRecorrencia = $(this);
                selectRecorrencia.attr('data-loaded', 'true');
            });
        }, 1000);

        setTimeout(function () {
            $("select[name^='add_recorrencia_']").each(function () {
                var selectRecorrencia = $(this);
                selectRecorrencia.attr('data-loaded', 'true');
            });
        }, 1000);

        aplicaUnidade('[id^=linha-material]');
        aplicaUnidade('[id^=linha-tarefa]');
        aplicaUnidade('[id^=linha-aditivo]');
        somatotalOS('.selectServico', '#sumservico', "input[name^='OS2_vtotal_servico_']");
        // Evento para calcular e preencher a duração quando o serviço é selecionado
        $(document).on("change", ".selectServico", function () {
            let $selectedOption = $(this).find('option:selected'); // Captura a opção selecionada
            let tempoServico = $selectedOption.data('tempo'); // Pega o tempo associado ao serviço
            let $row = $(this).closest(".linhatarefa"); // Captura a linha atual
            let valorServico = parseFloat($selectedOption.data('valor')); // pega o valor unitario        

            // Pega a quantidade digitada
            let quantidade = parseFloat($row.find("input[name^='OS2_qtd_servico_']").val()) || 0;

            // Calcula a nova duração (tempo do serviço * quantidade)
            let novaDuracao = parseFloat(((tempoServico * quantidade) / 60).toFixed(2));
            let totalServico = valorServico * quantidade;

            // Preenche o campo de duração
            if (!isNaN(novaDuracao) && novaDuracao !== null && novaDuracao !== '') {
                $row.find("input[name^='OS2_tempo_']").val(novaDuracao);
                $row.find("input[name^='OS2_vtotal_servico_']").val(formatarValor(totalServico));
                $row.find("input[name^='OS2_vunit_servico_']").val(formatarValor(valorServico));
            } else {
                $row.find("input[name^='OS2_tempo_']").val(''); // Define um valor padrão, como 0
                $row.find("input[name^='OS2_vtotal_servico_']").val('');
                $row.find("input[name^='OS2_vunit_servico_']").val('');
            }

            somatotalOS('.selectServico', '#sumservico', "input[name^='OS2_vtotal_servico_']");
            aplicaUnidade('[id^=linha-tarefa]');
        });


        // Evento para atualizar a duração se a quantidade mudar
        $(document).on("input", "input[name^='OS2_qtd_servico_']", function () {
            let $row = $(this).closest(".linhatarefa"); // Captura a linha atual
            let quantidade = parseFloat($(this).val().replace(',', '.')) || 0; // Pega a quantidade digitada, tratando ponto e vírgula como separadores de milhares
            let tempoServico = $row.find(".selectServico option:selected").data('tempo'); // Pega o tempo do serviço
            let valorServico = parseFloat($row.find(".selectServico option:selected").data('valor')); // Pega o tempo associado ao serviço        


            // Calcula a nova duração (tempo do serviço * quantidade)
            let novaDuracao = parseFloat(((tempoServico * quantidade) / 60).toFixed(2));
            let totalServico = valorServico * quantidade;

            // Preenche o campo de duração
            if (!isNaN(novaDuracao) && novaDuracao !== null && novaDuracao !== '') {
                $row.find("input[name^='OS2_tempo_']").val(novaDuracao);
                $row.find("input[name^='OS2_vtotal_servico_']").val(formatarValor(totalServico));
                $row.find("input[name^='OS2_vunit_servico_']").val(formatarValor(valorServico));
            } else {
                $row.find("input[name^='OS2_tempo_']").val(''); // Define um valor padrão, como 0
                $row.find("input[name^='OS2_vtotal_servico_']").val('');
                $row.find("input[name^='OS2_vunit_servico_']").val('');
            }


            somatotalOS('.selectServico', '#sumservico', "input[name^='OS2_vtotal_servico_']");
        });
    });

    $(document).ready(function () {
        somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
        //Evento para atualizar total do material ao escolher o material
        $(document).on("change", ".selectMaterial", function () {
            let $selectedOption = $(this).find('option:selected');
            let unMat = $selectedOption.data('unidade');
            let vlrMat = parseFloat($selectedOption.data('vfloat')) || 0;

            let $row = $(this).closest(".linha-material");

            // Define unidade
            $row.find("span[name^='OS3_und_material_']").text(unMat);

            // Define quantidade padrão como 1, se estiver vazio
            const qtdInput = $row.find("input[name^='OS3_qtd_material_']")[0];
            const anQtd = AutoNumeric.getAutoNumericElement(qtdInput);
            if (anQtd && anQtd.getNumber() === 0) {
                anQtd.set(1);
            }

            // Define valor unitário vindo do select
            const valInput = $row.find("input[name^='OS3_valor_material_']")[0];
            const anVal = AutoNumeric.getAutoNumericElement(valInput);
            if (anVal) {
                anVal.set(vlrMat);
            }

            // Recalcula total da linha
            calculaTotalLinhaMaterial();

            // Recalcula total geral
            somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
        });

        // Evento para atualizar total da tarefa se valor unitario mudar
        $(document).on("input", "input[name^='OS2_vunit_servico_']", function () {
            let vlrTotalServico = 0;
            let $row = $(this).closest(".fcad-form-row");
            let quantidade = parseFloat($row.find("input[name^='OS2_qtd_servico_']").val().replace(',', '.')) || 0;
            let vlrServico = parseFloat($(this).val().replace(',', '.')) || 0;

            vlrTotalServico = vlrServico * quantidade;
            $row.find("input[name^='OS2_vtotal_servico_']").val(formatarValor(vlrTotalServico));
            somatotalOS('.selectServico', '#sumservico', "input[name^='OS2_vtotal_servico_']");
        });

        // Função para preencher o valor unitário para uma linha específica
        function preencherValorUnitario(selectElement) {
            var valor = $(selectElement).find('option:selected').data('valor');
            // Encontra o campo de valor unitário na mesma linha que o select
            $(selectElement).closest('.fcad-form-row').find('#valor_material').val(valor);
            somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
        }

        // Preencher automaticamente no evento change do select para linhas dinâmicas
        $(document).on('change', '.selectMaterial', function () {
            preencherValorUnitario(this);
            somatotalOS('.selectMaterial', '#summaterial', "input[name^='OS3_vtotal_material_']");
        });
    });

    $(document).ready(function () {

        $(".selectAddServico").each(function () {
            $(this).trigger("change"); // Dispara o evento de mudança para cada select já preenchido
        });

        somatotalOS('.selectAddServico', '#sumAddservico', "input[name^='add_vtotal_servico_']");
        // Evento para calcular e preencher a duração quando o serviço é selecionado
        $(document).on("change", ".selectAddServico", function () {
            let $selectedOption = $(this).find('option:selected'); // Captura a opção selecionada
            let tempoServico = $selectedOption.data('tempo'); // Pega o tempo associado ao serviço
            let $row = $(this).closest(".linhaaditivo"); // Captura a linha atual
            let valorServico = parseFloat($selectedOption.data('valor')); // pega o valor unitario        

            // Pega a quantidade digitada
            let quantidade = parseFloat($row.find("input[name^='add_qtd_servico_']").val()) || 0;

            // Calcula a nova duração (tempo do serviço * quantidade)
            let novaDuracao = parseFloat(((tempoServico * quantidade) / 60).toFixed(2));
            let totalServico = valorServico * quantidade;

            // Preenche o campo de duração
            if (!isNaN(novaDuracao) && novaDuracao !== null && novaDuracao !== '') {
                $row.find("input[name^='add_tempo_']").val(novaDuracao);
                $row.find("input[name^='add_vtotal_servico_']").val(formatarValor(totalServico));
                $row.find("input[name^='add_vunit_servico_']").val(formatarValor(valorServico));
            } else {
                $row.find("input[name^='add_tempo_']").val(''); // Define um valor padrão, como 0
                $row.find("input[name^='add_vtotal_servico_']").val('');
                $row.find("input[name^='add_vunit_servico_']").val('');
            }

            somatotalOS('.selectAddServico', '#sumAddservico', "input[name^='add_vtotal_servico_']");
            aplicaUnidade('[id^=linha-aditivo]');
        });


        // Evento para atualizar a duração se a quantidade mudar
        $(document).on("input", "input[name^='add_qtd_servico_']", function () {
            let $row = $(this).closest(".linhaaditivo"); // Captura a linha atual
            let quantidade = parseFloat($(this).val().replace(',', '.')) || 0; // Pega a quantidade digitada, tratando ponto e vírgula como separadores de milhares
            let tempoServico = $row.find(".selectAddServico option:selected").data('tempo'); // Pega o tempo do serviço
            let valorServico = parseFloat($row.find(".selectAddServico option:selected").data('valor')); // Pega o tempo associado ao serviço        


            // Calcula a nova duração (tempo do serviço * quantidade)
            let novaDuracao = parseFloat(((tempoServico * quantidade) / 60).toFixed(2));
            let totalServico = valorServico * quantidade;

            // Preenche o campo de duração
            if (!isNaN(novaDuracao) && novaDuracao !== null && novaDuracao !== '') {
                $row.find("input[name^='add_tempo_']").val(novaDuracao);
                $row.find("input[name^='add_vtotal_servico_']").val(formatarValor(totalServico));
                $row.find("input[name^='add_vunit_servico_']").val(formatarValor(valorServico));
            } else {
                $row.find("input[name^='add_tempo_']").val(''); // Define um valor padrão, como 0
                $row.find("input[name^='add_vtotal_servico_']").val('');
                $row.find("input[name^='add_vunit_servico_']").val('');
            }

            somatotalOS('.selectAddServico', '#sumAddservico', "input[name^='add_vtotal_servico_']");
        });

        // Evento para atualizar total da tarefa se valor unitario mudar
        $(document).on("input", "input[name^='add_vunit_servico_']", function () {
            let vlrTotalServico = 0;
            let $row = $(this).closest(".fcad-form-row");
            let quantidade = parseFloat($row.find("input[name^='add_qtd_servico_']").val()) || 0;
            let vlrServico = parseFloat($(this).val()) || 0;

            vlrTotalServico = vlrServico * quantidade;
            $row.find("input[name^='add_vtotal_servico_']").val(formatarValor(vlrTotalServico));
            somatotalOS('.selectAddServico', '#sumAddservico', "input[name^='add_vtotal_servico_']");
        });
    });


    $(document).ready(function () {

        function filtrosOrdens() {
            var statusFiltro = $('#os-status').val();
            var clienteFiltro = $('#os-cli').val();
            var tipoFiltro = $('#os-tipo').val();
            var dataInicial = $('#os-datai').val();
            var dataFinal = $('#os-dataf').val();
            var mostrarCanceladas = $('#os-canceladas').is(':checked');
            var mostrarConcluidas = $('#os-concluidas').is(':checked');
            var textoBusca = $('#filtrarOrdens').length ? $('#filtrarOrdens').val().toLowerCase() : "";

            // Validação de datas
            if (dataInicial && dataFinal) {
                var dataIniMoment = moment(dataInicial, 'YYYY-MM-DD');
                var dataFimMoment = moment(dataFinal, 'YYYY-MM-DD');
                if (dataIniMoment.isAfter(dataFimMoment)) {
                    alert('A data inicial deve ser menor ou igual a data final.');
                    return;
                }
            }

            $('#ordens-list tbody tr').each(function () {
                var mostrarLinha = true;

                // --- Status
                var status = $(this).find('td').eq(2).text().trim();
                if (statusFiltro !== "todos" && status !== statusFiltro) mostrarLinha = false;

                // --- Cliente
                var cliente = $(this).find('td').eq(5).data('idcli');
                if (clienteFiltro !== "todos" && cliente != clienteFiltro) mostrarLinha = false;

                // --- Tipo
                if (tipoFiltro !== "selecione" && $(this).find('td.os-tipo').length) {
                    var tipo = $(this).find('td.os-tipo').text().trim();
                    if (tipo !== tipoFiltro) mostrarLinha = false;
                }

                // --- Datas de execução
                var execucao = $(this).find('td').eq(4).text().trim(); // ex: "10/08/2023"
                if (dataInicial) {
                    if (moment(execucao, 'DD/MM/YYYY').isBefore(moment(dataInicial, 'YYYY-MM-DD'))) mostrarLinha = false;
                }
                if (dataFinal) {
                    if (moment(execucao, 'DD/MM/YYYY').isAfter(moment(dataFinal, 'YYYY-MM-DD'))) mostrarLinha = false;
                }

                // --- Canceladas / Concluídas
                if ((status === "CANCELADA" && !mostrarCanceladas) || (status === "CONCLUÍDA" && !mostrarConcluidas)) {
                    mostrarLinha = false;
                }

                // --- Busca de texto
                if (textoBusca && $(this).text().toLowerCase().indexOf(textoBusca) === -1) {
                    mostrarLinha = false;
                }

                // Aplica resultado
                $(this).toggle(mostrarLinha);
            });
        }

        // Eventos
        // $('.os-filtrar').on('click', filtrosOrdens);
        // $('#os-status, #os-cli, #os-tipo, #os-canceladas, #os-concluidas').on('change', filtrosOrdens);
        // $('#filtrarOrdens').on('keyup', filtrosOrdens);

        // // Executa ao carregar a página
        // filtrosOrdens();

        $("body").on("click", "#pickobra", function () {

            var $os1cli = $("#cliente-os").val();
            var $selectObraModal = $("#cliente-obra");
            $selectObraModal.val($os1cli).trigger('change');
            $("#modalObras").modal('show');
        });

        $("body").on("click", "#pickobs", function () {
            $("#modalObs").modal('show');
            $("input[type='checkbox']").prop("checked", false);
        });

        $("body").on("click", "#incluir-obs", function () {
            let selecionadas = [];

            $("input[name='observacoes[]']:checked").each(function () {
                selecionadas.push($(this).closest('tr').find('td:nth-child(2)').text().trim());
            });

            if (selecionadas.length > 0) {
                let obsAtual = $("#obs").val().trim();
                let novasObs = selecionadas.join(';\n');
                let resultado = obsAtual ? obsAtual + ';\n' + novasObs : novasObs;
                $("#obs").val(resultado);
            }

            $("#modalObs").modal("hide"); // Fecha o modal
        });
    });

    $(document).ready(function () {

        $('#obra-container').hide();
        $('#abre-obra').prop('disabled', true);

        var statusdaordem = $("#status").val();

        // Verificação inicial ao carregar a página
        if (statusdaordem != 5 && statusdaordem != 7) {
            checkClienteOs();
        }
        checkAbreObra();
        filterObras();

        // Verificação ao mudar o select #cliente-os
        $('#cliente-os').change(function () {
            $('#obra').val("");
            checkClienteOs();
            filterObras();
        });

        // Verificação ao mudar o checkbox #abre-obra
        $('#abre-obra').change(function () {
            checkAbreObra();
        });

        // Observador para mudanças no select #cliente-os
        if (window.location.pathname.match(/^\/ordens\/form(\/.*)?$/)) {
            const observer = new MutationObserver(function (mutationsList, observer) {
                for (let mutation of mutationsList) {
                    if (mutation.type === 'childList') {
                        checkClienteOs();
                        filterObras();
                    }
                }
            });

            observer.observe(document.getElementById('cliente-os'), { childList: true });
        }
    });

    $(document).ready(function () {
        var checkbox = $("#orcamento-os1");
        var select = $("#status");

        checkbox.change(function () {
            if (checkbox.is(":checked")) {
                select.val("8");
            } else {
                select.val("2");
            }
        });
    });


    $(document).ready(function () {
        $('.periodo .fcad-form-group').hide();
        $('#radio-obra').hide();

        $('.filter-select').change(function () {
            var selectedValue = $(this).val();

            // Hide all filter groups and reset their values
            $('.periodo .fcad-form-group').hide().find('input, select').val('0');
            $('#radio-obra').hide().find('input[type="radio"]').prop('checked', false).filter('[value="individual"]').prop('checked', true);;
            $(".resultados-rel").empty();

            if (selectedValue === 'os') {

            } else if (selectedValue === 'obra') {
                $('#filter-obra').show();
                $('#radio-obra').show();
            } else if (selectedValue === 'funcionario') {
                $('#filter-funcionario').show();
            } else if (selectedValue === 'servico') {
                $('#filter-servico').show();
            } else if (selectedValue === 'cliente') {
                $('#filter-cliente').show();
            }
        });
    });


    $(document).ready(function () {
        function carregarRelatorio() {
            let formData = {};
            let url = $("#url-medicao-rel").val();

            $("#medicao-rel-form input, #medicao-rel-form select").each(function () {
                let name = $(this).attr('name');
                if ($(this).is(':radio')) {
                    // Se for rádio, pega apenas o selecionado
                    if ($(this).is(':checked')) {
                        formData[name] = $(this).val();
                    }
                } else {
                    let value = $(this).val();
                    if (name && value != null) {
                        formData[name] = value;
                    }
                }
            });

            $.ajax({
                url: url, // URL do seu controlador
                type: "POST",
                data: formData,
                dataType: "json",
                beforeSend: function () {
                    $(".resultados-rel").html("<p>Carregando...</p>");
                },
                success: function (response) {
                    $(".resultados-rel").empty();

                    if (response.message) {
                        ajaxMessage(response.message, ajaxResponseBaseTime);
                    }

                    if (response.status == 'success') {
                        $(".resultados-rel").html(response.result);
                    }

                },
                error: function () {
                    $(".resultados-rel").html("<p>Erro ao carregar os dados.</p>");
                }
            });
        }

        // Disparar a busca ao clicar no botão
        $("#gera-relatorio").click(function (e) {
            e.preventDefault();
            carregarRelatorio();
        });

        // // Disparar ao mudar os filtros
        // $("#filter-rel, #data-inicio, #data-fim").change(function () {
        //     carregarRelatorio();
        // });

        $(document).on('mouseenter', '.tooltip-rel', function (event) {

            var tooltipText = $(this).data('text');

            tooltipText = tooltipText.replace(/\n/g, '<br>'); // Substitui \n por <br>
            const tooltipDiv = $('<div class="tooltip-text2"></div>').html(tooltipText); // Usa html() em vez de text()

            $('body').append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            tooltipDiv.css({
                top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
                left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', '.tooltip-rel', function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });
    });


    //** BOTÕES DE EDIÇÃO GRUPOS (EMP1) */

    $(document).ready(function () {
        $(document).on("click", ".emp1-edit", function (e) {
            e.preventDefault();

            let row = $(this).closest("tr");
            let descricaoCell = row.find("td:eq(1)");

            // Armazena os valores antigos nos atributos data da linha
            row.data("oldValues", {
                desc: descricaoCell.text()
            });

            let oldValues = row.data("oldValues");

            // Substitui o texto por um input
            descricaoCell.html(`<input class="input-edit-emp1" type="text" value="${oldValues.desc}">`);

            // Oculta o botão de edição e mostra os de confirmação/cancelamento
            row.find(".emp1-edit").hide();
            row.find(".emp1-confirm-edit").prop("hidden", false);
            row.find(".emp1-delete").hide();
            row.find(".emp1-cancel-edit").prop("hidden", false);
        });

        $(document).on("click", ".emp1-cancel-edit", function () {
            let row = $(this).closest("tr");

            // Recupera os valores antigos armazenados nos atributos data
            let oldValues = row.data("oldValues");

            row.find("td:eq(1)").text(oldValues.desc);

            // Restaura os botões
            row.find(".emp1-edit").show();
            row.find(".emp1-confirm-edit").prop("hidden", true);
            row.find(".emp1-delete").show();
            row.find(".emp1-cancel-edit").prop("hidden", true);
        });

        $(document).on("click", ".emp1-confirm-edit", function () {
            let row = $(this).closest("tr");
            let descricao = row.find("td:eq(1) input").val();
            let id = $(this).data('id');
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_empgroup: id,
                    descricao: descricao
                },
                dataType: "json",
                success: function (response) {
                    // Atualiza as células com os novos valores                    
                    row.find("td:eq(1)").text(descricao);

                    ajaxMessage(response.message, 5);
                    row.find(".emp1-edit").show();
                    row.find(".emp1-confirm-edit").prop("hidden", true);
                    row.find(".emp1-delete").show();
                    row.find(".emp1-cancel-edit").prop("hidden", true);
                },
                error: function () {
                    alert("Erro ao salvar os dados. Tente novamente.");
                }
            });
        });
    });

    function calcularValorTotal() {
        $('.qtde_os2_material').each(function () {
            let linha = $(this).closest('tr');

            const qtd = AutoNumeric.getNumber(linha.find('.qtde_os2_material')[0]);
            const un = AutoNumeric.getNumber(linha.find('.vunit_os2_material')[0]);
            const total = qtd * un;

            AutoNumeric.getAutoNumericElement(linha.find('.vtotal_os2_material')[0]).set(total);
        });
    }

    $(document).on('input', '.qtde_os2_material, .vunit_os2_material', calcularValorTotal);

    $(document).ready(function () {
        $("#new-group").on("click", function (e) {
            e.preventDefault();

            // Gera um ID temporário negativo (por segurança)
            let tempId = "new-" + Date.now();
            let url = $(this).data('url');

            // Monta a nova linha
            let newRow = `
                <tr data-id="${tempId}" class="row-new">
                    <td>--</td>
                    <td><input class="input-edit-emp1" type="text" value=""></td>
                    <td class="coluna-acoes">                        
                        <button data-id="${tempId}" data-url="${url}" class="btn btn-secondary list-add emp1-confirm-insert">
                            <i class="fa fa-check"></i>
                        </button>
                    </td>
                    <td>                        
                        <button class="btn btn-secondary list-del emp1-cancel-insert">
                            <i class="fa fa-xmark"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Adiciona no topo da tabela
            $("tbody").prepend(newRow);
        });

        // Cancelar nova linha
        $(document).on("click", ".row-new .emp1-cancel-insert", function () {
            $(this).closest("tr").remove();
        });

        // Confirmação da nova linha
        $(document).on("click", ".row-new .emp1-confirm-insert", function () {
            let row = $(this).closest("tr");
            let descricao = row.find("td:eq(1) input").val();
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_empgroup: null, // ou deixe em branco se for inserção
                    descricao: descricao
                },
                dataType: "json",
                success: function (response) {
                    ajaxMessage(response.message, 5);
                    if (response.reload) {
                        window.location.reload();
                    }
                },
                error: function () {
                    alert("Erro ao salvar o novo grupo. Tente novamente.");
                }
            });
        });
    });

    //** MODAL MATERIAIS OS2 */

    $("body").on("change", ".id_os2_material", function (e) {
        let row = $(this).closest("tr");
        let unidade = $(this).find("option:selected").data('unidade');
        let valor = $(this).find("option:selected").data('vunit'); // deve ser número puro
        let campo = row.find(".und_os2mat");
        let inputVunit = row.find("#vunit_os2_material");
        let inputQtde = row.find(".qtde_os2_material")[0];

        campo.text(unidade);

        const anInstance = AutoNumeric.getAutoNumericElement(inputVunit[0]);
        if (anInstance) {
            anInstance.set(valor); // ← Aqui está o segredo
        } else {
            inputVunit.val(formatarValor(valor)); // fallback
        }

        // Se campo de quantidade estiver vazio, definir como 1
        const anQtde = AutoNumeric.getAutoNumericElement(inputQtde);
        if (anQtde && !AutoNumeric.getNumber(inputQtde)) {
            anQtde.set(1);
        }

        calcularValorTotal();
    });

    $(document).ready(function () {
        let botaoMaterialClicado = null;

        $("body").on("click", ".btn-os2mat", function (e) {
            let id = $(this).data('tarefa');
            let url = $(this).data('url'); //ordens/verificamateriais

            botaoMaterialClicado = $(this);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_tarefa: id
                },
                dataType: "json",
                success: function (response) {
                    let tbody = $("#matOs2-list tbody");
                    tbody.empty();

                    $("#tarefa-titulo-materiais").text(id);
                    $("#tarefa_id_material").val(id);
                    $("#modalMateriaisOs #nomeservico").text(response.servico);

                    if (response.status == "success") {
                        $("#os2mat-total-geral").text(formatarValor(response.soma_vtotal));
                        response.materiais.forEach(function (material) {
                            let newRow = matCreateLine(material);
                            tbody.append(newRow);
                        });
                    } else {
                        tbody.append('<tr><td data-vazio="vazio" colspan="5">Nenhum produto/material para essa tarefa.</td></tr>');
                        $("#os2mat-total-geral").text("0,00");
                    }

                    //Sempre limpar os inputs
                    $("#id_os2_material").val("");
                    $("#qtde_os2_material").val("");
                    $("#vunit_os2_material").val("");
                    $("#vtotal_os2_material").val("");
                    $(".und_os2mat").text("");

                },
                error: function () {
                    console.log("Erro ao carregar os dados. Tente novamente.");
                }
            });

            $('#modalMateriaisOs').modal('show');
        })


        //** CÁLCULO DOS VALORES NA MODAL DE LANÇAMENTO DE MATERIAIS */


        function atualizaValoresTela(response, tbody) { //pra lançamentos de materiais
            if (response.aditivo == "S") { //se for tarefa aditiva, alterações na linha aditiva
                $(".linhaaditivo").each(function () {
                    let id = $(this).find('input[name^="add_id_"]').val(); //pego o id do serviço salvo

                    if (id == response.tarefaId) { //se o id do serviço da linha for o mesmo id do serviço da resposta
                        let selectServicoAdd = $(this).find('.selectAddServico'); //pego o select de serviço da linha aditiva
                        let btnDelTarefa = $(this).find('.deltarefa-add'); //pego o botão de deletar tarefa da linha aditiva

                        if (tbody.find('tr td').length == 0) { //verifico se contém registro de materiais na tabela da modal, e caso não tenha, adiciono a linha de vazio
                            tbody.append('<tr><td data-vazio="vazio" colspan="5">Nenhum produto/material para esta tarefa.</td></tr>'); //adicionando a linha de vazio
                        } else {
                            selectServicoAdd.addClass('select-readonly'); //desabilitando o select de serviço pra alterações
                            btnDelTarefa.hide(); //escondendo o botão de deletar tarefa
                        }

                        let botaoMaterial = $(this).find(".btn-os2mat"); //pego o botão que abre a modal de materiais da linha
                        botaoMaterial.data('total', response.soma_vtotal); //atualizo seu data-total
                        botaoMaterial.attr('data-total', response.soma_vtotal);
                        if (!response.soma_vtotal || response.soma_vtotal === 0) { //se o total for 0, deixo o botão branco
                            botaoMaterial.css('color', 'white');
                        } else if (response.soma_vtotal && parseFloat(response.soma_vtotal) > 0) {
                            botaoMaterial.css('color', '#7FFF00');
                        }
                    }
                });
            } else {
                $(".linhatarefa").each(function () {
                    let id = $(this).find('input[name^="OS2_id_"]').val();

                    if (id == response.tarefaId) {
                        let selectServico = $(this).find('.selectServico');
                        let btnDelTarefa = $(this).find('.deltarefa'); //pego o botão de deletar tarefa da linha aditiva

                        if (tbody.find('tr td').length == 0) {
                            tbody.append('<tr><td data-vazio="vazio" colspan="5">Nenhum produto/material para esta tarefa.</td></tr>');
                        } else {
                            selectServico.addClass('select-readonly');
                            btnDelTarefa.hide(); //escondendo o botão de deletar tarefa
                        }

                        let botaoMaterial = $(this).find(".btn-os2mat");
                        botaoMaterial.data('total', response.soma_vtotal);
                        botaoMaterial.attr('data-total', response.soma_vtotal);
                        if (!response.soma_vtotal || response.soma_vtotal === 0) {
                            botaoMaterial.css('color', 'white');
                        } else if (response.soma_vtotal && parseFloat(response.soma_vtotal) > 0) {
                            botaoMaterial.css('color', '#7FFF00');
                        }
                    }
                });
            }

            $("#os2mat-total-geral").text(formatarValor(response.soma_vtotal || 0)); //por fim atualizo o total no corpo da modal
        }

        $("body").on("click", "#btn-os2mat-add", function (e) {
            let url = $("#form-materiaisos2modal").data('url'); //ordens/materiais
            let tarefaId = $("#tarefa_id_material").val();
            let material_id = $("#id_os2_material").val();
            let qtde = $("#qtde_os2_material").val();
            let vunit = $("#vunit_os2_material").val();
            let vtotal = $("#vtotal_os2_material").val();

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_tarefa: tarefaId,
                    id_material: material_id,
                    qtde: qtde,
                    vunit: vunit,
                    vtotal: vtotal,
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        let tbody = $("#matOs2-list tbody"); //seleciona a tabela da modal

                        if (tbody.find('tr td').data('vazio') == 'vazio') {
                            tbody.find('tr').remove();
                        } //remove a linha de vazio                        

                        response.material.forEach(function (material) {
                            let newRow = matCreateLine(material);
                            tbody.append(newRow);
                        }); // adiciona os novos dados na tabela

                        atualizaValoresTela(response, tbody); //atualiza os totais e os botões de materiais
                        somaTotalMateriaisPorTarefa('.linhatarefa');
                        somaTotalMateriaisPorTarefa('.linhaaditivo');
                        atualizaTotalGeral();

                        ajaxMessage(response.message, 5); //Mensagem de sucesso

                        //limpa os inputs
                        $("#id_os2_material").val("");
                        $("#qtde_os2_material").val("");
                        $("#vunit_os2_material").val("");
                        $("#vtotal_os2_material").val("");
                    } else {
                        ajaxMessage(response.message, 5); //Mensagem de erro
                    }
                },
                error: function () {
                    ajaxMessage("Erro ao carregar os dados. Tente novamente.", 5);
                }
            });
        })

        $(document).on("click", ".os2mat-delete", function (e) {
            let url = $(this).data('url'); //ordens/excluirMaterial
            let item = $(this).closest("tr");
            let id = $(this).data('id');

            if (confirm('Tem certeza que deseja excluir este item?')) {
                $.ajax({
                    url: url,
                    data: { id: id },
                    type: 'POST',
                    dataType: 'json', // Define o tipo de dados esperados na resposta
                    success: function (response) {
                        //console.log(response);
                        if (response.status == 'success') {
                            var tbody = $('#matOs2-list tbody');
                            item.remove();

                            atualizaValoresTela(response, tbody); //atualiza os totais e os botões de materiais
                            somaTotalMateriaisPorTarefa('.linhatarefa');
                            somaTotalMateriaisPorTarefa('.linhaaditivo');
                            atualizaTotalGeral();

                            ajaxMessage(response.message, 5);
                        } else {
                            ajaxMessage(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        });


        $(document).on("click", ".os2mat-edit", function (e) {
            e.preventDefault();

            let row = $(this).closest("tr");
            let qtdeCell = row.find("td:eq(1)");
            let vunitCell = row.find("td:eq(3)");
            let vtotalCell = row.find("td:eq(4)");

            // Armazena os valores antigos nos atributos data da linha
            row.data("oldValues", {
                qtde: qtdeCell.text(),
                vunit: vunitCell.text(),
                vtotal: vtotalCell.text()
            });

            let oldValues = row.data("oldValues");

            // Substitui os textos por inputs            
            qtdeCell.html(`<input type="text" class="form-control qtde_os2_material num-decimal3" value="${oldValues.qtde}">`);
            vunitCell.html(`<input type="text" class="form-control vunit_os2_material num-decimal2" value="${oldValues.vunit}">`);
            vtotalCell.html(`<input type="text" class="form-control vtotal_os2_material num-decimal2" value="${oldValues.vtotal}" readonly>`);

            initAutoNumeric('.num-decimal2');
            initAutoNumeric('.num-decimal3', 3);

            // Oculta o botão de edição e mostra os de confirmação/cancelamento
            row.find(".os2mat-edit").hide();
            row.find(".os2mat-confirm-edit").prop("hidden", false);
            row.find(".os2mat-delete").hide();
            row.find(".os2mat-cancel-edit").prop("hidden", false);
        });

        $(document).on("click", ".os2mat-cancel-edit", function () {
            let row = $(this).closest("tr");

            // Recupera os valores antigos armazenados nos atributos data
            let oldValues = row.data("oldValues");

            row.find("td:eq(1)").text(oldValues.qtde);
            row.find("td:eq(3)").text(oldValues.vunit);
            row.find("td:eq(4)").text(oldValues.vtotal);

            // Restaura os botões
            row.find(".os2mat-edit").show();
            row.find(".os2mat-confirm-edit").prop("hidden", true);
            row.find(".os2mat-delete").show();
            row.find(".os2mat-cancel-edit").prop("hidden", true);
        });

        $(document).on("click", ".os2mat-confirm-edit", function () {
            let row = $(this).closest("tr");
            let qtde = row.find("td:eq(1) input").val();
            let tarefaId = $("#tarefa_id_material").val();
            let vunit = row.find("td:eq(3) input").val();
            let vtotal = row.find("td:eq(4) input").val();
            let id = $(this).data('id');
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: id,
                    id_tarefa: tarefaId,
                    qtde: qtde,
                    vunit: vunit,
                    vtotal: vtotal
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        let tbody = $('#matOs2-list tbody');
                        // Atualiza as células com os novos valores                    
                        row.find("td:eq(1)").text(qtde);
                        row.find("td:eq(3)").text(vunit);
                        row.find("td:eq(4)").text(vtotal);

                        ajaxMessage(response.message, 5);
                        row.find(".os2mat-edit").show();
                        row.find(".os2mat-confirm-edit").prop("hidden", true);
                        row.find(".os2mat-delete").show();
                        row.find(".os2mat-cancel-edit").prop("hidden", true);

                        atualizaValoresTela(response, tbody); //atualiza os totais e os botões de materiais
                        somaTotalMateriaisPorTarefa('.linhatarefa');
                        somaTotalMateriaisPorTarefa('.linhaaditivo');
                        atualizaTotalGeral();

                    } else {
                        ajaxMessage(response.message, 5);
                    }

                },
                error: function () {
                    alert("Erro ao salvar os dados. Tente novamente.");
                }
            });
        });

        function matCreateLine(data) {
            var botaoEdit = '<button type="button" class="btn btn-secondary os2mat-edit"><i class="fa fa-pen"></i></button>';
            var botaoConfirm = '<button type="button" data-id="' + data.crypt_id + '" data-url="' + data.edit + '" class="btn btn-success os2mat-confirm-edit" hidden><i class="fa fa-check"></i></button>';
            var botaoDelete = '<button type="button" class="btn btn-secondary os2mat-delete" data-url="' + data.delete + '" data-id="' + data.crypt_id + '"><i class="fa fa-trash"></i>';
            var botaoCancel = '<button type="button" class="btn btn-danger os2mat-cancel-edit" hidden><i class="fa fa-xmark"></i></button>';

            let qtde = parseFloat(data.qtde);
            let qtdeDecimal = qtde - Math.floor(qtde);
            let descricao = data.descricao;
            let vunit = data.vunit;
            let vtotal = data.vtotal;

            if (qtdeDecimal == 0) {
                qtde = qtde.toFixed(0);
            }

            var newRow = '<tr>' +
                '<td style="width: 50%;">' + descricao + '</td>' +
                '<td style="text-align: right;">' + formatarValor(qtde) + '</td>' +
                '<td>' + data.unidade + '</td>' +
                '<td style="text-align: right;">' + formatarValor(vunit) + '</td>' +
                '<td style="text-align: right; padding-right: 10px;">' + formatarValor(vtotal) + '</td>' +
                '<td style="width: 5%;">' + botaoEdit + botaoConfirm + '</td>' +
                '<td style="width: 5%;">' + botaoDelete + botaoCancel + '</td>' +
                '</tr>';
            return newRow;
        }
    });


    //** MODAL EQUIPAMENTOS OS2 */    

    $(document).ready(function () {
        $("body").on("click", ".btn-os2eqp", function (e) {
            let id = $(this).data('tarefa');
            let url = $(this).data('url'); //ordens/verificaequipamentos            

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_tarefa: id
                },
                dataType: "json",
                success: function (response) {
                    let tbody = $("#equipOs2-list tbody");
                    tbody.empty();

                    $("#tarefa-titulo-equipamentos").text(id);
                    $("#tarefa_id_equipamento").val(id);
                    $("#modalEquipamentosOs #nomeservico").text(response.servico);

                    if (response.status == "success") {
                        response.equipamentos.forEach(function (eqp) {
                            let newRow = eqpCreateLine(eqp);
                            tbody.append(newRow);
                        });
                    } else {
                        tbody.append('<tr><td data-vazio="vazio" colspan="5">Nenhum equipamento para essa tarefa.</td></tr>');
                    }

                    //Sempre limpar os inputs
                    $("#id_os2_equipamento").val("");
                    $("#qtde_os2_equipamento").val("");

                },
                error: function () {
                    console.log("Erro ao carregar os dados. Tente novamente.");
                }
            });

            $('#modalEquipamentosOs').modal('show');
        })

        $("body").on("click", "#btn-os2equip-add", function (e) {
            let url = $("#form-equipamentosos2modal").data('url'); //ordens/materiais
            let tarefaId = $("#tarefa_id_equipamento").val();
            let equipamento_id = $("#id_os2_equipamento").val();
            let qtde = $("#qtde_os2_equipamento").val();

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id_tarefa: tarefaId,
                    id_equipamento: equipamento_id,
                    qtde: qtde
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        let tbody = $("#equipOs2-list tbody"); //seleciona a tabela da modal

                        if (tbody.find('tr td').data('vazio') == 'vazio') {
                            tbody.find('tr').remove();
                        } //remove a linha de vazio                        

                        response.equipamentos.forEach(function (equipamento) {
                            let newRow = eqpCreateLine(equipamento);
                            tbody.append(newRow);
                        }); // adiciona os novos dados na tabela

                        atualizaValoresTela2(response, tbody); //atualiza os totais e os botões de equipamentos

                        ajaxMessage(response.message, 5); //Mensagem de sucesso

                        //limpa os inputs
                        $("#id_os2_equipamento").val("");
                        $("#qtde_os2_equipamento").val("");
                    } else {
                        ajaxMessage(response.message, 5); //Mensagem de erro
                    }
                },
                error: function () {
                    ajaxMessage("Erro ao carregar os dados. Tente novamente.", 5);
                }
            });
        })


        $(document).on("click", ".os2eqp-delete", async function (e) {
            let url = $(this).data('url'); //ordens/excluirEquipamento
            let item = $(this).closest("tr");
            let id = $(this).data('id');

            if (confirm('Tem certeza que deseja excluir este item?')) {

                let urlVerifica = $("#form-equipamentosos2modal").data('verificachk'); //ordens/verificaequipamentos
                let continuar = true;

                try {
                    const temChkList = await $.post(urlVerifica, { id: id }, null, 'json');
                    if (temChkList === true) {
                        continuar = confirm('Este equipamento possui checklist vinculado. Deseja excluir mesmo assim?');
                    }
                } catch (error) {
                    console.error('Erro na verificação:', error);
                    return;
                }

                if (!continuar) return;

                $.ajax({
                    url: url,
                    data: { id: id },
                    type: 'POST',
                    dataType: 'json', // Define o tipo de dados esperados na resposta
                    success: function (response) {
                        //console.log(response);
                        if (response.status == 'success') {
                            var tbody = $('#equipOs2-list tbody');
                            item.remove();

                            atualizaValoresTela2(response, tbody); //atualiza os totais e os botões de materiais                            

                            ajaxMessage(response.message, 5);
                        } else {
                            ajaxMessage(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            }
        });


        $(document).on("click", ".os2eqp-edit", function (e) {
            e.preventDefault();

            let row = $(this).closest("tr");
            let qtdeCell = row.find("td:eq(1)");

            // Armazena os valores antigos nos atributos data da linha
            row.data("oldValues", {
                qtde: qtdeCell.text()
            });

            let oldValues = row.data("oldValues");

            // Substitui os textos por inputs            
            qtdeCell.html(`<input type="text" class="form-control qtde_os2_equipamento only-int" value="${oldValues.qtde}">`);

            $('.only-int').on('input', function () {
                // Remove tudo que não for número
                this.value = this.value.replace(/\D/g, '');

                // Remove zero à esquerda, caso exista
                this.value = this.value.replace(/^0+/, '');

                // Se o campo ficar vazio, coloca "0" (não deixa ficar vazio)
                if (this.value === '') {
                    this.value = '0';
                }
            });

            // Oculta o botão de edição e mostra os de confirmação/cancelamento
            row.find(".os2eqp-edit").hide();
            row.find(".os2eqp-confirm-edit").prop("hidden", false);
            row.find(".os2eqp-delete").hide();
            row.find(".os2eqp-cancel-edit").prop("hidden", false);
        });

        $(document).on("click", ".os2eqp-cancel-edit", function () {
            let row = $(this).closest("tr");

            // Recupera os valores antigos armazenados nos atributos data
            let oldValues = row.data("oldValues");

            row.find("td:eq(1)").text(oldValues.qtde);

            // Restaura os botões
            row.find(".os2eqp-edit").show();
            row.find(".os2eqp-confirm-edit").prop("hidden", true);
            row.find(".os2eqp-delete").show();
            row.find(".os2eqp-cancel-edit").prop("hidden", true);
        });


        $(document).on("click", ".os2eqp-confirm-edit", function () {
            let row = $(this).closest("tr");
            let qtde = row.find("td:eq(1) input").val();
            let tarefaId = $("#tarefa_id_equipamento").val();
            let id = $(this).data('id');
            let url = $(this).data('url');

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: id,
                    id_tarefa: tarefaId,
                    qtde: qtde
                },
                dataType: "json",
                success: function (response) {
                    if (response.status == "success") {
                        let tbody = $('#equipOs2-list tbody');
                        // Atualiza as células com os novos valores                    
                        row.find("td:eq(1)").text(qtde);

                        // Verifica se a quantidade é maior que 1 para desabilitar o botão de checklist
                        if (qtde > 1) {
                            row.find('.os2eqp-open-modalchk').prop('disabled', true);
                        } else {
                            row.find('.os2eqp-open-modalchk').prop('disabled', false);
                        }

                        ajaxMessage(response.message, 5);
                        row.find(".os2eqp-edit").show();
                        row.find(".os2eqp-confirm-edit").prop("hidden", true);
                        row.find(".os2eqp-delete").show();
                        row.find(".os2eqp-cancel-edit").prop("hidden", true);

                        atualizaValoresTela2(response, tbody); //atualiza os totais e os botões de materiais                        

                    } else {
                        ajaxMessage(response.message, 5);
                    }

                },
                error: function () {
                    alert("Erro ao salvar os dados de edição. Tente novamente.");
                }
            });
        });

        function eqpCreateLine(data) {
            var botaoEdit = '<button type="button" class="btn btn-secondary os2eqp-edit"><i class="fa fa-pen"></i></button>';
            var botaoConfirm = '<button type="button" data-id="' + data.crypt_id + '" data-url="' + data.edit + '" class="btn btn-success os2eqp-confirm-edit" hidden><i class="fa fa-check"></i></button>';
            var botaoDelete = '<button type="button" class="btn btn-secondary os2eqp-delete" data-url="' + data.delete + '" data-id="' + data.crypt_id + '"><i class="fa fa-trash"></i>';
            var botaoCancel = '<button type="button" class="btn btn-danger os2eqp-cancel-edit" hidden><i class="fa fa-xmark"></i></button>';

            let qtde = parseFloat(data.qtde);
            let qtdeDecimal = qtde - Math.floor(qtde);
            let descricao = data.descricao;

            if (qtdeDecimal == 0) {
                qtde = qtde.toFixed(0);
            }

            // Verifica se a quantidade é maior que 1 para desabilitar o botão de checklist
            let botaoChk;
            if (qtde > 1 || !data.temchkitens) {
                botaoChk = '<button type="button" class="btn btn-secondary os2eqp-open-modalchk" disabled data-id_os2="' + data.id_os2 + '" data-id_os2_2="' + data.id + '"><i class="fa fa-list-check"></i></button>';
            } else {
                botaoChk = '<button type="button" class="btn btn-secondary os2eqp-open-modalchk" data-id_os2="' + data.id_os2 + '" data-id_os2_2="' + data.id + '"><i class="fa fa-list-check"></i></button>';
            }

            // Verifica se temchk é true e desabilita botaoEdit se necessário
            if (data.temchk) {
                botaoEdit = '<button type="button" class="btn btn-secondary os2eqp-edit" disabled><i class="fa fa-pen"></i></button>';
            }

            var newRow = '<tr>' +
                '<td style="width: 40%;">' + descricao + '</td>' +
                '<td style="width: 20%; text-align: left;">' + qtde + '</td>' +
                '<td style="width: 5%;">' + botaoChk + '</td>' +
                '<td style="width: 5%;">' + botaoEdit + botaoConfirm + '</td>' +
                '<td style="width: 5%;">' + botaoDelete + botaoCancel + '</td>' +
                '</tr>';
            return newRow;
        }
    });

    /**  
     * FUNÇÃO PARA ATUALIZAR A MODAL DE EQUIPAMENTO PRA CADA TAREFA E OS BOTÕES DE EQUIPAMENTOS E SERVIÇOS DA LINHA DE TAREFA
     * ESSA FUNÇÃO FUNCIONA EM CONJUNTO COM UMA REQUISIÇÃO AJAX QUE É FEITA QUANDO O USUÁRIO CLICA NO BOTÃO DE EQUIPAMENTOS
     * @param {*} response - RESPOSTA DA REQUISIÇÃO AJAX QUE CONTÉM OS VALORES NOVOS DO EQUIPAMENTO DA TAREFA
     * @param {*} tbody - CORPO DA TABELA DA MODAL DE EQUIPAMENTOS
     */
    function atualizaValoresTela2(response, tbody) { //pra lançamentos de equipamentos
        if (response.aditivo == "S") { //se for tarefa aditiva, alterações na linha aditiva                
            $(".linhaaditivo").each(function () {
                let id = $(this).find('input[name^="add_id_"]').val(); //pego o id do serviço salvo

                if (id == response.tarefaId) { //se o id do serviço da linha for o mesmo id do serviço da resposta
                    let selectServicoAdd = $(this).find('.selectAddServico'); //pego o select de serviço da linha aditiva
                    let botaoEquipamento = $(this).find(".btn-os2eqp");
                    let btnDelTarefa = $(this).find('.deltarefa-add'); //pego o botão de deletar tarefa da linha aditiva

                    if (tbody.find('tr td').length == 0) { //verifico se contém registro de materiais na tabela da modal, e caso não tenha, adiciono a linha de vazio
                        tbody.append('<tr><td data-vazio="vazio" colspan="3">Nenhum equipamento para esta tarefa.</td></tr>'); //adicionando a linha de vazio
                        botaoEquipamento.css('color', 'white');
                    } else {
                        selectServicoAdd.addClass('select-readonly'); //desabilitando o select de serviço pra alterações
                        botaoEquipamento.css('color', '#7FFF00');
                        btnDelTarefa.hide(); //escondendo o botão de deletar tarefa
                    }
                }
            });
        } else {
            $(".linhatarefa").each(function () { //pra cada linha de tarefa               
                let id = $(this).find('input[name^="OS2_id_"]').val(); //pego o id da tarefa
                if (id == response.tarefaId) { //encontro a linha da tarefa que foi alterada
                    let selectServico = $(this).find('.selectServico'); //pego o select de serviço da linha de tarefa
                    let botaoEquipamento = $(this).find(".btn-os2eqp"); //pego o botão que abre a modal de equipamentos da linha
                    let btnDelTarefa = $(this).find('.deltarefa'); //pego o botão de deletar tarefa da linha

                    if (tbody.find('tr td').length == 0) { //se a tabela da modal não contém registros de equipamentos, adiciono a linha de vazio
                        tbody.append('<tr><td data-vazio="vazio" colspan="3">Nenhum equipamento para esta tarefa.</td></tr>');
                        botaoEquipamento.css('color', 'white'); //se não tiver equipamentos, deixo o botão branco sinalizando que não há equipamentos na tarefa
                    } else {
                        selectServico.addClass('select-readonly'); //desabilitando o select de serviço pra alterações - uma vez que existe equipamento lançado praquela tarefa, o serviço precisa continuar o mesmo
                        botaoEquipamento.css('color', '#7FFF00'); //se tiver equipamentos, deixo o botão verde sinalizando que há equipamentos na tarefa
                        btnDelTarefa.hide(); //escondendo o botão de deletar tarefa - uma vez que existe equipamento lançado praquela tarefa, não é possível deletar a tarefa
                    }
                }
            });
        }
    }

    //** MODAL MUDANÇA DE STATUS TAREFAS *//
    $(document).ready(function () {
        let currentTarefaId = null;

        // Ao clicar no botão
        $(document).on('click', '.btn-os2-att-status', function (e) {
            e.preventDefault();

            const $button = $(this);
            const statusAtual = $button.data('status');
            const tarefaId = $button.data('tarefa');

            currentTarefaId = tarefaId;

            const offset = $button.offset();
            const $dropdown = $('.status-dropdown');
            const $optionsContainer = $('#status-options');

            // Resetando opções (caso venham de outro clique anterior)
            $optionsContainer.children().show(); // Mostra tudo
            $optionsContainer.children('[data-status="A"]').hide(); // Remove status 'A'
            $optionsContainer.children(`[data-status="${statusAtual}"]`).hide(); // Remove status atual

            // Ajusta texto da opção 'I' se status atual for 'P'
            const $optionI = $optionsContainer.children('[data-status="I"]');
            if (statusAtual === 'P') {
                $optionI.text('RETOMAR');
            } else {
                $optionI.text('EM EXECUÇÃO');
            }

            $dropdown.css({
                top: offset.top + $button.outerHeight(),
                left: offset.left
            }).show();

            //console.log('Tarefa:', tarefaId, '| Status atual:', statusAtual);
        });

        // Ao clicar em uma opção
        $(document).on('click', '.stcustom-option', function () {
            const status = $(this).data('status');
            const text = $(this).text();

            $('.status-dropdown').hide();

            if (status == 'I') {
                confirmacao = confirm('Iniciar a tarefa ' + currentTarefaId + '. Tem certeza?');
            } else if (status == 'P') {
                confirmacao = confirm('Pausar a tarefa ' + currentTarefaId + '. Tem certeza?');
            } else if (status == 'C') {
                confirmacao = confirm('Concluir a tarefa ' + currentTarefaId + '. Tem certeza?');
            } else if (status == 'D') {
                confirmacao = confirm('Cancelar a tarefa ' + currentTarefaId + '. Tem certeza?');
            }

            if (!confirmacao) {
                return; // Se o usuário cancelar, não faz nada
            }

            //console.log('Alterar status da tarefa', currentTarefaId, 'para', status, '-', text);

            let url = $("#modal-statustarefa").data('url'); //ordens/atualizaStatus

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    id_tarefa: currentTarefaId,
                    status: status
                },
                dataType: 'json',
                success: function (response) {
                    if (response.message) {
                        ajaxMessage(response.message, 5);
                    }

                    if (response.reload) {
                        $(".ajax_load").fadeIn(200).css("display", "flex");
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Tratar erro de requisição
                    console.error('Erro na requisição:', textStatus, errorThrown);
                }
            });
        });

        // Fechar dropdown ao clicar fora
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.btn-os2-att-status').length && !$(e.target).closest('.status-dropdown').length) {
                $('.status-dropdown').hide();
            }
        });
    });

    function ajaxMessage(message, time) {
        //console.log(message);
        var ajaxMessage = $(message);

        ajaxMessage.append("<div class='message_time'></div>");
        ajaxMessage.find(".message_time").animate({ "width": "100%" }, time * 1000, function () {
            $(this).parents(".message").fadeOut(200);
        });

        $(".ajax_response").append(ajaxMessage);
        ajaxMessage.effect("bounce");
    }

    const dataAtual = new Date().toISOString().split('T')[0]; // Formato da data atual (YYYY-MM-DD)

    // Seleciona o <td> que contém a data atual
    const linhaAtual = $(`table.minitable tbody td:contains(${dataAtual})`);

    if (linhaAtual.length) {
        // Faz a rolagem até o <td> correspondente ao dia atual
        $('html, body').animate({
            scrollTop: linhaAtual.offset().top - 100 // Ajuste de 100px para centralizar se necessário
        }, 500); // 500ms para a animação
    }

    function graficoDashDefault() {
        var urlDash = $('#url-dash').data('url');
        $.ajax({
            url: urlDash,
            method: 'GET',
            cache: false,
            dataType: 'json',
            success: function (retorno) {
                if ($('#graficoDespesas').length) {
                    graficoDespesas.data.datasets[0].data = [retorno.graficoDespesas.receitas, retorno.graficoDespesas.despesas];
                    graficoDespesas.update();
                }

                // Verifica se o gráfico de evolução mensal existe antes de atualizá-lo
                if ($('#graficoEvolucaoMensal').length) {
                    graficoEvolucaoMensal.data.labels = retorno.graficoEvolucao.meses;
                    graficoEvolucaoMensal.data.datasets[0].data = retorno.graficoEvolucao.despesas;
                    graficoEvolucaoMensal.data.datasets[1].data = retorno.graficoEvolucao.entradas;
                    graficoEvolucaoMensal.update();
                }
            },
            error: function (error) {
                //console.error('Erro ao buscar dados do servidor', error);
            }
        });
    }

    graficoDashDefault();

    // Configuração para o gráfico de despesas
    if ($('#graficoDespesas').length) {
        var ctxDespesas = $('#graficoDespesas')[0].getContext('2d');
        var graficoDespesas = new Chart(ctxDespesas, {
            type: 'doughnut', // Tipo do gráfico (pizza)
            data: {
                labels: ['Receitas', 'Despesas'],
                datasets: [{
                    label: ['R$ '],
                    data: [], // Dados do gráfico
                    backgroundColor: [
                        '#003e99', // Cor da Receita
                        '#a52834'
                    ],
                    borderColor: [
                        '#c2c2c2',
                        '#666666'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Permite que o gráfico ajuste a altura
            }
        });
    }

    if ($('#graficoEvolucaoMensal').length) {
        var ctxEvolucaoMensal = $('#graficoEvolucaoMensal')[0].getContext('2d');
        var graficoEvolucaoMensal = new Chart(ctxEvolucaoMensal, {
            type: 'bar', // Tipo do gráfico (linha)
            data: {
                labels: ['Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'], // Labels do gráfico
                datasets: [{
                    label: 'Despesas',
                    data: [],
                    backgroundColor: '#a52834'
                }, {
                    label: 'Receitas',
                    data: [],
                    backgroundColor: '#003e99'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Permite que o gráfico ajuste a altura
            }
        });
    }

    $(".fdash-mprev, .fdash-mnext").prop('disabled', true);
    $(".fdash-tprev, .fdash-tnext").prop('disabled', true);
    $(".fdash-wprev, .fdash-wnext").prop('disabled', true);

    $(".fdash-matu").on('click', function () { //clicando no botão mês atual
        var valor = $(this).data('valor'); //pego o valor do elemento clicado que é o mês atual
        var situacao = $('#situacao option:selected').text(); //pego o valor do select situação - todas, aberto, baixado
        $("#mes-escolhido").val(valor); //coloco o valor do botão que cliquei num input escondido que receber o mes escolhido        
        filtrosDash("1", valor); //chamo a função dos filtros do dashboard passando o valor do mes atual pra atualizar os gráficos
        $(".valor-vfiltro").text("mês: " + valor + " - (situação: " + situacao + ")"); //coloco o valor do mes atual no span que mostra o filtro
        $(this).addClass('active'); //coloco a classe active no botão que cliquei
        $(".fdash-today, .fdash-week, .fdash-all").removeClass('active'); //removo a classe active dos outros botões
        $(".fdash-mprev, .fdash-mnext").prop('disabled', false); //habilito os botões de mês anterior e próximo
        $(".fdash-tprev, .fdash-tnext").prop('disabled', true); //desabilito os botões de dia anterior e próximo
        $(".fdash-wprev, .fdash-wnext").prop('disabled', true); //desabilito os botões de semana anterior e próxima
    })

    $(".fdash-mprev").on('click', function () { //quando clicar na flecha de mês anterior
        var valor = $('#mes-escolhido').val(); //pego o valor do input escondido que tem o mes atual pois pra esse botão clicado estar ativo, automaticamente o botao escondido terá recebido o valor do mẽs
        var situacao = $('#situacao option:selected').text(); //pego o texto da situação escolhida pra colocar no span
        var partes = valor.split('/'); //separando o valor do input escondido que tem o mes atual
        var mes = parseInt(partes[0], 10) - 1; // O mês é zero-indexado
        var ano = parseInt(partes[1], 10); //pego o ano
        var mescompleto = new Date(ano, mes); //crio uma nova data com o ano e mes que peguei
        mescompleto.setMonth(mescompleto.getMonth() - 1); //subtraio 1 mês
        var mesFormatado = String(mescompleto.getMonth() + 1).padStart(2, '0') + '/' + mescompleto.getFullYear(); //formato o mês e ano pra ficar no padrão MM/AAAA
        //console.log(mesFormatado);
        $("#mes-escolhido").val(mesFormatado); // a cada clique no botão, o input escondido recebe o valor do mês anterior        
        filtrosDash("1", mesFormatado);
        $(".valor-vfiltro").text("mês: " + mesFormatado + " - (situação: " + situacao + ")")
    })

    $(".fdash-mnext").on('click', function () {
        var valor = $('#mes-escolhido').val();
        var situacao = $('#situacao option:selected').text();
        var partes = valor.split('/');
        var mes = parseInt(partes[0], 10) - 1; // O mês é zero-indexado
        var ano = parseInt(partes[1], 10);
        var mescompleto = new Date(ano, mes);
        mescompleto.setMonth(mescompleto.getMonth() + 1);
        var mesFormatado = String(mescompleto.getMonth() + 1).padStart(2, '0') + '/' + mescompleto.getFullYear();
        //console.log(mesFormatado);
        $("#mes-escolhido").val(mesFormatado);
        filtrosDash("1", mesFormatado);
        $(".valor-vfiltro").text("mês: " + mesFormatado + " - (situação: " + situacao + ")")
    })

    $(".fdash-today").on('click', function () {
        var valor = $(this).data('valor');
        var situacao = $('#situacao option:selected').text();
        $("#dia-escolhido").val(valor);
        filtrosDash("2", valor);
        $(".valor-vfiltro").text("Hoje - (situação: " + situacao + ")");
        $(this).addClass('active');
        $(".fdash-matu, .fdash-week, .fdash-all").removeClass('active');
        $(".fdash-mprev, .fdash-mnext").prop('disabled', true);
        $(".fdash-tprev, .fdash-tnext").prop('disabled', false);
        $(".fdash-wprev, .fdash-wnext").prop('disabled', true);
    })



    $(".fdash-tprev").on('click', function () {
        var valor = $('#dia-escolhido').val();
        var situacao = $('#situacao option:selected').text();
        var partes = valor.split('-');
        var ano = parseInt(partes[0], 10);
        var mes = parseInt(partes[1], 10) - 1; // O mês é zero-indexado
        var dia = parseInt(partes[2], 10);

        var dataCompleta = new Date(ano, mes, dia);
        dataCompleta.setDate(dataCompleta.getDate() - 1); // Subtrai um dia

        var diaFormatado = String(dataCompleta.getDate()).padStart(2, '0');
        var mesFormatado = String(dataCompleta.getMonth() + 1).padStart(2, '0');
        var dataFormatada = ano + '-' + mesFormatado + '-' + diaFormatado; // Formato YYYY-MM-DD

        $("#dia-escolhido").val(dataFormatada);
        filtrosDash("2", dataFormatada);

        if ($(".fdash-today").data('valor') == dataFormatada) {
            dataFormatada = "Hoje";
        } else {
            dataFormatada = diaFormatado + '/' + mesFormatado + '/' + ano;
        }
        $(".valor-vfiltro").text("dia: " + dataFormatada + " - (situação: " + situacao + ")")
    });

    $(".fdash-tnext").on('click', function () {
        var valor = $('#dia-escolhido').val();
        var situacao = $('#situacao option:selected').text();
        var partes = valor.split('-');
        var ano = parseInt(partes[0], 10);
        var mes = parseInt(partes[1], 10) - 1; // O mês é zero-indexado
        var dia = parseInt(partes[2], 10);

        var dataCompleta = new Date(ano, mes, dia);
        dataCompleta.setDate(dataCompleta.getDate() + 1); // Adiciona um dia

        var diaFormatado = String(dataCompleta.getDate()).padStart(2, '0');
        var mesFormatado = String(dataCompleta.getMonth() + 1).padStart(2, '0');
        var dataFormatada = ano + '-' + mesFormatado + '-' + diaFormatado; // Formato YYYY-MM-DD        
        $("#dia-escolhido").val(dataFormatada);
        filtrosDash("2", dataFormatada);

        if ($(".fdash-today").data('valor') == dataFormatada) {
            dataFormatada = "Hoje";
        } else {
            dataFormatada = diaFormatado + '/' + mesFormatado + '/' + ano;
        }
        $(".valor-vfiltro").text("dia: " + dataFormatada + " - (situação: " + situacao + ")")
    });


    $(".fdash-week").on('click', function () {
        var valor = $(this).data('valor');
        var situacao = $('#situacao option:selected').text();
        const referenceDate = new Date(valor);
        const dayOfWeek = referenceDate.getDay();
        const firstDayOfWeek = new Date(referenceDate);
        firstDayOfWeek.setDate(referenceDate.getDate() - dayOfWeek);
        const lastDayOfWeek = new Date(referenceDate);
        lastDayOfWeek.setDate(referenceDate.getDate() + (6 - dayOfWeek));
        const formatDate = (date) => date.toISOString().split('T')[0];

        var primeirodia = formatDate(firstDayOfWeek);
        var ultimodia = formatDate(lastDayOfWeek);

        $("#semana-escolhida").val(valor);
        filtrosDash("3", primeirodia, ultimodia);

        $(".valor-vfiltro").text("semana: " + formatarDataBr(primeirodia) + " até " + formatarDataBr(ultimodia) + " - (situação: " + situacao + ")");

        $(this).addClass('active');
        $(".fdash-matu, .fdash-today, .fdash-all").removeClass('active');
        $(".fdash-mprev, .fdash-mnext").prop('disabled', true);
        $(".fdash-tprev, .fdash-tnext").prop('disabled', true);
        $(".fdash-wprev, .fdash-wnext").prop('disabled', false);
    })

    $(".fdash-wprev").on('click', function () {
        var valor = $('#semana-escolhida').val();
        var situacao = $('#situacao option:selected').text();
        var partes = valor.split('-');
        var ano = parseInt(partes[0], 10);
        var mes = parseInt(partes[1], 10) - 1; // O mês é zero-indexado
        var dia = parseInt(partes[2], 10);

        var dataCompleta = new Date(ano, mes, dia);
        var diasParaVoltar = dataCompleta.getDay(); // Pega o dia da semana (0 = domingo, 6 = sábado)
        dataCompleta.setDate(dataCompleta.getDate() - diasParaVoltar); // Volta para o domingo da semana
        dataCompleta.setDate(dataCompleta.getDate() - 7); // Subtrai um dia

        var diaFormatado = String(dataCompleta.getDate()).padStart(2, '0');
        var mesFormatado = String(dataCompleta.getMonth() + 1).padStart(2, '0');
        var dataFormatada = ano + '-' + mesFormatado + '-' + diaFormatado; // Formato YYYY-MM-DD

        // Calcula o dia inicial (domingo)
        var diaInicial = new Date(dataCompleta);
        var diaInicialFormatado = diaInicial.getFullYear() + '-' +
            String(diaInicial.getMonth() + 1).padStart(2, '0') + '-' +
            String(diaInicial.getDate()).padStart(2, '0');

        // Calcula o dia final (sábado)
        var diaFinal = new Date(dataCompleta);
        diaFinal.setDate(diaFinal.getDate() + 6); // Avança 6 dias para chegar ao sábado
        var diaFinalFormatado = diaFinal.getFullYear() + '-' +
            String(diaFinal.getMonth() + 1).padStart(2, '0') + '-' +
            String(diaFinal.getDate()).padStart(2, '0');

        const formatDate = (date) => date.toISOString().split('T')[0];
        var primeirodia = formatDate(diaInicial);
        var ultimodia = formatDate(diaFinal);

        $("#semana-escolhida").val(dataFormatada);
        filtrosDash("3", primeirodia, ultimodia);
        $(".valor-vfiltro").text("semana: " + formatarDataBr(primeirodia) + " até " + formatarDataBr(ultimodia) + " - (situação: " + situacao + ")")
    });

    $(".fdash-wnext").on('click', function () {
        var valor = $('#semana-escolhida').val();
        var situacao = $('#situacao option:selected').text();
        var partes = valor.split('-');
        var ano = parseInt(partes[0], 10);
        var mes = parseInt(partes[1], 10) - 1; // O mês é zero-indexado
        var dia = parseInt(partes[2], 10);

        var dataCompleta = new Date(ano, mes, dia);
        var diasParaVoltar = dataCompleta.getDay(); // Pega o dia da semana (0 = domingo, 6 = sábado)
        dataCompleta.setDate(dataCompleta.getDate() - diasParaVoltar); // Volta para o domingo da semana
        dataCompleta.setDate(dataCompleta.getDate() + 7); // Adiciona um dia

        var diaFormatado = String(dataCompleta.getDate()).padStart(2, '0');
        var mesFormatado = String(dataCompleta.getMonth() + 1).padStart(2, '0');
        var dataFormatada = ano + '-' + mesFormatado + '-' + diaFormatado; // Formato YYYY-MM-DD        

        // Calcula o dia inicial (domingo)
        var diaInicial = new Date(dataCompleta);
        var diaInicialFormatado = diaInicial.getFullYear() + '-' +
            String(diaInicial.getMonth() + 1).padStart(2, '0') + '-' +
            String(diaInicial.getDate()).padStart(2, '0');

        // Calcula o dia final (sábado)
        var diaFinal = new Date(dataCompleta);
        diaFinal.setDate(diaFinal.getDate() + 6); // Avança 6 dias para chegar ao sábado
        var diaFinalFormatado = diaFinal.getFullYear() + '-' +
            String(diaFinal.getMonth() + 1).padStart(2, '0') + '-' +
            String(diaFinal.getDate()).padStart(2, '0');

        const formatDate = (date) => date.toISOString().split('T')[0];
        var primeirodia = formatDate(diaInicial);
        var ultimodia = formatDate(diaFinal);

        $("#semana-escolhida").val(dataFormatada);
        filtrosDash("3", primeirodia, ultimodia);
        $(".valor-vfiltro").text("semana: " + formatarDataBr(primeirodia) + " até " + formatarDataBr(ultimodia) + " - (situação: " + situacao + ")")
    });

    $(".fdash-all").on('click', function () {
        var situacao = $('#situacao option:selected').text();
        filtrosDash("4");
        $(".valor-vfiltro").text("Tudo - (situação: " + situacao + ")");
        $(this).addClass('active');
        $(".fdash-matu, .fdash-week, .fdash-today").removeClass('active');
        $(".fdash-mprev, .fdash-mnext").prop('disabled', true);
        $(".fdash-tprev, .fdash-tnext").prop('disabled', true);
        $(".fdash-wprev, .fdash-wnext").prop('disabled', true);
    })

    $("#filtrar-periodo-dash").on('click', function (e) {
        e.preventDefault();
        var primeirodia = $("#data-inicio").val();
        var ultimodia = $("#data-fim").val();

        if (primeirodia && ultimodia) {
            var dataIniMoment = moment(primeirodia, 'YYYY-MM-DD');
            var dataFimMoment = moment(ultimodia, 'YYYY-MM-DD');

            if (dataIniMoment.isAfter(dataFimMoment)) {
                alert('A data inicial deve ser menor ou igual a data final.');
                return; // Interrompe a execução da função
            }
        }
        var situacao = $('#situacao option:selected').text();
        filtrosDash();
        $(".valor-vfiltro").text("período: " + formatarDataBr(primeirodia) + " até " + formatarDataBr(ultimodia) + " - (situação: " + situacao + ")")
    })

    /**
     * 
     * @param {1-mês, 2-hoje, 3-semana, 4-tudo} botao 
     * @param {data-inicio} valorbtn
     * @param {data-fim} valorbtn2
     * @returns 
     */
    function filtrosDash(botao = "", valorbtn = "", valorbtn2 = "") {
        const dataini = $("#data-inicio").val();
        const datafim = $("#data-fim").val();
        const situacao = $("#situacao").val();
        var url = $('#url-dash').data('url');

        var dados = {};

        if (botao == "1") {
            dados = {
                mes: valorbtn,
                situacao: situacao
            };
        } else if (botao == "2") {
            dados = {
                hoje: valorbtn,
                situacao: situacao
            };
        } else if (botao == "3") {
            dados = {
                semana: true,
                datai: valorbtn,
                dataf: valorbtn2,
                situacao: situacao
            };
        } else if (botao == "4") {
            dados = {
                tudo: true,
                situacao: situacao
            };
        } else {
            dados = {
                semana: true,
                datai: dataini,
                dataf: datafim,
                situacao: situacao
            };
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: dados,
            cache: false,
            dataType: 'json',
            success: function (retorno) {
                if (retorno.message) {
                    ajaxMessage(retorno.message, 7);
                    $(".valor-vfiltro").text("");
                    return;
                }

                if ($('#graficoDespesas').length) {
                    var receitas = retorno.graficoDespesas.receitas || 0;
                    var despesas = retorno.graficoDespesas.despesas || 0;

                    graficoDespesas.data.datasets[0].data = [retorno.graficoDespesas.receitas, retorno.graficoDespesas.despesas];

                    var saldo = retorno.graficoDespesas.receitas - retorno.graficoDespesas.despesas;
                    receitas = formatarValor(receitas);
                    despesas = formatarValor(despesas);

                    if (saldo < 0) {
                        $(".cartao.saldo .valor-cartao").removeClass('positivo');
                        $(".cartao.saldo .valor-cartao").addClass('negativo');
                    } else {
                        $(".cartao.saldo .valor-cartao").removeClass('negativo');
                        $(".cartao.saldo .valor-cartao").addClass('positivo');
                    }

                    $(".cartao.entradas .valor-cartao").text("R$ " + receitas);
                    $(".cartao.despesas .valor-cartao").text("R$ " + despesas);
                    $(".cartao.saldo .valor-cartao").text("R$ " + formatarValor(saldo));
                    graficoDespesas.update();
                }

                if (retorno.graficoEvolucao) {
                    // Verifica se o gráfico de evolução mensal existe antes de atualizá-lo
                    if ($('#graficoEvolucaoMensal').length) {
                        graficoEvolucaoMensal.data.labels = retorno.graficoEvolucao.meses;
                        graficoEvolucaoMensal.data.datasets[0].data = retorno.graficoEvolucao.despesas;
                        graficoEvolucaoMensal.data.datasets[1].data = retorno.graficoEvolucao.entradas;
                        graficoEvolucaoMensal.update();
                    }
                }

            },
            error: function (xhr, status, error) {
                console.error('Erro ao aplicar filtros:', error);
            }
        });
    }

    var $linhaAtual = $('#linha-atual');
    if ($linhaAtual.length) {
        var $tbody = $linhaAtual.closest('tbody');
        var offset = $linhaAtual.position().top + $tbody.scrollTop() - $tbody.height() / 2;
        $tbody.animate({
            scrollTop: offset
        }, 600);
    }

    $(document).on('click', '#nvLocal', function () {
        $('#cdLocalFerramenta').modal('show');
        let url = $('#cdLocalFerramenta').data('url');
        $.post(url, { value: 'modal' }, function (response) {
            createLineLocal(response.dados);
        }, 'json');
    });

    $(document).on('click', '#btnMoverFerramenta', function () {
        $('#modalMovFerramenta').modal('show');
        $('#modalMovFerramenta').find('form')[0].reset();
        $("#modalMovFerramenta").find(".fornecedor-mov-row").prop('hidden', true);

        let url = $(this).data('url'); //equipamentos/refresh_local

        $.post($(this).data('url'), { value: 'refresh-local' }, function (response) {
            // Garante que 'dados' existe
            if (!response.dados || !Array.isArray(response.dados)) {
                console.error("Formato de resposta inválido.");
                return;
            }

            // Limpa os selects
            $('#localOrigemSelect, #localDestinoSelect').empty();

            // Adiciona a opção padrão
            const defaultOption = '<option value="">Selecione</option>';
            $('#localOrigemSelect').append(defaultOption);
            $('#localDestinoSelect').append(defaultOption);

            // Preenche os selects com os dados
            $.each(response.dados, function (i, local) {
                const option = `<option value="${local.id}">${local.descricao} (${local.desc_status})</option>`;
                $('#localOrigemSelect').append(option);
                $('#localDestinoSelect').append(option);
            });
        }, 'json');
    });

    $(document).on('click', '.listAlocado', function () {
        let url = $(this).data('url'); //equipamentos/listar_alocados
        let id = $(this).data('id'); //id do equipamento
        let eqp = $(this).closest('tr').find('td:eq(0)').text(); //nome do equipamento

        $("#listLocal").find('#nome-ferramenta').text(eqp);

        $.ajax({
            url: url,
            data: { id: id },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                let tbody = $("#listLocal").find('tbody');
                tbody.empty(); // Limpa o conteúdo atual da tabela
                if (!response.length) {
                    tbody.append("<tr><td colspan='100%'>NENHUM REGISTRO ENCONTRADO</td></tr>");
                    return;
                }
                response.forEach(item => {
                    const linha = $(`<tr></tr>`);
                    linha.append(`<td>${item.local_desc}</td>`);
                    linha.append(`<td>${item.last_user}</td>`);
                    linha.append(`<td>${item.qtde}</td>`);
                    tbody.append(linha);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
                    errorThrown, 5);
            }
        });
        // Exibe o modal

        $('#listLocal').modal('show');
    });

    $(document).on('click', '.kardex', function () {
        let url = $(this).data('url'); //equipamentos/listar_kardex
        let id = $(this).data('id'); //id do equipamento
        let eqp = $(this).closest('tr').find('td:eq(0)').text(); //nome do equipamento

        $("#listKardex").find('#nome-ferramenta').text(eqp);

        $.ajax({
            url: url,
            data: { id: id },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                let tbody = $("#listKardex").find('tbody');
                tbody.empty(); // Limpa o conteúdo atual da tabela
                if (!response.length) {
                    tbody.append("<tr><td colspan='100%'>NENHUM REGISTRO ENCONTRADO</td></tr>");
                    return;
                }
                response.forEach(item => {
                    let cor = 'green';
                    if (item.entrada == '0') {
                        cor = 'red';
                    }
                    const idMov = item.id_mov || '';
                    const linha = $(`<tr style="color: ${cor};"></tr>`);
                    linha.append(`<td>${idMov}</td>`);
                    linha.append(`<td>${item.data_formatada}</td>`);
                    linha.append(`<td>${item.local_nome}</td>`);
                    linha.append(`<td>${item.usuario_nome}</td>`);
                    linha.append(`<td style="text-align: center;">${item.entrada}</td>`);
                    linha.append(`<td style="text-align: center;">${item.saida}</td>`);
                    linha.append(`<td style="text-align: center;">${item.saldo}</td>`);
                    tbody.append(linha);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
                    errorThrown, 5);
            }
        });

        $('#listKardex').modal('show');
    });

    function linhasSolicitacoes(tbody, response, tipo, user = "user") {
        if (!response || !response.length) {
            tbody.append("<tr><td colspan='100%'>NENHUM REGISTRO ENCONTRADO</td></tr>");
            return;
        }
        response.forEach(item => {

            let botaoConfirm = '<button type="button" data-id="' + item.id + '" class="btn btn-secondary confirm-movimentacao">Visualizar</button>';
            if (user == "user" && tipo == "R") {
                botaoConfirm = '<button type="button" data-id="' + item.id + '" class="btn btn-success confirm-movimentacao">Confirmar</button>';
            }
            let eqp = limitarTexto(`<td>${item.equipamento_desc}</td>`, 25);
            let origem = `<td>${limitarTexto(item.local_origem_desc, 15)} (${limitarTexto(item.usuario_origem_nome, 15)})</td>`;
            let destino = limitarTexto(`<td>${limitarTexto(item.local_destino_desc, 15)} (${limitarTexto(item.usuario_destino_nome, 25)})</td>`, 25);


            const linha = $(`<tr></tr>`);
            linha.append(eqp);
            linha.append(origem);
            linha.append(destino);
            linha.append(`<td>${item.qtde}</td>`);
            linha.append(`<td>${botaoConfirm}</td>`);
            tbody.append(linha);
        });
    }

    ///**********SOLICITACOES AQUI */
    $(document).on('click', '#btnSolicitacoes', function () {
        let url = $(this).data('url'); //equipamentos/listar_solicitacoes

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                let tbodyRecebidas = $("#listSolicitacao").find('#tbody-recebidas');
                let tbodyEnviadas = $("#listSolicitacao").find('#tbody-enviadas');

                tbodyRecebidas.empty();
                tbodyEnviadas.empty();

                linhasSolicitacoes(tbodyRecebidas, response.recebidas, "R", response.user);
                linhasSolicitacoes(tbodyEnviadas, response.enviadas, "E", response.user);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
                    errorThrown, 5);
            }
        });
        // Exibe o modal

        $('#listSolicitacao').modal('show');
    });

    $(document).on('click', '.confirm-movimentacao', function () {
        let id = $(this).data('id'); // ID da solicitação
        let url = $('#modalConfirmSolicitacao').data('url'); //equipamentos/retorna_solicitacao

        // Define estado inicial dos botões para evitar piscar
        $('#modalConfirmSolicitacao #confirmarSolicitacao').prop('hidden', true);
        $('#modalConfirmSolicitacao #cancelarSolicitacao').prop('hidden', true);

        // Limpa textos da modal
        $('#modalConfirmSolicitacao .modal-soldesk').text('');

        // Abre modal já com botões escondidos
        $("#modalConfirmSolicitacao").modal('show');

        $.ajax({
            url: url,
            data: { id: id },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                // Só mostra se a resposta permitir
                if (!response[0].escondeCancelar) {
                    $('#modalConfirmSolicitacao #cancelarSolicitacao').prop('hidden', false);
                }
                if (!response[0].escondeConfirmar) {
                    $('#modalConfirmSolicitacao #confirmarSolicitacao').prop('hidden', false);
                }

                $('#modalConfirmSolicitacao #id_mov').val(response[0].id);
                $('#modalConfirmSolicitacao #soldesk-eqp').text(limitarTexto(response[0].equipamento_desc));
                $('#modalConfirmSolicitacao #soldesk-lorigem').text(limitarTexto(response[0].local_origem_desc));
                $('#modalConfirmSolicitacao #soldesk-qtde').text(response[0].qtde);
                $('#modalConfirmSolicitacao #soldesk-data').text(response[0].data_formatada);
                $('#modalConfirmSolicitacao #soldesk-udestino').text(response[0].usuario_destino_nome);
                $('#modalConfirmSolicitacao #soldesk-ldestino').text(response[0].local_destino_desc);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
                    errorThrown, 5);
            }
        });
    });

    $(document).on('click', '#cancelarSolicitacao', function () {
        let id = $('#id_mov').val(); // ID da solicitação
        let url = $(this).data('url'); //equipamentos/cancelar_mov

        $.ajax({
            url: url,
            data: { id: id },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.reload) {
                    window.location.reload();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' +
                    errorThrown, 5);
            }
        });
    });

    $("#addLocal").on('click', function () {
        let status = $('#stEqp').val();
        let descricao = $('#descLocal').val();
        let url = $('#cdLocalFerramenta').data('url'); //equipamentos/salvar_local

        $.ajax({
            url: url,
            data:
            {
                descricao: descricao,
                status: status
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.status == "success") {
                    createLineLocal(response.dados);
                    ajaxMessage(response.message, 5);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxMessage('Falha na requisição: ' + textStatus + ' - ' + errorThrown, 5);
            }
        });
    });

    function createLineLocal(dados) {
        const $tbody = $("#eqpLocal-list tbody");
        $tbody.empty(); // Limpa o conteúdo atual da tabela

        if (!dados.length) {
            $tbody.append("<tr><td colspan='100%'>NENUHM REGISTRO ENCONTRADO</td></tr>");
            return;
        }
        let status = "";
        dados.forEach(item => {
            if (item.status == "1") {
                status = "Estoque";
            } else if (item.status == "2") {
                status = "Entrada";
            } else if (item.status == "3") {
                status = "Inativo";
            } else if (item.status == "4") {
                status = "Alocados";
            } else {
                status = "Manutenção";
            }
            var botaoEdit = '<button type="button" class="btn btn-secondary list-edt edit-eqplocal"><i class="fa fa-pen"></i></button>';
            var botaoConfirm = '<button type="button" data-id="' + item.id_encode + '" class="btn btn-success confirm-edit-eqplocal" hidden><i class="fa fa-check"></i></button>';
            var botaoDelete = '<button type="button" class="btn btn-secondary list-del eqplocal-delete" data-id="' + item.id_encode + '"><i class="fa fa-trash"></i></button>';
            var botaoCancel = '<button type="button" class="btn btn-danger cancel-edit-eqplocal" hidden><i class="fa fa-xmark"></i></button>';

            const linha = $(`<tr></tr>`);
            linha.append(`<td style="width:20%;">${item.descricao}</td>`);
            linha.append(`<td style="width:20%;">${status}</td>`);
            linha.append(`<td style="width:5%;">${botaoEdit}${botaoConfirm}</td>`);
            linha.append(`<td style="width:5%;">${botaoDelete}${botaoCancel}</td>`);
            $tbody.append(linha);
        });

        $("#cdLocalFerramenta").find("input[name='descLocal']").val('');
        $("#cdLocalFerramenta").find("select[name='stEqp']").val('');
    }

    $(document).on("click", ".eqplocal-delete", function (e) {
        let url = $("#cdLocalFerramenta").data("delete"); //checklist/excluirGrupo
        let item = $(this).closest("tr");
        let id = $(this).data('id');

        if (confirm('Tem certeza que deseja excluir este item?')) {
            $.ajax({
                url: url,
                data: { id: id },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.status == 'success') {
                        var tbody = $('#eqpLocal-list tbody');
                        item.remove();

                        // Verifica se a tabela ficou vazia após a remoção
                        if (tbody.find('tr').length === 0) {
                            tbody.append('<tr><td colspan="100%">NENHUM REGISTRO ENCONTRADO</td></tr>');
                        }
                        ajaxMessage(response.message, 5);
                    } else {
                        ajaxMessage(response.message, 5);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Falha na requisição: ' + textStatus + ' - ' + errorThrown);
                }
            });
        }
    });

    $(document).on("click", ".edit-eqplocal", function (e) {
        e.preventDefault();

        let row = $(this).closest("tr");
        let descCell = row.find("td:eq(0)");

        // Armazena os valores antigos nos atributos data da linha
        row.data("oldValues", {
            descricao: descCell.text()
        });

        let oldValues = row.data("oldValues");

        // Substitui os textos por inputs                
        descCell.html(`<input type="text" class="form-control" value="${oldValues.descricao}" style="height: 30px; width: 100%;">`);

        // Oculta o botão de edição e mostra os de confirmação/cancelamento
        row.find(".edit-eqplocal").hide();
        row.find(".confirm-edit-eqplocal").prop("hidden", false);
        row.find(".eqplocal-delete").hide();
        row.find(".cancel-edit-eqplocal").prop("hidden", false);
    });

    $(document).on("click", ".cancel-edit-eqplocal", function () {
        let row = $(this).closest("tr");

        // Recupera os valores antigos armazenados nos atributos data
        let oldValues = row.data("oldValues");

        row.find("td:eq(0)").text(oldValues.descricao);

        // Restaura os botões
        row.find(".edit-eqplocal").show();
        row.find(".confirm-edit-eqplocal").prop("hidden", true);
        row.find(".eqplocal-delete").show();
        row.find(".cancel-edit-eqplocal").prop("hidden", true);
    });

    $(document).on("click", ".confirm-edit-eqplocal", function () {
        let row = $(this).closest("tr");
        let descricao = row.find("td:eq(0) input").val();
        let id = $(this).data('id');
        let url = $('#cdLocalFerramenta').data('url');

        $.ajax({
            url: url,
            type: "POST",
            data: {
                id: id,
                descricao: descricao
            },
            dataType: "json",
            success: function (response) {
                // Atualiza as células com os novos valores
                row.find("td:eq(0)").text(descricao);

                ajaxMessage(response.message, 5);
                row.find(".edit-eqplocal").show();
                row.find(".confirm-edit-eqplocal").prop("hidden", true);
                row.find(".eqplocal-delete").show();
                row.find(".cancel-edit-eqplocal").prop("hidden", true);
            },
            error: function () {
                alert("Erro ao salvar os dados. Tente novamente.");
            }
        });
    });

    $(document).on('change', '#descFerramenta', function () {
        let ferramentaId = $(this).val();
        let url = $(this).data('url');
        // Supondo que você tenha um endpoint para buscar a quantidade em estoque
        $.ajax({
            url: url,
            method: 'POST',
            data:
            {
                id: ferramentaId
            },
            dataType: 'json',
            success: function (response) {
                $('#localOrigemSelect option').each(function () {
                    let option = $(this);
                    let val = Number(option.val());

                    // Procura no array de resposta se existe um objeto com essa chave
                    let local = response.find(obj => obj.hasOwnProperty(val));

                    if (local) {
                        let qtde = local[val];

                        // Atualiza o texto da opção com a quantidade
                        let originalText = option.data('original-text');
                        if (!originalText) {
                            originalText = option.text();
                            option.data('original-text', originalText); // Salva o texto original
                        }

                        option.text(`${originalText} (${qtde})`);
                        option.prop('disabled', false).data('no-stock', false);
                    } else {
                        option.prop('disabled', true).data('no-stock', true);
                    }


                });

                $('#localDestinoSelect option').each(function () {
                    let option = $(this);
                    let val = Number(option.val());

                    // Procura no array de resposta se existe um objeto com essa chave
                    let local = response.find(obj => obj.hasOwnProperty(val));

                    if (local) {
                        let qtde = local[val];

                        // Atualiza o texto da opção com a quantidade
                        let originalText = option.data('original-text');
                        if (!originalText) {
                            originalText = option.text();
                            option.data('original-text', originalText); // Salva o texto original
                        }

                        option.text(`${originalText} (${qtde})`);
                    }
                });
            },
            error: function () {
                console.log('Erro ao verificar o estoque.');
            }
        });
    });



    $(document).ready(function () {

        const $checkbox = $('#emp_os_financeiro_auto');
        const $toggleBtn = $('#toggle_fin_config_modal');
        const $modal = $('#parFinConfModal');

        function updateButtonState() {
            if ($checkbox.is(':checked')) {
                $toggleBtn.prop('disabled', false);
            } else {
                $toggleBtn.prop('disabled', true);
                $modal.hide();
            }
        }

        // estado inicial
        updateButtonState();

        // captura o change e dispara verificação AJAX
        $checkbox.on('change', function () {
            updateButtonState();

            if ($(this).is(':checked')) {
                const url = $(this).data('url');

                // desabilita o checkbox durante a requisição
                $checkbox.prop('disabled', true);

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (!response.status) {
                            $checkbox.prop('checked', false);
                            ajaxMessage(response.message, 10);
                            updateButtonState();
                        }
                    },
                    error: function (xhr, status, error) {
                        alert("Erro na requisição: " + error);
                        $checkbox.prop('checked', false);
                        updateButtonState();
                    },
                    complete: function () {
                        // reabilita o checkbox após a requisição
                        $checkbox.prop('disabled', false);
                    }
                });
            }
        });

        // toggle modal suspenso
        $toggleBtn.on('click', function (e) {
            e.stopPropagation();

            if ($modal.is(':visible')) {
                $modal.css('animation', 'slideUp 0.3s ease-out');
                setTimeout(() => $modal.hide(), 300);
            } else {
                const offset = $toggleBtn.offset();
                const btnHeight = $toggleBtn.outerHeight();

                $modal.css({
                    top: offset.top + btnHeight + 6,
                    left: offset.left,
                    position: 'absolute'
                }).show().css('animation', 'slideDown 0.3s ease-out');
            }
        });

        // impedir que clique dentro feche
        $modal.on('click', function (e) {
            e.stopPropagation();
        });
    });

    $(document).on('click', '#btnPreOs, #atalhoPreOS', function () {
        const modalPreos = $("#modalPreOs");
        // Limpar todos os inputs dentro da modal
        modalPreos.find('input').val('');
        // Resetar selects (incluindo select2, se houver)
        modalPreos.find('select').val('').trigger('change');
        // Limpar tabela/lista de itens
        $("#preos_lista_servicos").empty();
        $("#preos_lista_produtos").empty();
        updatePreosTablesVisibility();
        modalPreos.modal("show");

    });

    $(document).on('change', '#preos_cliente', function () {
        let clienteId = $(this).val();

        let url = $(this).data('url');//ordens/verifica_os

        $.ajax({
            url: url,
            method: 'POST',
            data:
            {
                preosIdCli: clienteId
            },
            dataType: 'json',
            success: function (response) {

                if (response.success) {
                    $("#preos_ultimas_ordens .lateral-itens").empty();

                    response.ordens.forEach(function (ordem) {
                        $("#preos_ultimas_ordens .lateral-itens").append(
                            `<div data-id="${ordem.id}" class="preos-lateral-item">#${ordem.id}</div>`
                        );
                    });

                } else {
                    $("#preos_ultimas_ordens .lateral-itens").empty().append(
                        `<div class="preos-lateral-item" style="color: #666; font-style: italic;">Nenhuma OS encontrada</div>`
                    );
                }
            },
            error: function () {
                console.log('Erro ao buscar dados do cliente.');
            }
        });


    });

    $(document).on('click', '#preos_btn_buscar', function () {
        let osId = $("#preos_filtro_ultimas").val();

        let url = $("#preos_cliente").data('url'); //ordens/verifica_os

        $.ajax({
            url: url,
            method: 'POST',
            data:
            {
                osId: osId
            },
            dataType: 'json',
            success: function (response) {

                if (response.success) {
                    $("#preos_ultimas_ordens .lateral-itens").empty();

                    response.ordens.forEach(function (ordem) {
                        $("#preos_ultimas_ordens .lateral-itens").append(
                            `<div data-id="${ordem.id}" class="preos-lateral-item">#${ordem.id}</div>`
                        );
                    });

                } else {
                    $("#preos_ultimas_ordens .lateral-itens").empty().append(
                        `<div class="preos-lateral-item" style="color: #666; font-style: italic;">Nenhuma OS encontrada</div>`
                    );

                    $('#busca_os_group').addClass('shake-horizontal');
                    setTimeout(() => {
                        $('#busca_os_group').removeClass('shake-horizontal');
                    }, 600);

                    $("#preos_filtro_ultimas").val("");
                }
            },
            error: function () {
                console.log('Erro ao buscar dados do cliente.');
            }
        });
    });

    function servicoJaExiste(servicoId, operadorId) {
        let existe = false;
        $("#preos_lista_servicos tr").each(function () {
            if ($(this).data("servico") == servicoId && $(this).data("operador") == operadorId) {
                existe = true;
                return false; // interrompe o loop
            }
        });
        return existe;
    }

    function produtoJaExiste(produtoId) {
        let existe = false;
        $("#preos_lista_produtos tr").each(function () {
            if ($(this).data("produto") == produtoId) {
                existe = true;
                return false;
            }
        });
        return existe;
    }

    function updatePreosTablesVisibility() {
        // Serviços
        const $servBody = $("#preos_lista_servicos");
        const $servTable = $servBody.closest("table");
        const $servTitle = $servTable.prev("h6");

        if ($servBody.children("tr").length > 0) {
            $servTitle.show();
            $servTable.show();
        } else {
            $servTitle.hide();
            $servTable.hide();
        }

        // Produtos
        const $prodBody = $("#preos_lista_produtos");
        const $prodTable = $prodBody.closest("table");
        const $prodTitle = $prodTable.prev("h6");

        if ($prodBody.children("tr").length > 0) {
            $prodTitle.show();
            $prodTable.show();
        } else {
            $prodTitle.hide();
            $prodTable.hide();
        }
    }

    function addServicoLinha(servico, servicoId, operador, operadorId, qtd) {
        let linha = `
        <tr data-servico="${servicoId}" data-operador="${operadorId}">
            <td>${servico}</td>
            <td>${operador}</td>
            <td>${qtd}</td>
            <td class="preos-item-remove">&#10005;</td>
        </tr>
    `;
        $("#preos_lista_servicos").append(linha);
    }

    function addProdutoLinha(produto, produtoId, qtd) {
        let linha = `
        <tr data-produto="${produtoId}">
            <td>${produto}</td>
            <td>${qtd}</td>
            <td class="preos-item-remove">&#10005;</td>
        </tr>
    `;
        $("#preos_lista_produtos").append(linha);
    }

    $(document).ready(function () {

        // === FUNÇÕES DE CONSTRUÇÃO DAS LINHAS ===



        // Adicionar serviço
        $("#preos_add_servico").on("click", function () {
            let servico = $("#preos_servico option:selected").text();
            let servicoId = $("#preos_servico").val();
            let operador = $("#preos_operador option:selected").text();
            let operadorId = $("#preos_operador").val();
            let qtd = $("#preos_serv_qtd").val();

            if (!servicoId || !operadorId || !qtd) {
                alert("Preencha todos os campos de serviço.");
                return;
            }

            if (servicoJaExiste(servicoId, operadorId)) {
                alert("O serviço escolhido já foi atribuído para esse operador!");
                return;
            }

            addServicoLinha(servico, servicoId, operador, operadorId, qtd);
            updatePreosTablesVisibility();


            // limpa campos
            $("#preos_servico").val("").trigger("change");
            $("#preos_operador").val("").trigger("change");
            $("#preos_serv_qtd").val("");
        });

        // Adicionar produto
        $("#preos_add_produto").on("click", function () {
            let produto = $("#preos_mat option:selected").text();
            let produtoId = $("#preos_mat").val();
            let qtd = $("#preos_mat_qtd").val();

            if (!produtoId || !qtd) {
                alert("Preencha todos os campos de produto.");
                return;
            }

            if (produtoJaExiste(produtoId)) {
                alert("Esse produto/material já foi adicionado na lista!");
                return;
            }

            addProdutoLinha(produto, produtoId, qtd);
            updatePreosTablesVisibility();

            // limpa campos
            $("#preos_mat").val("").trigger("change");
            $("#preos_mat_qtd").val("");
        });

        // Remover linha (funciona para ambas as tabelas)
        $(document).on("click", ".preos-item-remove", function () {
            $(this).closest("tr").remove();
            updatePreosTablesVisibility();
        });
    });

    $(document).on('click', '.preos-lateral-item', function () {
        const id = $(this).data('id');

        if (!id) {
            return; // Se não tiver data-id, não faz nada
        }

        let url = $("#preos_ultimas_ordens").data('url');

        $.ajax({
            url: url,
            method: 'POST',
            data:
            {
                id: id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.servicos && response.servicos.length) {
                        $("#preos_lista_servicos").empty();
                        response.servicos.forEach(function (servico) {
                            addServicoLinha(servico.servico, servico.id_servico, servico.operador, servico.id_colaborador, servico.qtde);
                        });
                    } else {
                        $("#preos_lista_servicos").empty();
                    }

                    if (response.materiais && response.materiais.length) {
                        $("#preos_lista_produtos").empty();
                        response.materiais.forEach(function (produto) {
                            addProdutoLinha(produto.material, produto.id_materiais, produto.qtde);
                        });
                    } else {
                        $("#preos_lista_produtos").empty();
                    }
                    updatePreosTablesVisibility();
                }
            },
            error: function () {
                console.log('Erro ao buscar dados da OS.');
            }
        });
    });

    //função pra carregar os dados da pré-OS na OS
    async function preencherOS(preOS) {
        // preOS = { clienteId: 123, servicos: [...], materiais: [...] }

        // 1 Seleciona cliente
        if (preOS.clienteId) {
            $('#cliente-os').val(preOS.clienteId).trigger('change');
            await aguardaAjax(); // espera se houver requisição ajax ao mudar o cliente
        }

        // Função auxiliar para aguardar que AJAX ou DOM esteja pronto
        function aguardaAjax(ms = 100) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        // 2 Adiciona serviços
        if (preOS.servicos && preOS.servicos.length > 0) {

            for (let i = 0; i < preOS.servicos.length; i++) {
                let item = preOS.servicos[i];

                // se não for a primeira linha, clica no botão "+" para criar nova linha
                if (i > 0) {
                    $('.novatarefa').click();
                    await aguardaAjax(200); // espera linha ser criada
                }

                // pega a última linha de serviço
                let linha = $('.ordens-form.linhatarefa').last();

                // preenche os selects e inputs
                linha.find('.selectServico').val(item.servicoId).trigger('change');
                linha.find('.selectOperador').val(item.operadorId).trigger('change');
                linha.find('#qtd_servico').val(item.qtd).trigger('input');

                // atualiza unidade do serviço
                let unidade = linha.find('.selectServico option:selected').data('unidade');
                linha.find('.unidade-servico').text(unidade || '');
            }
        }

        // 3 Adiciona materiais
        if (preOS.materiais && preOS.materiais.length > 0) {
            for (let i = 0; i < preOS.materiais.length; i++) {
                let item = preOS.materiais[i];

                if (i > 0) {
                    $('.novomat').click();
                    await aguardaAjax(200); // espera linha ser criada
                }

                // pega a última linha de material
                let linha = $('.ordens-form.linha-material').last();

                linha.find('.selectMaterial').val(item.materialId).trigger('change');
                const qtdeInput = linha.find('.qtde_material')[0];
                const anInstance = AutoNumeric.getAutoNumericElement(qtdeInput);
                if (anInstance) {
                    anInstance.set(item.qtd);
                } else {
                    linha.find('.qtde_material').val(item.qtd).trigger('keyup');
                }

                calculaTotalLinhaMaterial();

                // atualiza unidade e valores
                let unidade = linha.find('.selectMaterial option:selected').data('unidade');
                let vunit = linha.find('.selectMaterial option:selected').data('valor');
                linha.find('.unidade-mat').text(unidade || '');
                linha.find('.vunit_material').val(vunit || '');
            }
        }
    }

    $('#btnCriarOS').on('click', function () {
        let clienteId = $('#preos_cliente').val();

        // Verifica se o cliente foi selecionado
        if (!clienteId) {
            alert("Selecione um cliente antes de criar a OS!");
            return; // Interrompe a execução
        }

        // 2. Verifica se há serviços ou materiais adicionados
        let temServicos = $('#preos_lista_servicos tr').length > 0;
        let temMateriais = $('#preos_lista_produtos tr').length > 0;

        if (!temServicos && !temMateriais) {
            alert("Adicione pelo menos um serviço ou um material antes de criar a OS!");
            return;
        }

        // 1 Monta o objeto preOS a partir da modal
        let preOS = {
            clienteId: clienteId,
            servicos: [],
            materiais: []
        };

        // Serviços
        $('#preos_lista_servicos tr').each(function () {
            let servicoId = $(this).data('servico');
            let operadorId = $(this).data('operador');
            let qtd = $(this).find('td:nth-child(3)').text();
            if (servicoId && operadorId) {
                preOS.servicos.push({ servicoId, operadorId, qtd });
            }
        });

        // Materiais
        $('#preos_lista_produtos tr').each(function () {
            let materialId = $(this).data('produto');
            let qtd = $(this).find('td:nth-child(2)').text();
            if (materialId) {
                preOS.materiais.push({ materialId, qtd });
            }
        });

        // 3 Salva preOS no localStorage temporariamente para usar após carregar a página
        localStorage.setItem('preOS', JSON.stringify(preOS));

        // 2 Redireciona para a página de OS tradicional
        // Podemos passar clienteId como query string para já selecionar o cliente
        let url = $(this).data("url");
        window.location.href = url;
    });

    $(document).ready(function () {
        let preOS = localStorage.getItem('preOS');
        if (preOS) {
            preOS = JSON.parse(preOS);
            preencherOS(preOS); // função que você já tem
            localStorage.removeItem('preOS'); // evita repetição
        }
    });

    // $(document).on('click', '#buscar-ordens', function (e) {
    //     e.preventDefault();
    //     var form = $("#form-oslist");
    //     var formData = form.serialize();
    //     var url = form.attr('action');

    //     $.ajax({
    //         url: url,
    //         type: 'POST',
    //         data: formData,
    //         dataType: 'json',
    //         success: function (response) {
    //             // Processa a resposta do servidor
    //             if (response.success) {
    //                 // Código para sucesso
    //                 console.log('Busca realizada com sucesso');
    //             } else {
    //                 // Código para erro
    //                 console.log('Erro na busca:', response.message);
    //             }
    //         },
    //         error: function (xhr, status, error) {
    //             console.error('Erro na requisição:', error);
    //         }
    //     });
    // });



    /**PAGINAÇÃO LISTAGEM DE OS - INÍCIO */

    // Função para reinicializar o TableSorter específico desta página
    function reinicializarTableSorter() {
        // Sua lógica atual de inicialização (mantém exatamente como está)
        if ($('#os-tipo').length) {
            tableSorter("#ordens-list", {
                headers: {
                    2: { sorter: 'data-status' },
                    5: { sorter: 'datetimeBR', empty: 'bottom' },
                    7: { sorter: 'number' }
                }
            });
        } else {
            tableSorter("#ordens-list", {
                headers: {
                    2: { sorter: 'data-status' },
                    4: { sorter: 'datetimeBR', empty: 'bottom' },
                    6: { sorter: 'number' }
                }
            });
        }
    }

    function initPaginacaoAjax(config) {
        var registrosPorPaginaAtual = $(config.limitSelector).val() || 50;
        var filtrosAplicados = {}; // Filtros congelados do último clique em "Buscar"

        // Event listeners para paginação (MANTÉM como está)
        $(document).on('click', config.paginacao + ' .os1pg-link', function (e) {
            e.preventDefault();
            var pagina = $(this).data('page');
            if (pagina) {
                carregarPagina(pagina, registrosPorPaginaAtual, filtrosAplicados);
            }
        });

        // Event listener para mudança de registros por página
        $(document).on('change', config.limitSelector, function () {
            registrosPorPaginaAtual = $(this).val();
            carregarPagina(1, registrosPorPaginaAtual, filtrosAplicados);
        });

        // Event listener para o botão buscar
        if (config.formSelector && config.btnBuscarSelector) {
            $(document).on('click', config.btnBuscarSelector, function (e) {
                e.preventDefault();

                // Capturar dados do formulário
                var form = $(config.formSelector);
                var formData = form.serializeArray();

                // Converter para objeto
                filtrosAplicados = {};
                $.each(formData, function (i, field) {
                    if (filtrosAplicados[field.name]) {
                        // Arrays (como os-status[])
                        if (!Array.isArray(filtrosAplicados[field.name])) {
                            filtrosAplicados[field.name] = [filtrosAplicados[field.name]];
                        }
                        filtrosAplicados[field.name].push(field.value);
                    } else {
                        filtrosAplicados[field.name] = field.value;
                    }
                });

                // Carregar primeira página com filtros
                carregarPagina(1, registrosPorPaginaAtual, filtrosAplicados);
            });
        }

        // Função para carregar página
        function carregarPagina(pagina, registrosPorPagina = null, filtros = {}) {
            if (!registrosPorPagina) {
                registrosPorPagina = registrosPorPaginaAtual;
            }

            mostrarLoading();

            // Combinar paginação + filtros
            var dados = {
                page: pagina,
                limit: registrosPorPagina,
                ...filtros
            };

            let url = $(config.container).data("url");

            $.ajax({
                url: url,
                type: 'POST',
                data: dados,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        atualizarTabela(response.data.ordens);
                        atualizarPaginacao(response.data.paginacao);
                    } else {
                        alert('Erro ao carregar dados: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    alert('Erro ao carregar dados. Tente novamente.');
                },
                complete: function () {
                    esconderLoading();
                }
            });
        }

        // Função para atualizar tabela
        function atualizarTabela(registros) {
            // Destruir TableSorter se existir
            if (config.usaTableSorter && $.fn.tablesorter && $(config.tabela).hasClass('tablesorter')) {
                $(config.tabela).trigger('destroy.pager').trigger('destroy');
            }

            var tbody = $(config.tabela + ' tbody');
            tbody.empty();

            if (registros && registros.length > 0) {
                tbody.html(registros);
            } else {
                var colspan = $(config.tabela + ' thead th').length || '100%';
                tbody.html('<tr><td colspan="' + colspan + '">NENHUM REGISTRO ENCONTRADO</td></tr>');
            }

            // Reinicializar TableSorter se configurado
            if (config.usaTableSorter && config.reinicializarTableSorter) {
                setTimeout(function () {
                    config.reinicializarTableSorter();
                }, 100);
            }
        }

        // Função para atualizar a paginação
        function atualizarPaginacao(paginacao) {
            var container = $(config.paginacao);
            container.html(paginacao); // O HTML da paginação já vem pronto do servidor

            // Importante: após atualizar a paginação, sincronizar a variável
            registrosPorPaginaAtual = $(config.limitSelector).val();
        }

        // Função para mostrar loading
        function mostrarLoading() {
            var tbody = $(config.tabela + ' tbody');
            var colspan = $(config.tabela + ' thead th').length || '100%';
            tbody.html('<tr><td colspan="' + colspan + '"><i class="fa fa-spinner fa-spin"></i> Carregando...</td></tr>');

            if (config.paginacao) {
                $(config.paginacao).css('opacity', '0.5');
            }
        }

        // Função para esconder loading
        function esconderLoading() {
            if (config.paginacao) {
                $(config.paginacao).css('opacity', '1');
            }
        }

        // Retornar API pública (opcional)
        return {
            carregarPagina: carregarPagina,
            aplicarFiltros: function () {
                if (config.btnBuscarSelector) {
                    $(config.btnBuscarSelector).click();
                }
            }
        };
    }

    function salvarFiltrosLocalStorage() {
        // Só salvar se estiver na página de ordens
        if (!$('#ordens-list-container').length) {
            return;
        }

        var filtros = {
            status: [],
            tipo: $('#os-tipo').val(),
            buscarPor: $('#os-buscar-por').val(),
            cliente: $('#os-cli').val(),
            tarefa: $('#os-tarefa').val(),
            operador: $('#os-operador').val(),
            segmento: $('#os-segmento').val(),
            buscaGeral: $('#os-busca-geral').val(),
            ordenarPor: $('#os-ordenar-por').val(),
            ordem: $('#oslist-order1-sort').val(),
            dataInicio: $('#os-datai').val(),
            dataFim: $('#os-dataf').val()
        };

        // Capturar status marcados
        $('input[name="os-status[]"]:checked').each(function () {
            filtros.status.push($(this).val());
        });

        localStorage.setItem('oslist_filtros', JSON.stringify(filtros));
    }

    function restaurarFiltrosLocalStorage() {
        // Só salvar se estiver na página de ordens
        if (!$('#ordens-list-container').length) {
            return;
        }

        var filtrosSalvos = localStorage.getItem('oslist_filtros');

        if (filtrosSalvos) {
            try {
                var filtros = JSON.parse(filtrosSalvos);

                // Restaurar checkboxes de status
                $('input[name="os-status[]"]').prop('checked', false);
                if (filtros.status && filtros.status.length > 0) {
                    filtros.status.forEach(function (statusId) {
                        $('#os-status-' + statusId).prop('checked', true);
                    });
                }

                // Restaurar outros campos
                if (filtros.tipo) $('#os-tipo').val(filtros.tipo);
                if (filtros.buscarPor) $('#os-buscar-por').val(filtros.buscarPor);
                if (filtros.cliente) $('#os-cli').val(filtros.cliente).trigger('change');
                if (filtros.tarefa) $('#os-tarefa').val(filtros.tarefa).trigger('change');
                if (filtros.operador) $('#os-operador').val(filtros.operador).trigger('change');
                if (filtros.segmento) $('#os-segmento').val(filtros.segmento).trigger('change');
                if (filtros.buscaGeral) $('#os-busca-geral').val(filtros.buscaGeral);
                if (filtros.ordenarPor) $('#os-ordenar-por').val(filtros.ordenarPor);
                if (filtros.ordem) {
                    $('#oslist-order1-sort').val(filtros.ordem);

                    // NOVO: Atualizar o estado visual do botão também
                    var $btn = $('#toggle-sort-oslist');
                    if (filtros.ordem === 'desc') {
                        $btn.attr('title', 'DECRESCENTE');
                        $btn.find('i').attr('class', 'fa fa-arrow-up-z-a');
                        $btn.html('<i class="fa fa-arrow-up-z-a"></i> DECRE');
                    } else {
                        $btn.attr('title', 'CRESCENTE');
                        $btn.find('i').attr('class', 'fa fa-arrow-down-a-z');
                        $btn.html('<i class="fa fa-arrow-down-a-z"></i> CRESC');
                    }
                }
                if (filtros.dataInicio) $('#os-datai').val(filtros.dataInicio);
                if (filtros.dataFim) $('#os-dataf').val(filtros.dataFim);

                // Importante: disparar change no buscar-por para mostrar o campo correto
                $("#os-buscar-por").trigger("change");

            } catch (e) {
                console.log('Erro ao restaurar filtros:', e);
            }
        }
    }

    $(document).ready(function () {
        // Event listener para buscar (NOVO/MODIFICADO)
        // $(document).on('click', '#buscar-ordens', function (e) {
        //     e.preventDefault();

        //     // Capturar dados do formulário
        //     var form = $("#form-oslist");
        //     var formData = form.serializeArray();

        //     // Converter para objeto
        //     filtrosAplicados = {};
        //     $.each(formData, function (i, field) {
        //         if (filtrosAplicados[field.name]) {
        //             // Arrays (como os-status[])
        //             if (!Array.isArray(filtrosAplicados[field.name])) {
        //                 filtrosAplicados[field.name] = [filtrosAplicados[field.name]];
        //             }
        //             filtrosAplicados[field.name].push(field.value);
        //         } else {
        //             filtrosAplicados[field.name] = field.value;
        //         }
        //     });

        //     // Carregar primeira página com filtros
        //     carregarPagina(1, registrosPorPaginaAtual, filtrosAplicados);
        // });

        var paginacaoOsList = initPaginacaoAjax({
            container: '#ordens-list-container',
            tabela: '#ordens-list',
            paginacao: '#paginacao-oslist-section',
            limitSelector: '#os1pg-registros-por-pagina',
            formSelector: '#form-oslist',
            btnBuscarSelector: '#buscar-ordens',
            usaTableSorter: true,
            reinicializarTableSorter: reinicializarTableSorter
        });

    });


    /**PAGINAÇÃO LISTAGEM DE OS - FIM */


    $(document).ready(function () {

        restaurarFiltrosLocalStorage();

        // Salvar sempre que algum filtro mudar
        $(document).on('change', '#form-oslist input, #form-oslist select', function () {
            salvarFiltrosLocalStorage();
        });

        $(document).on('click', '#toggle-sort-oslist', function () {
            // Aguardar a função sortToggle processar primeiro
            setTimeout(function () {
                salvarFiltrosLocalStorage();
            }, 100);
        });


        $("#os-buscar-por").on("change", function () {
            const valor = $(this).val();

            // Esconde todos os filtros e desabilita seus inputs
            $("#filtro-todos, #filtro-cliente, #filtro-tarefa, #filtro-operador, #filtro-segmento").hide().find("input, select").prop("disabled", true);

            // Mostra o filtro correspondente e habilita seus inputs
            switch (valor) {
                case "todos":
                    $("#filtro-todos").show().find("input, select").prop("disabled", false);
                    break;
                case "cliente":
                    $("#filtro-cliente").show().find("input, select").prop("disabled", false);
                    break;
                case "tarefa":
                    $("#filtro-tarefa").show().find("input, select").prop("disabled", false);
                    break;
                case "operador":
                    $("#filtro-operador").show().find("input, select").prop("disabled", false);
                    break;
                case "segmento":
                    $("#filtro-segmento").show().find("input, select").prop("disabled", false);
                    break;
            }

            salvarFiltrosLocalStorage();
        });
        // Exibe o campo correto já no carregamento inicial
        $("#os-buscar-por").trigger("change");
    });
});



// TINYMCE INIT

tinyMCE.init({
    selector: "textarea.mce",
    language: 'pt_BR',
    menubar: false,
    theme: "modern",
    height: 132,
    skin: 'light',
    entity_encoding: "raw",
    theme_advanced_resizing: true,
    plugins: [
        "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save table contextmenu directionality emoticons template paste textcolor media"
    ],
    toolbar: "styleselect | pastetext | removeformat |  bold | italic | underline | strikethrough | bullist | numlist | alignleft | aligncenter | alignright |  link | unlink | fsphpimage | code | fullscreen",
    style_formats: [
        { title: 'Normal', block: 'p' },
        { title: 'Titulo 3', block: 'h3' },
        { title: 'Titulo 4', block: 'h4' },
        { title: 'Titulo 5', block: 'h5' },
        { title: 'Código', block: 'pre', classes: 'brush: php;' }
    ],
    link_class_list: [
        { title: 'None', value: '' },
        { title: 'Blue CTA', value: 'btn btn_cta_blue' },
        { title: 'Green CTA', value: 'btn btn_cta_green' },
        { title: 'Yellow CTA', value: 'btn btn_cta_yellow' },
        { title: 'Red CTA', value: 'btn btn_cta_red' }
    ],
    setup: function (editor) {
        editor.addButton('fsphpimage', {
            title: 'Enviar Imagem',
            icon: 'image',
            onclick: function () {
                $('.mce_upload').fadeIn(200, function (e) {
                    $("body").click(function (e) {
                        if ($(e.target).attr("class") === "mce_upload") {
                            $('.mce_upload').fadeOut(200);
                        }
                    });
                }).css("display", "flex");
            }
        });
    },
    link_title: false,
    target_list: false,
    theme_advanced_blockformats: "h1,h2,h3,h4,h5,p,pre",
    media_dimensions: false,
    media_poster: false,
    media_alt_source: false,
    media_embed: false,
    extended_valid_elements: "a[href|target=_blank|rel|class]",
    imagemanager_insert_template: '<img src="{$url}" title="{$title}" alt="{$title}" />',
    image_dimensions: false,
    relative_urls: false,
    remove_script_host: false,
    paste_as_text: true
});

$(document).ready(function () {
    const $servicosComEquipamentos = $('#emp_servicosComEquipamentos');
    const $equipamentoObrigatorio = $('#emp_equipamentoObrigatorio');

    function toggleEquipamentoObrigatorio() {
        $equipamentoObrigatorio.prop('disabled', !$servicosComEquipamentos.is(':checked'));
        if (!$servicosComEquipamentos.is(':checked')) {
            $equipamentoObrigatorio.prop('checked', false);
        }
    }

    $servicosComEquipamentos.on('change', toggleEquipamentoObrigatorio);

    // Initialize state on page load
    toggleEquipamentoObrigatorio();
});

/** GRAFICOS DASHBOARD */

$(document).ready(function () {



});



/***CADASTRO DE TURNOS ***/

$(document).ready(function () {
    const $customSelect = $('#dias_semana');
    const $selectPlaceholder = $customSelect.find('.select-placeholder');
    const $selectOptions = $customSelect.find('.select-options');
    const $hiddenInput = $('#dias_semana_hidden');

    // Função para atualizar o campo hidden e o placeholder
    function atualizarSelecionados() {
        const selectedOptions = $selectOptions.find('input[type="checkbox"]:checked')
            .map(function () {
                const $label = $(this).parent();
                $label.addClass('option-selected'); // Adiciona a classe ao texto da opção selecionada
                return $label.text().trim();
            })
            .get();

        // Remove o estilo das opções não selecionadas
        $selectOptions.find('input[type="checkbox"]:not(:checked)').parent().removeClass('option-selected');

        $selectPlaceholder.text(selectedOptions.length ? selectedOptions.join(', ') : 'Selecione os dias...');
        $hiddenInput.val(selectedOptions.join(','));

        // Verifica se as opções 5 ou 8 estão selecionadas
        const isMondayVisible = selectedOptions.includes('Segunda-feira') || selectedOptions.includes('Segunda à Sábado') || selectedOptions.includes('Segunda à Sexta');
        $('#segunda-row input').prop('disabled', !isMondayVisible);
        const isTuesdayVisible = selectedOptions.includes('Terça-feira') || selectedOptions.includes('Segunda à Sábado') || selectedOptions.includes('Segunda à Sexta');
        $('#terca-row input').prop('disabled', !isTuesdayVisible);
        const isWednesdayVisible = selectedOptions.includes('Quarta-feira') || selectedOptions.includes('Segunda à Sábado') || selectedOptions.includes('Segunda à Sexta');
        $('#quarta-row input').prop('disabled', !isWednesdayVisible);
        const isThursdayVisible = selectedOptions.includes('Quinta-feira') || selectedOptions.includes('Segunda à Sábado') || selectedOptions.includes('Segunda à Sexta');
        $('#quinta-row input').prop('disabled', !isThursdayVisible);
        const isFridayVisible = selectedOptions.includes('Sexta-feira') || selectedOptions.includes('Segunda à Sábado') || selectedOptions.includes('Segunda à Sexta');
        $('#sexta-row input').prop('disabled', !isFridayVisible);
        const isSaturdayVisible = selectedOptions.includes('Sábado') || selectedOptions.includes('Segunda à Sábado');
        $('#sabado-row input').prop('disabled', !isSaturdayVisible);
        const isSundayVisible = selectedOptions.includes('Domingo');
        $('#domingo-row input').prop('disabled', !isSundayVisible);
    }

    // Marcar os checkboxes selecionados com base nos valores já carregados
    const diasSelecionados = $customSelect.data('selected-days');
    if (diasSelecionados) {
        diasSelecionados.toString().split(',').forEach(function (valor) {
            valor = valor.trim();
            const checkbox = $selectOptions.find(`input[type="checkbox"][value="${valor}"]`);
            checkbox.prop('checked', true);

            // Aplica o estilo às opções selecionadas durante o carregamento
            checkbox.parent().addClass('option-selected');
        });
    }

    // Atualizar o campo hidden e o placeholder
    atualizarSelecionados();

    // Função para atualizar a visibilidade dos inputs do sábado
    function atualizarInputsSegunda() {
        const isChecked = $('#segunda').is(':checked');
        $('#monday-inputs').toggle(isChecked);
    }
    function atualizarInputsTerca() {
        const isChecked = $('#terca').is(':checked');
        $('#tuesday-inputs').toggle(isChecked);
    }
    function atualizarInputsQuarta() {
        const isChecked = $('#quarta').is(':checked');
        $('#wednesday-inputs').toggle(isChecked);
    }
    function atualizarInputsQuinta() {
        const isChecked = $('#quinta').is(':checked');
        $('#thursday-inputs').toggle(isChecked);
    }
    function atualizarInputsSexta() {
        const isChecked = $('#sexta').is(':checked');
        $('#friday-inputs').toggle(isChecked);
    }
    function atualizarInputsSabado() {
        const isChecked = $('#sabado').is(':checked');
        $('#saturday-inputs').toggle(isChecked);
    }
    function atualizarInputsDomingo() {
        const isChecked = $('#domingo').is(':checked');
        $('#sunday-inputs').toggle(isChecked);
    }

    // Adicione um listener para o checkbox do sábado
    $('#segunda').on('change', atualizarInputsSegunda);
    $('#terca').on('change', atualizarInputsTerca);
    $('#quarta').on('change', atualizarInputsQuarta);
    $('#quinta').on('change', atualizarInputsQuinta);
    $('#sexta').on('change', atualizarInputsSexta);
    $('#sabado').on('change', atualizarInputsSabado);
    $('#domingo').on('change', atualizarInputsDomingo);

    // Verifique o estado do checkbox na carga da página
    atualizarInputsSegunda();
    atualizarInputsTerca();
    atualizarInputsQuarta();
    atualizarInputsQuinta();
    atualizarInputsSexta();
    atualizarInputsSabado();
    atualizarInputsDomingo();

    // Abrir e fechar a lista de opções
    $customSelect.on('click', function (event) {
        $selectOptions.toggle();
        event.stopPropagation();
    });

    $(document).on('click', function () {
        $selectOptions.hide();
    });

    // Atualizar o campo hidden e o placeholder quando os checkboxes são alterados
    $selectOptions.on('change', 'input[type="checkbox"]', function () {
        atualizarSelecionados();
    });
});

$(document).ready(function () {

    const modalPlc = $("#modalNovoplconta");
    const formPlc = $("#form-novoplconta");
    const tipoPlcSelect = modalPlc.find("#tipo");

    $("#novoplcpag, #novoplcrec").on("click", function () {
        const tipo = $(this).attr("id") === "novoplcpag" ? "D" : "R";

        modalPlc.modal("show");
        formPlc.trigger("reset");
        tipoPlcSelect.val(tipo).addClass('select-readonly').trigger("change");
    });
});

$(document).ready(function () {
    const modalOpr = $("#modalNovoOpr");
    const formOpr = $("#form-novoopr");

    $(".btn-novo-opr").on("click", function () {
        let tipo = $(this).data("tipo");
        modalOpr.modal("show");
        formOpr.trigger("reset");

        modalOpr.find("#tipo").val(tipo);
    });
});

$(document).ready(function () {
    const modalTitle = $("#title-editNovoCli");
    const tipoSelect = $("#ent_tipo");
    const btnVoltar = $("#btnEntVoltar");
    const formNovocli = $("#form-novocli");
    const modalCli = $("#modalcli");

    $('#novocli, #novoclirec, #novoclipag').click(function () {
        $('#modalNovocli').modal('show');
        $('#form-novocli').trigger('reset');
    });

    function resetForm() {
        // Limpa todos os campos do formulário
        formNovocli[0].reset();
    }

    $("#novocli").on("click", function () {
        setTimeout(function () {
            tipoSelect.val("1").trigger("change");
        }, 100);
    });

    // Função para configurar modal como "Cliente"
    $("#novoclirec").on("click", function () {
        // Limpa o formulário
        resetForm();
        // Atualiza o título da modal
        modalTitle.text("Cadastrar Cliente");
        // Define o valor de "tipo" no select
        setTimeout(function () {
            tipoSelect.val("1").trigger("change");
            btnVoltar.attr("href", "<?= url('ent/cliente') ?>");
            modalCli.val("rec");
        }, 100);
    });

    // Função para configurar modal como "Fornecedor"
    $("#novoclipag").on("click", function () {
        // Limpa o formulário
        resetForm();
        // Atualiza o título da modal
        modalTitle.text("Cadastrar Fornecedor");
        // Define o valor de "tipo" no select
        setTimeout(function () {
            tipoSelect.val("2").trigger("change");
            //console.log("Depois do reset, tipoSelect:", tipoSelect.val());
        }, 100);
        // Atualiza o link de voltar para o valor da URI
        btnVoltar.attr("href", "<?= url('ent/fornecedor') ?>");
        modalCli.val("pag");
    });
});

$(document).ready(function () {
    $(document).on("input", "input[type='text']:not(#emp_label), textarea", function () {
        const el = this;
        const val = el.value;
        const upper = val.toUpperCase();

        if (val !== upper) {
            const start = el.selectionStart;
            const end = el.selectionEnd;

            el.value = upper;
            el.setSelectionRange(start, end);
        }
    });

    $("input[type='email']").on("input", function () {
        $(this).val($(this).val().toLowerCase());
    });

    $("form").find("input:visible:not([type='checkbox']), select:visible:not(#emp2-select), textarea:visible").first().focus();

    $('.os1-all-disabled').find('input, select, textarea').attr('tabindex', '-1');
    $('.os2-item-disabled').find('input, select, textarea').attr('tabindex', '-1');
});

/** TELA MOBILE ARQUIVOS **/
$(document).ready(function () {

    if (window.location.pathname.includes("/files/lista") ||
        window.location.pathname.includes("/files/emp") ||
        window.location.pathname.includes("/files/func")) {
        $('.main').addClass('specific-page');
        $('.titulo-secao').addClass('titulo-mobile');
        $("html").addClass("overflow-mobile");
    }

    // Gerenciamento de abas e carregamento imediato do conteúdo
    $('#tabs').on('click', '.tab', function () {
        const $this = $(this);
        $('.tab')
            .removeClass('active')
            .attr('aria-selected', 'false')
            .css('transform', 'scale(0.95)');

        $this
            .addClass('active')
            .attr('aria-selected', 'true')
            .css('transform', 'scale(1)');

        // Scroll horizontal suave usando easing padrão 'swing'
        const container = $('.tabs-wrapper')[0];
        const tabPosition = $this.position().left;
        const containerWidth = container.offsetWidth;
        const scrollAmount = tabPosition - (containerWidth / 2) + ($this.outerWidth() / 2);
        $('.tabs').stop().animate({
            scrollLeft: scrollAmount
        }, 600, 'swing');

        // Carregar conteúdo ao clicar na aba
        const type = $this.data('type');
        $('#contentArea').animate({ scrollTop: 0 }, 300);
        loadContent(type);
    });

    // Expansão de documentos com toggle corrigido
    $('#contentArea').on('click', '.user-item', function () {
        const $panel = $(this).next('.document-panel');
        if ($panel.is(':visible')) {
            $panel.slideUp(300);
            $(this).find('.chevron-icone').removeClass('active');
        } else {
            $('.document-panel').slideUp(300);
            $('.chevron-icone').removeClass('active');
            $panel.slideDown(300);
            $(this).find('.chevron-icone').addClass('active');
        }
    });

    function loadContent(type) {
        $(".content-card").each(function () {
            const card = $(this);
            const category = card.data("cat");

            console.log(category, type);

            if (type == "todos" || category == type) {
                card.show();
            } else {
                card.hide();
            }
        });
    }

    $('#filtrar-arq-mobile').on('input', function () {
        const searchText = $(this).val().toLowerCase();
        const selectedTab = $('.tab.active').data('type');

        $(".content-card").each(function () {
            const card = $(this);
            const category = card.data("cat");
            const textContent = card.find('.user-info').text().toLowerCase();

            // Verifica se o texto digitado aparece no conteúdo do cartão
            const matchesText = textContent.includes(searchText);

            // Verifica se o item pertence à aba selecionada
            const matchesCategory = (selectedTab == "todos" || category == selectedTab);

            // Exibe ou oculta com base nas condições
            if (matchesText && matchesCategory) {
                card.show();
            } else {
                card.hide();
            }
        });
    });

    $(".btn-add-arq-mob").click(function (event) {
        event.stopPropagation();
        var menu = $(this).siblings(".tooltip-menu");
        $(".tooltip-menu").not(menu).hide(); // Esconde outros menus abertos
        menu.toggle();
    });

    $(document).click(function () {
        $(".tooltip-menu").hide();
    });

    $(".tooltip-menu").click(function (event) {
        event.stopPropagation(); // Evita que o clique no menu o feche
    });

    $('#custom-file-upload-mobile').on('click', function () {
        $('#arquivo-mobile').click();
    });

    $('#arquivo-mobile').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $('#file-name').text("Arquivo Selecionado: " + fileName);
    });

    $('#custom-file-upload-mobile-func').on('click', function () {
        $('#arquivo-mobile-func').click();
    });

    $('#arquivo-mobile-func').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $('#file-name-func').text("Arquivo Selecionado: " + fileName);
    });

});

$(document).ready(function () {
    const icons = ["fa-address-book", "fa-address-card", "fa-adjust", "fa-air-freshener", "fa-align-center", "fa-align-justify", "fa-align-left", "fa-align-right", "fa-allergies", "fa-ambulance", "fa-american-sign-language-interpreting", "fa-anchor", "fa-angle-double-down", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-angry", "fa-ankh", "fa-apple-alt", "fa-archive", "fa-archway", "fa-arrow-alt-circle-down", "fa-arrow-alt-circle-left", "fa-arrow-alt-circle-right", "fa-arrow-alt-circle-up", "fa-arrow-circle-down", "fa-arrow-circle-left", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-down", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrows-alt", "fa-arrows-alt-h", "fa-arrows-alt-v", "fa-assistive-listening-systems", "fa-asterisk", "fa-at", "fa-atlas", "fa-atom", "fa-audio-description", "fa-award", "fa-baby", "fa-baby-carriage", "fa-backspace", "fa-backward", "fa-bacon", "fa-bacteria", "fa-bacterium", "fa-bahai", "fa-balance-scale", "fa-balance-scale-left", "fa-balance-scale-right", "fa-ban", "fa-band-aid", "fa-barcode", "fa-bars", "fa-baseball-ball", "fa-basketball-ball", "fa-bath", "fa-battery-empty", "fa-battery-full", "fa-battery-half", "fa-battery-quarter", "fa-battery-three-quarters", "fa-bed", "fa-beer", "fa-bell", "fa-bell-slash", "fa-bezier-curve", "fa-bible", "fa-bicycle", "fa-biking", "fa-binoculars", "fa-biohazard", "fa-birthday-cake", "fa-blender", "fa-blender-phone", "fa-blind", "fa-blog", "fa-bold", "fa-bolt", "fa-bomb", "fa-bone", "fa-bong", "fa-book", "fa-book-dead", "fa-book-medical", "fa-book-open", "fa-book-reader", "fa-bookmark", "fa-border-all", "fa-border-none", "fa-border-style", "fa-bowling-ball", "fa-box", "fa-box-open", "fa-box-tissue", "fa-boxes", "fa-braille", "fa-brain", "fa-bread-slice", "fa-briefcase", "fa-briefcase-medical", "fa-broadcast-tower", "fa-broom", "fa-brush", "fa-bug", "fa-building", "fa-bullhorn", "fa-bullseye", "fa-burn", "fa-bus", "fa-bus-alt", "fa-business-time", "fa-calculator", "fa-calendar", "fa-calendar-alt", "fa-calendar-check", "fa-calendar-day", "fa-calendar-minus", "fa-calendar-plus", "fa-calendar-times", "fa-calendar-week", "fa-camera", "fa-camera-retro", "fa-campground", "fa-candy-cane", "fa-cannabis", "fa-capsules", "fa-car", "fa-car-alt", "fa-car-battery", "fa-car-crash", "fa-caret-down", "fa-caret-left", "fa-caret-right", "fa-caret-square-down", "fa-caret-square-left", "fa-caret-square-right", "fa-caret-square-up", "fa-caret-up", "fa-carrot", "fa-car-side", "fa-cart-arrow-down", "fa-cart-plus", "fa-cash-register", "fa-cat", "fa-certificate", "fa-chair", "fa-chalkboard", "fa-chalkboard-teacher", "fa-charging-station", "fa-chart-area", "fa-chart-bar", "fa-chart-line", "fa-chart-pie", "fa-check", "fa-check-circle", "fa-check-double", "fa-check-square", "fa-cheese", "fa-chess", "fa-chess-bishop", "fa-chess-board", "fa-chess-king", "fa-chess-knight", "fa-chess-pawn", "fa-chess-queen", "fa-chess-rook", "fa-chevron-circle-down", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-down", "fa-chevron-left", "fa-chevron-right", "fa-chevron-up", "fa-child", "fa-church", "fa-circle", "fa-circle-notch", "fa-city", "fa-clinic-medical", "fa-clipboard", "fa-clipboard-check", "fa-clipboard-list", "fa-clock", "fa-clone", "fa-closed-captioning", "fa-cloud", "fa-cloud-download-alt", "fa-cloud-meatball", "fa-cloud-moon", "fa-cloud-moon-rain", "fa-cloud-rain", "fa-cloud-showers-heavy", "fa-cloud-sun", "fa-cloud-sun-rain", "fa-cloud-upload-alt", "fa-cocktail", "fa-code", "fa-code-branch", "fa-coffee", "fa-cog", "fa-cogs", "fa-coins", "fa-columns", "fa-comment", "fa-comment-alt", "fa-comment-dollar", "fa-comment-dots", "fa-comment-medical", "fa-comment-slash", "fa-comments", "fa-comments-dollar", "fa-compact-disc", "fa-compass", "fa-compress", "fa-compress-alt", "fa-compress-arrows-alt", "fa-concierge-bell", "fa-cookie", "fa-cookie-bite", "fa-copy", "fa-copyright", "fa-couch", "fa-credit-card", "fa-crop", "fa-crop-alt", "fa-cross", "fa-crosshairs", "fa-crow", "fa-crown", "fa-crutch", "fa-cube", "fa-cubes", "fa-cut", "fa-database", "fa-deaf", "fa-democrat", "fa-desktop", "fa-dharmachakra", "fa-diagnoses", "fa-dice", "fa-dice-d20", "fa-dice-d6", "fa-dice-five", "fa-dice-four", "fa-dice-one", "fa-dice-six", "fa-dice-three", "fa-dice-two", "fa-digital-tachograph", "fa-directions", "fa-divide", "fa-dizzy", "fa-dna", "fa-dog", "fa-dollar-sign", "fa-dolly", "fa-dolly-flatbed", "fa-donate", "fa-door-closed", "fa-door-open", "fa-dot-circle", "fa-dove", "fa-download", "fa-drafting-compass", "fa-dragon", "fa-draw-polygon", "fa-drum", "fa-drum-steelpan", "fa-drumstick-bite", "fa-dumbbell", "fa-dumpster", "fa-dumpster-fire", "fa-dungeon", "fa-edit", "fa-egg", "fa-eject", "fa-ellipsis-h", "fa-ellipsis-v", "fa-envelope", "fa-envelope-open", "fa-envelope-open-text", "fa-envelope-square", "fa-equals", "fa-eraser", "fa-ethernet", "fa-euro-sign", "fa-exchange-alt", "fa-exclamation", "fa-exclamation-circle", "fa-exclamation-triangle", "fa-expand", "fa-expand-alt", "fa-expand-arrows-alt", "fa-external-link-alt", "fa-external-link-square-alt", "fa-eye", "fa-eye-dropper", "fa-eye-slash", "fa-fan", "fa-fast-backward", "fa-fast-forward", "fa-fax", "fa-feather", "fa-feather-alt", "fa-female", "fa-fighter-jet", "fa-file", "fa-file-alt", "fa-file-archive", "fa-file-audio", "fa-file-code", "fa-file-contract", "fa-file-csv", "fa-file-download", "fa-file-excel", "fa-file-export", "fa-file-image", "fa-file-import", "fa-file-invoice", "fa-file-invoice-dollar", "fa-file-medical", "fa-file-medical-alt", "fa-file-pdf", "fa-file-powerpoint", "fa-file-prescription", "fa-file-signature", "fa-file-upload", "fa-file-video", "fa-file-word", "fa-fill", "fa-fill-drip", "fa-film", "fa-filter", "fa-fingerprint", "fa-fire", "fa-fire-alt", "fa-fire-extinguisher", "fa-first-aid", "fa-fish", "fa-fist-raised", "fa-flag", "fa-flag-checkered", "fa-flag-usa", "fa-flask", "fa-flushed", "fa-folder", "fa-folder-minus", "fa-folder-open", "fa-folder-plus", "fa-font", "fa-football-ball", "fa-forward", "fa-frog", "fa-frown", "fa-frown-open", "fa-funnel-dollar", "fa-futbol", "fa-gamepad", "fa-gas-pump", "fa-gavel", "fa-gem", "fa-genderless", "fa-ghost", "fa-gift", "fa-gifts", "fa-glass-cheers", "fa-glass-martini", "fa-glass-martini-alt", "fa-glass-whiskey", "fa-glasses", "fa-globe", "fa-globe-africa", "fa-globe-americas", "fa-globe-asia", "fa-globe-europe", "fa-golf-ball", "fa-gopuram", "fa-graduation-cap", "fa-greater-than", "fa-greater-than-equal", "fa-grimace", "fa-grin", "fa-grin-alt", "fa-grin-beam", "fa-grin-beam-sweat", "fa-grin-hearts", "fa-grin-squint", "fa-grin-squint-tears", "fa-grin-stars", "fa-grin-tears", "fa-grin-tongue", "fa-grin-tongue-squint", "fa-grin-tongue-wink", "fa-grin-wink", "fa-grip-horizontal", "fa-grip-lines", "fa-grip-lines-vertical", "fa-grip-vertical", "fa-guitar", "fa-h-square", "fa-hamburger", "fa-hammer", "fa-hamsa", "fa-hand-holding", "fa-hand-holding-heart", "fa-hand-holding-medical", "fa-hand-holding-usd", "fa-hand-holding-water", "fa-hand-lizard", "fa-hand-middle-finger", "fa-hand-paper", "fa-hand-peace", "fa-hand-point-down", "fa-hand-point-left", "fa-hand-point-right", "fa-hand-point-up", "fa-hand-pointer", "fa-hand-rock", "fa-hand-scissors", "fa-hand-sparkles", "fa-hand-spock", "fa-hands", "fa-hands-helping", "fa-hands-wash", "fa-handshake", "fa-handshake-alt-slash", "fa-handshake-slash", "fa-hanukiah", "fa-hard-hat", "fa-hashtag", "fa-hat-cowboy", "fa-hat-cowboy-side", "fa-hat-wizard", "fa-hdd", "fa-head-side-cough", "fa-head-side-cough-slash", "fa-head-side-mask", "fa-head-side-virus", "fa-heading", "fa-headphones", "fa-headphones-alt", "fa-headset", "fa-heart", "fa-heart-broken", "fa-heartbeat", "fa-helicopter", "fa-highlighter", "fa-hiking", "fa-hippo", "fa-history", "fa-hockey-puck", "fa-holly-berry", "fa-home", "fa-horse", "fa-horse-head", "fa-hospital", "fa-hospital-alt", "fa-hospital-symbol", "fa-hospital-user", "fa-hot-tub", "fa-hotdog", "fa-hotel", "fa-hourglass", "fa-hourglass-end", "fa-hourglass-half", "fa-hourglass-start", "fa-house-damage", "fa-house-user", "fa-hryvnia", "fa-i-cursor", "fa-ice-cream", "fa-icicles", "fa-icons", "fa-id-badge", "fa-id-card", "fa-id-card-alt", "fa-igloo", "fa-image", "fa-images", "fa-inbox", "fa-indent", "fa-industry", "fa-infinity", "fa-info", "fa-info-circle", "fa-italic", "fa-jedi", "fa-joint", "fa-journal-whills", "fa-kaaba", "fa-key", "fa-keyboard", "fa-khanda", "fa-kiss", "fa-kiss-beam", "fa-kiss-wink-heart", "fa-kiwi-bird", "fa-landmark", "fa-language", "fa-laptop", "fa-laptop-code", "fa-laptop-house", "fa-laptop-medical", "fa-laugh", "fa-laugh-beam", "fa-laugh-squint", "fa-laugh-wink", "fa-layer-group", "fa-leaf", "fa-lemon", "fa-less-than", "fa-less-than-equal", "fa-level-down-alt", "fa-level-up-alt", "fa-life-ring", "fa-lightbulb", "fa-link", "fa-lira-sign", "fa-list", "fa-list-alt", "fa-list-ol", "fa-list-ul", "fa-location-arrow", "fa-lock", "fa-lock-open", "fa-long-arrow-alt-down", "fa-long-arrow-alt-left", "fa-long-arrow-alt-right", "fa-long-arrow-alt-up", "fa-low-vision", "fa-luggage-cart", "fa-magic", "fa-magnet", "fa-mail-bulk", "fa-male", "fa-map", "fa-map-marked", "fa-map-marked-alt", "fa-map-marker", "fa-map-marker-alt", "fa-map-pin", "fa-map-signs", "fa-marker", "fa-mars", "fa-mars-double", "fa-mars-stroke", "fa-mars-stroke-h", "fa-mars-stroke-v", "fa-mask", "fa-medal", "fa-medkit", "fa-meh", "fa-meh-blank", "fa-meh-rolling-eyes", "fa-memory", "fa-menorah", "fa-mercury", "fa-meteor", "fa-microchip", "fa-microphone", "fa-microphone-alt", "fa-microphone-alt-slash", "fa-microphone-slash", "fa-microscope", "fa-minus", "fa-minus-circle", "fa-minus-square", "fa-mitten", "fa-mobile", "fa-mobile-alt", "fa-money-bill", "fa-money-bill-alt", "fa-money-bill-wave", "fa-money-bill-wave-alt", "fa-money-check", "fa-money-check-alt", "fa-monument", "fa-moon", "fa-mortar-pestle", "fa-mosque", "fa-motorcycle", "fa-mountain", "fa-mouse", "fa-mouse-pointer", "fa-mug-hot", "fa-music", "fa-network-wired", "fa-neuter", "fa-newspaper", "fa-not-equal", "fa-notes-medical", "fa-object-group", "fa-object-ungroup", "fa-oil-can", "fa-om", "fa-otter", "fa-outdent", "fa-pager", "fa-paint-brush", "fa-paint-roller", "fa-palette", "fa-pallet", "fa-paper-plane", "fa-paperclip", "fa-parachute-box", "fa-paragraph", "fa-parking", "fa-passport", "fa-pastafarianism", "fa-paste", "fa-pause", "fa-pause-circle", "fa-paw", "fa-peace", "fa-pen", "fa-pen-alt", "fa-pen-fancy", "fa-pen-nib", "fa-pen-square", "fa-pencil-alt", "fa-pencil-ruler", "fa-people-arrows", "fa-people-carry", "fa-pepper-hot", "fa-percent", "fa-percentage", "fa-person-booth", "fa-phone", "fa-phone-alt", "fa-phone-slash", "fa-phone-square", "fa-phone-square-alt", "fa-phone-volume", "fa-photo-video", "fa-piggy-bank", "fa-pills", "fa-pizza-slice", "fa-place-of-worship", "fa-plane", "fa-plane-arrival", "fa-plane-departure", "fa-plane-slash", "fa-play", "fa-play-circle", "fa-plug", "fa-plus", "fa-plus-circle", "fa-plus-square", "fa-podcast", "fa-poll", "fa-poll-h", "fa-poo", "fa-poo-storm", "fa-poop", "fa-portrait", "fa-pound-sign", "fa-power-off", "fa-pray", "fa-praying-hands", "fa-prescription", "fa-prescription-bottle", "fa-prescription-bottle-alt", "fa-print", "fa-procedures", "fa-project-diagram", "fa-pump-medical", "fa-pump-soap", "fa-puzzle-piece", "fa-qrcode", "fa-question", "fa-question-circle", "fa-quidditch", "fa-quote-left", "fa-quote-right", "fa-quran", "fa-radiation", "fa-radiation-alt", "fa-rainbow", "fa-random", "fa-receipt", "fa-record-vinyl", "fa-recycle", "fa-redo", "fa-redo-alt", "fa-registered", "fa-remove-format", "fa-reply", "fa-reply-all", "fa-republican", "fa-restroom", "fa-retweet", "fa-ribbon", "fa-ring", "fa-road", "fa-robot", "fa-rocket", "fa-route", "fa-rss", "fa-rss-square", "fa-ruble-sign", "fa-ruler", "fa-ruler-combined", "fa-ruler-horizontal", "fa-ruler-vertical", "fa-running", "fa-rupee-sign", "fa-sad-cry", "fa-sad-tear", "fa-satellite", "fa-satellite-dish", "fa-save", "fa-school", "fa-screwdriver", "fa-scroll", "fa-sd-card", "fa-search", "fa-search-dollar", "fa-search-location", "fa-search-minus", "fa-search-plus", "fa-seedling", "fa-server", "fa-shapes", "fa-share", "fa-share-alt", "fa-share-alt-square", "fa-share-square", "fa-shekel-sign", "fa-shield-alt", "fa-shield-virus", "fa-ship", "fa-shipping-fast", "fa-shoe-prints", "fa-shopping-bag", "fa-shopping-basket", "fa-shopping-cart", "fa-shower", "fa-shuttle-van", "fa-sign", "fa-sign-in-alt", "fa-sign-language", "fa-sign-out-alt", "fa-signal", "fa-signature", "fa-sim-card", "fa-sink", "fa-sitemap", "fa-skating", "fa-skiing", "fa-skiing-nordic", "fa-skull", "fa-skull-crossbones", "fa-slash", "fa-sleigh", "fa-sliders-h", "fa-smile", "fa-smile-beam", "fa-smile-wink", "fa-smog", "fa-smoking", "fa-smoking-ban", "fa-sms", "fa-snowboarding", "fa-snowflake", "fa-snowman", "fa-snowplow", "fa-soap", "fa-socks", "fa-solar-panel", "fa-sort", "fa-sort-alpha-down", "fa-sort-alpha-down-alt", "fa-sort-alpha-up", "fa-sort-alpha-up-alt", "fa-sort-amount-down", "fa-sort-amount-down-alt", "fa-sort-amount-up", "fa-sort-amount-up-alt", "fa-sort-down", "fa-sort-numeric-down", "fa-sort-numeric-down-alt", "fa-sort-numeric-up", "fa-sort-numeric-up-alt", "fa-sort-up", "fa-spa", "fa-space-shuttle", "fa-spell-check", "fa-spider", "fa-spinner", "fa-splotch", "fa-spray-can", "fa-square", "fa-square-full", "fa-square-root-alt", "fa-stamp", "fa-star", "fa-star-and-crescent", "fa-star-half", "fa-star-half-alt", "fa-star-of-david", "fa-star-of-life", "fa-step-backward", "fa-step-forward", "fa-stethoscope", "fa-sticky-note", "fa-stop", "fa-stop-circle", "fa-stopwatch", "fa-stopwatch-20", "fa-store", "fa-store-alt", "fa-store-alt-slash", "fa-store-slash", "fa-stream", "fa-street-view", "fa-strikethrough", "fa-stroopwafel", "fa-subscript", "fa-subway", "fa-suitcase", "fa-suitcase-rolling", "fa-sun", "fa-superscript", "fa-surprise", "fa-swatchbook", "fa-swimmer", "fa-swimming-pool", "fa-synagogue", "fa-sync", "fa-sync-alt", "fa-syringe", "fa-table", "fa-table-tennis", "fa-tablet", "fa-tablet-alt", "fa-tablets", "fa-tachometer-alt", "fa-tag", "fa-tags", "fa-tape", "fa-tasks", "fa-taxi", "fa-teeth", "fa-teeth-open", "fa-temperature-high", "fa-temperature-low", "fa-tenge", "fa-terminal", "fa-text-height", "fa-text-width", "fa-th", "fa-th-large", "fa-th-list", "fa-theater-masks", "fa-thermometer", "fa-thermometer-empty", "fa-thermometer-full", "fa-thermometer-half", "fa-thermometer-quarter", "fa-thermometer-three-quarters", "fa-thumbs-down", "fa-thumbs-up", "fa-thumbtack", "fa-ticket-alt", "fa-times", "fa-times-circle", "fa-tint", "fa-tint-slash", "fa-tired", "fa-toggle-off", "fa-toggle-on", "fa-toilet", "fa-toilet-paper", "fa-toilet-paper-slash", "fa-toolbox", "fa-tools", "fa-tooth", "fa-torah", "fa-torii-gate", "fa-tractor", "fa-trademark", "fa-traffic-light", "fa-trailer", "fa-train", "fa-tram", "fa-transgender", "fa-transgender-alt", "fa-trash", "fa-trash-alt", "fa-trash-restore", "fa-trash-restore-alt", "fa-tree", "fa-trophy", "fa-truck", "fa-truck-loading", "fa-truck-monster", "fa-truck-moving", "fa-truck-pickup", "fa-tshirt", "fa-tty", "fa-tv", "fa-umbrella", "fa-umbrella-beach", "fa-underline", "fa-undo", "fa-undo-alt", "fa-universal-access", "fa-university", "fa-unlink", "fa-unlock", "fa-unlock-alt", "fa-upload", "fa-user", "fa-user-alt", "fa-user-alt-slash", "fa-user-astronaut", "fa-user-check", "fa-user-circle", "fa-user-clock", "fa-user-cog", "fa-user-edit", "fa-user-friends", "fa-user-graduate", "fa-user-injured", "fa-user-lock", "fa-user-md", "fa-user-minus", "fa-user-ninja", "fa-user-nurse", "fa-user-plus", "fa-user-secret", "fa-user-shield", "fa-user-slash", "fa-user-tag", "fa-user-tie", "fa-user-times", "fa-users", "fa-users-cog", "fa-users-slash", "fa-utensil-spoon", "fa-utensils", "fa-vector-square", "fa-venus", "fa-venus-double", "fa-venus-mars", "fa-vial", "fa-vials", "fa-video", "fa-video-slash", "fa-vihara", "fa-voicemail", "fa-volleyball-ball", "fa-volume-down", "fa-volume-mute", "fa-volume-off", "fa-volume-up", "fa-vote-yea", "fa-vr-cardboard", "fa-walking", "fa-wallet", "fa-warehouse", "fa-water", "fa-wave-square", "fa-weight", "fa-weight-hanging", "fa-wheelchair", "fa-wifi", "fa-wind", "fa-window-close", "fa-window-maximize", "fa-window-minimize", "fa-window-restore", "fa-wine-bottle", "fa-wine-glass", "fa-wine-glass-alt", "fa-won-sign", "fa-wrench", "fa-x-ray", "fa-yen-sign", "fa-yin-yang"];

    // Gera os ícones na grade
    const iconGrid = $('#icon_grid');
    icons.forEach(icon => {
        iconGrid.append(`
            <div class="icon-item" data-icon="${icon}" style="text-align: center; cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <i class="fa ${icon}" style="font-size: 24px;"></i>
            </div>
        `);
    });

    // Abre o modal
    $('#open_icon_modal').on('click', function () {
        $('#icon_modal').fadeIn();
    });

    // Fecha o modal
    $('#close_icon_modal').on('click', function () {
        $('#icon_modal').fadeOut();
    });

    // Seleciona um ícone
    $('#icon_grid').on('click', '.icon-item', function () {
        const iconClass = $(this).data('icon');

        // Atualiza o botão com o ícone selecionado
        $('#selected_icon').html(`<i class="fa ${iconClass}"></i>`);

        // Atualiza o valor do campo oculto
        $('#icon_select').val(iconClass);

        // Atualiza a pré-visualização
        $('#icon_preview').html(`<i class="fa ${iconClass}"></i>`);

        // Fecha o modal
        $('#icon_modal').fadeOut();
    });

    // Fecha o modal ao clicar fora da caixa
    $(document).on('click', function (e) {
        if ($(e.target).is('#icon_modal')) {
            $('#icon_modal').fadeOut();
        }
    });

    $('#icon_search').on('keyup', function () {
        const searchTerm = $(this).val().toLowerCase();

        $('#icon_grid .icon-item').each(function () {
            const iconName = $(this).data('icon').toLowerCase();
            if (iconName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

$(document).ready(function () {
    $(function () {
        setInterval(function () {
            $('.blinking-text').each(function () {
                const current = $(this).text().trim();
                $(this).text(current === 'FINALIZADA' ? 'AG. CONCLUSÃO' : 'FINALIZADA');
            });
        }, 2000);
    });
});

$(document).ready(function () {
    $(document).on('mouseenter', '#movInfo', function (event) {
        tooltipText = $(this).attr('data-tooltip');
        const tooltipDiv = $('<div class="tooltip-text2"></div>').html(tooltipText);

        $('body').append(tooltipDiv);

        const rect = this.getBoundingClientRect();
        tooltipDiv.css({
            top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
            left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
        });

        $(this).data('tooltipDiv', tooltipDiv);
    });

    $(document).on('mouseleave', '#movInfo', function () {
        const tooltipDiv = $(this).data('tooltipDiv');
        if (tooltipDiv) {
            tooltipDiv.remove();
            $(this).removeData('tooltipDiv');
        }
    });
});

$(document).on('click', '#btnSolicitacoesInfo', function () {
    // Se já existir um tooltip aberto, remove antes
    $('.tooltip-text2').remove();

    // Pega o texto do tooltip do atributo data-tooltip
    let tooltipText = 'Nenhuma solicitação pendente no momento. <br>Atualize a página para verificar novas solicitações.';
    // Cria o tooltip com botão 'X'
    const tooltipDiv = $(`
        <div class="tooltip-text2" style="position:absolute; z-index: 9999;">
            <div class="tooltip-content">
                <span>${tooltipText}</span>
                <button type="button" class="close-tooltip">X</button>
            </div>
        </div>
    `);

    // Adiciona o tooltip ao body
    $('body').append(tooltipDiv);

    // Posiciona o tooltip em cima e centralizado no botão
    const rect = this.getBoundingClientRect();
    tooltipDiv.css({
        top: rect.top + window.scrollY - tooltipDiv.outerHeight() - 10,
        left: rect.left + window.scrollX + rect.width / 2 - tooltipDiv.outerWidth() / 2
    });

    // Handler para fechar o tooltip ao clicar no botão 'X'
    tooltipDiv.find('.close-tooltip').on('click', function () {
        tooltipDiv.remove();
    });

    // Remove automaticamente após 5 segundos
    setTimeout(() => {
        tooltipDiv.remove();
    }, 5000);
});

$(document).ready(function () {
    $(".form-financeiro-rel #status").on("change", function () {
        let status = $(this).val();
        let $form = $(this).closest(".form-financeiro-rel");
        let $periodo = $form.find("#filtro-periodo");
        let $optBaixa = $periodo.find("option[value='baixa']");
        let $optsOutros = $periodo.find("option").not($optBaixa);

        let $orderSelects = $(".order-select");
        let $optDataBaixa = $orderSelects.find("option[value='databaixa']");

        if (status === "baixado") {
            // Filtro-período: mostra só "baixa"
            $optBaixa.prop("hidden", false).prop("selected", true);
            $optsOutros.prop("hidden", true);

            // Order-select: mostra "databaixa"
            $optDataBaixa.prop("hidden", false);

        } else {
            // Filtro-período: esconde "baixa", mostra os outros
            $optBaixa.prop("hidden", true);
            $optsOutros.prop("hidden", false);
            if ($periodo.val() === "baixa") {
                $periodo.val("emissao");
            }

            // Order-select: para cada select que estava em 'databaixa',
            // seta para a primeira opção que não esteja desabilitada nem escondida
            $orderSelects.each(function () {
                let $sel = $(this);
                if ($sel.val() === "databaixa") {
                    let firstEnabled = $sel.find("option:not(:disabled):not([hidden]):first").val();
                    if (typeof firstEnabled === "undefined") {
                        // fallback: se não houver opção habilitada/visível, limpa o valor
                        firstEnabled = "";
                    }
                    $sel.val(firstEnabled).trigger("change");
                }
            });

            // Esconde a opção databaixa
            $optDataBaixa.prop("hidden", true);
        }
    });

    // Inicial: "baixa" e "databaixa" escondidos
    $(".form-financeiro-rel #filtro-periodo option[value='baixa']").prop("hidden", true);
    $(".order-select option[value='databaixa']").prop("hidden", true);
});

$(document).ready(function () {
    $(document).on("click", ".file-pdf-ajax", function () {
        let url = $(this).data("url");
        let id = $(this).data("id");

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (response) {
                const $serv = $("#servicos");
                const $prod = $("#produtos");

                // Servicos
                if (response.servicos) {
                    $serv.prop("checked", true).prop("disabled", false);
                } else {
                    $serv.prop("checked", false).prop("disabled", true);
                }

                // Materiais
                if (response.materiais) {
                    $prod.prop("checked", true).prop("disabled", false);
                } else {
                    $prod.prop("checked", false).prop("disabled", true);
                }

                // Impedir que ambos fiquem desmarcados
                $serv.add($prod).off("change.pdfCheck").on("change.pdfCheck", function (e) {
                    // Se ao desmarcar esse, o outro também está desmarcado → bloqueia
                    if (!$serv.is(":checked") && !$prod.is(":checked")) {
                        e.preventDefault(); // cancela a mudança
                        $(this).prop("checked", true); // mantém marcado
                    }
                });

                // finalmente mostra a modal (após configurar tudo)
                $('#modalCabecalhoPdf').modal('show');
            },
            error: function (xhr, status, error) {
                // Processar erro da requisição
                console.error('Erro na requisição:', error);
                ajaxMessage('Erro ao processar requisição.', 5);
            }
        });

        $("#modalCabecalhoPdf").data("id", id);

    });

    $("#btnGerarPdf").on("click", function () {
        let id = $("#modalCabecalhoPdf").data("id");

        let $empresas = $("input[name='empresa']");
        let empresaId = $empresas.length > 0 ? $empresas.filter(":checked").val() : 0;

        if ($empresas.length > 0 && !empresaId) {
            alert("Selecione uma empresa antes de gerar o PDF.");
            return;
        }

        // Captura checkboxes
        let servicos = $("#servicos").is(":checked") ? 1 : 0;
        let produtos = $("#produtos").is(":checked") ? 1 : 0;

        // Monta URL
        let baseUrl = $("#modalCabecalhoPdf").data("url");
        let url = `${baseUrl}/${id}/${empresaId}?servicos=${servicos}&produtos=${produtos}`;

        // Abre em nova aba
        window.open(url, "_blank");

        // Fecha a modal
        let modalEl = document.getElementById('modalOpcoesPdf');
        let modal = bootstrap.Modal.getInstance(modalEl);
        $('#modalCabecalhoPdf').modal('hide');
    });
    //inicio função busca cep
    //parametros: o input digitado, logradouro, cidade, bairro, uf 
    function buscarCep(input_cep, logradouro, bairro, cidade, uf) {
        $(input_cep).on("input", function () {
            let cep = $(this).val().replace(/\D/g, "");
            //let url = $(this).data("url");
            let url = `https://viacep.com.br/ws/${cep}/json/`;

            if (cep.length === 8) {
                $.ajax({
                    url: url,
                    method: "get",
                    dataType: "json",
                    success: function (data) {
                        if (!data.erro) {
                            $(logradouro).val(data.logradouro);
                            $(bairro).val(data.bairro);
                            $(cidade).val(data.localidade);
                            $(uf).val(data.uf);
                        } else {
                            alert("CEP não encontrado!");
                        }
                    },
                    error: function () {
                        alert("Erro ao buscar o CEP.");
                    }
                });
            }
        });
    }

    buscarCep("#cep", "#logradouro", "#bairro", "#cidade", "#uf");
    buscarCep("#ent_cep", "#ent_endereco", "#ent_bairro", "#ent_cidade", "#ent_uf");
    
});



