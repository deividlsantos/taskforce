$(document).ready(function () {
    // Função para converter horário em minutos
    function parseMinutes(timeStr) {
        var parts = timeStr.split(':');
        if (parts.length === 2) {
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        }
        return 0;
    }

    // Função para formatar minutos em horário "hh:mm"
    function formatTime(minutes) {
        var h = Math.floor(minutes / 60);
        var m = minutes % 60;
        return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
    }

    // Função para calcular a diferença em minutos entre dois horários
    function calcularDiferenca(horaInicio, horaFim) {
        var minutosInicio = parseMinutes(horaInicio);
        var minutosFim = parseMinutes(horaFim);
        return minutosFim - minutosInicio; // Retorna a diferença em minutos
    }

    // Função para validar o formato do horário "hh:mm"
    function validaHoraFormato(hora) {
        var regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
        return regex.test(hora);
    }

    // Função para calcular o total de horas trabalhadas de uma linha
    function calcularHorasTrabalhadas($row) {
        // Verifica se o checkbox está marcado
        if ($row.find('input.edit-free').is(':checked')) {
            return; // Se marcado, não calcula
        }

        var entrada = $row.find('td:nth-child(3)').text().trim();
        var intervalo = $row.find('td:nth-child(4)').text().trim();
        var retorno = $row.find('td:nth-child(5)').text().trim();
        var saida = $row.find('td:nth-child(6)').text().trim();

        // Valida os formatos das horas
        if (!validaHoraFormato(entrada) || !validaHoraFormato(intervalo) || !validaHoraFormato(retorno) || !validaHoraFormato(saida)) {
            $row.find('td:nth-child(7)').text(''); // Horas trabalhadas
            $row.find('td:nth-child(8)').text(''); // Banco de horas
            $row.find('td:nth-child(9)').text(''); // Horas extras
            return;
        }

        var horasTrabalhadas = 0;

        if (entrada && intervalo) {
            horasTrabalhadas += calcularDiferenca(entrada, intervalo);
        }
        if (retorno && saida) {
            horasTrabalhadas += calcularDiferenca(retorno, saida);
        }

        var diaSemana = $row.data('dia-semana');
        var referenciaHoras = 0;

        if (diaSemana == 'Segunda-feira' && $('.seg-txt').text().trim() != "NORMAL") {
            referenciaHoras = parseMinutes($('.total-segunda').text().trim()) || 0;
        } else if (diaSemana == 'Terça-feira' && $('.ter-txt').text().trim() != "NORMAL") {
            referenciaHoras = parseMinutes($('.total-terca').text().trim()) || 0;
        } else if (diaSemana == 'Quarta-feira' && $('.qua-txt').text().trim() != "NORMAL") {
            referenciaHoras = parseMinutes($('.total-quarta').text().trim()) || 0;
        } else if (diaSemana == 'Quinta-feira' && $('.qui-txt').text().trim() != "NORMAL") {
            referenciaHoras = parseMinutes($('.total-quinta').text().trim()) || 0;
        } else if (diaSemana == 'Sexta-feira' && $('.sex-txt').text().trim() != "NORMAL") {
            referenciaHoras = parseMinutes($('.total-sexta').text().trim()) || 0;
        } else if (diaSemana == 'Sábado') {
            referenciaHoras = parseMinutes($('.total-sabado').text().trim()) || 0;
        } else if (diaSemana == 'Domingo') {
            referenciaHoras = parseMinutes($('.total-domingo').text().trim()) || 0;
        } else {
            referenciaHoras = parseMinutes($('.total-dia').text().trim()) || 0;
        }

        var bancoHoras = 0;
        var horasExtras = parseMinutes($row.find('td:nth-child(9)').text().trim()) || 0;

        if (horasTrabalhadas > referenciaHoras) {
            bancoHoras = horasTrabalhadas - referenciaHoras;
            horasTrabalhadas = referenciaHoras;
        }

        // Atualiza as colunas com valores calculados
        $row.find('td:nth-child(7)').text(formatTime(horasTrabalhadas)); // Atualiza a coluna 'horas trabalhadas'
        $row.find('td:nth-child(8)').text(formatTime(bancoHoras)); // Atualiza a coluna 'banco de horas'

        // Recalcula as horas extras, ajustando a coluna 'banco de horas' se necessário
        if (horasExtras > bancoHoras) {
            horasExtras = 0; // Zera horas extras se exceder o banco de horas
            alert('HORAS EXTRAS não pode exceder quantidade de hroas do BANCO DE HORAS')
        }
        bancoHoras -= horasExtras;

        $row.find('td:nth-child(8)').text(formatTime(Math.max(0, bancoHoras))); // Atualiza 'banco de horas'
        $row.find('td:nth-child(9)').text(formatTime(horasExtras)); // Atualiza 'horas extras'
    }

    function calcularTotais() {
        var totalGeral = 0;
        var totalBanco = 0;
        var totalExtras = 0;

        $('.tab-folha tbody tr').each(function () {
            var horasTrabalhadas = $(this).find('td:nth-child(7)').text().trim();
            totalGeral += parseMinutes(horasTrabalhadas);
        });

        $('.tab-folha tbody tr').each(function () {
            var horasBanco = $(this).find('td:nth-child(8)').text().trim();
            totalBanco += parseMinutes(horasBanco);
        });

        $('.tab-folha tbody tr').each(function () {
            var horasExtras = $(this).find('td:nth-child(9)').text().trim();
            totalExtras += parseMinutes(horasExtras);
        });

        var geral = 0;
        geral = totalGeral + totalExtras + totalBanco;

        $('#total-geral').text(formatTime(geral));
        $('#extras-geral').text(formatTime(totalExtras));
        $('#banco-geral').text(formatTime(totalBanco));
    }

    $('.tab-folha').on('blur', 'td.editable input', function () {
        var $cell = $(this).closest('td');
        var newValue = $(this).val().trim();

        if (newValue) {
            $cell.text(newValue);
            var $row = $cell.closest('tr');
            calcularHorasTrabalhadas($row);
            calcularTotais();
        } else {
            $cell.text($cell.data('originalValue')); // Reverte ao valor original se o novo valor for inválido
        }
    });

    // Delegação do evento dblclick para células com a classe editable
    $('.tab-folha').on('dblclick', 'td.editable', function () {
        var $cell = $(this);
        var currentText = $cell.text().trim();
        var width = $cell.width();
        var $input = $('<input>', {
            type: 'text',
            value: currentText,
            data: { originalValue: currentText }, // Armazena o valor original
            blur: function () {
                var newValue = $(this).val().trim();
                if (newValue) {
                    $cell.text(newValue);
                    var $row = $cell.closest('tr');
                    calcularHorasTrabalhadas($row);
                    calcularTotais();
                } else {
                    $cell.text($(this).data('originalValue')); // Reverte ao valor original se o novo valor for inválido
                }

                $cell.removeClass('editing');
            },
            keyup: function (e) {
                if (e.which === 13) { // Tecla Enter pressionada
                    $(this).blur();
                }
            }
        }).appendTo($cell.empty()).focus().width(width - 10); // Ajusta a largura do input

        $cell.addClass('editing');
    });

    // Inicializa as células com 0:00 se estiverem vazias e calcula as horas
    $('.tab-folha tbody tr').each(function () {
        var $row = $(this);
        if (!$row.find('td:nth-child(7)').text().trim()) {
            $row.find('td:nth-child(7)').text('0:00');
        }
        if (!$row.find('td:nth-child(8)').text().trim()) {
            $row.find('td:nth-child(8)').text('0:00');
        }
        if (!$row.find('td:nth-child(9)').text().trim()) {
            $row.find('td:nth-child(9)').text('0:00');
        }
        calcularHorasTrabalhadas($row);
        calcularTotais();
    });

    // Recalcula as horas trabalhadas e o banco de horas quando o valor de horas extras muda
    $('.tab-folha').on('blur', 'td:nth-child(9) input', function () {
        var $cell = $(this).closest('td');
        var newValue = $(this).val().trim();

        if (newValue) {
            var $row = $cell.closest('tr');
            var horasExtras = parseMinutes(newValue) || 0;
            var bancoHoras = parseMinutes($row.find('td:nth-child(8)').text().trim()) || 0;
            var horasTrabalhadas = parseMinutes($row.find('td:nth-child(7)').text().trim()) || 0;
            var referenciaHoras = parseMinutes($('.total-dia').text().trim()) || 0;

            if (horasExtras > bancoHoras) {
                horasExtras = 0; // Zera horas extras se exceder o banco de horas
            }

            bancoHoras -= horasExtras;

            $row.find('td:nth-child(8)').text(formatTime(Math.max(0, bancoHoras))); // Atualiza 'banco de horas'
            $row.find('td:nth-child(9)').text(formatTime(horasExtras)); // Atualiza 'horas extras'
            calcularHorasTrabalhadas($row); // Recalcula as horas trabalhadas e banco de horas
            calcularTotais();
        }
    });

    $('.tab-folha').on('change', 'input.edit-free', function () {
        var $row = $(this).closest('tr');
        if ($(this).is(':checked')) {
            $row.find('td:nth-child(7), td:nth-child(8)').addClass('editable');
        } else {
            calcularHorasTrabalhadas($row);
            calcularTotais();
            $row.find('td:nth-child(7), td:nth-child(8)').removeClass('editable');
        }
    });

    $('.tab-folha').on('click', 'button.zera-linha', function () {
        var $row = $(this).closest('tr');
        $row.find('td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7), td:nth-child(8), td:nth-child(9)').text('');
    });

    $('select[name="obs"]').on('change', function () {
        let btn = $(this).siblings('.btn_ponto_copy');

        if ($(this).val() != '0') {
            // Habilita o botão
            btn.prop('disabled', false);
        } else {
            // Desabilita o botão
            btn.prop('disabled', true);
        }
    });

    // Dispara o evento de mudança ao carregar a página para tratar o valor inicial
    $('select[name="obs"]').each(function () {
        $(this).trigger('change');
    });

    // Dispara o evento de mudança ao carregar a página para tratar o valor inicial
    $('select[name="obs"]').each(function () {
        $(this).trigger('change');
    });

    $(document).on('click', '.btn_ponto_copy', function () {
        // Encontra o <select> na mesma célula <td> do botão clicado
        let select = $(this).siblings('select[name="obs"]');
        let selectedOptionText = select.find('option:selected').text();

        // Encontra a linha (tr) da célula <td> do botão clicado
        let row = $(this).closest('tr');

        // Preenche as colunas específicas com o texto da opção selecionada
        row.find('td:nth-child(3)').text(selectedOptionText);
        row.find('td:nth-child(4)').text(selectedOptionText);
        row.find('td:nth-child(5)').text(selectedOptionText);
        row.find('td:nth-child(6)').text(selectedOptionText);
    });

});
