$(document).ready(function () {
    const dataContainer = document.getElementById('dataContainer');
    let os1 = JSON.parse(dataContainer.dataset.os1);
    let os2 = JSON.parse(dataContainer.dataset.os2);
    let os3 = dataContainer.dataset.os3 ? JSON.parse(dataContainer.dataset.os3) : null;
    let os2os3 = dataContainer.dataset.os2os3 ? JSON.parse(dataContainer.dataset.os2os3) : null;
    let url = dataContainer.dataset.url;

    function refreshData() {
        $.ajax({
            url: url, // Altere para a URL do seu backend que retorna os dados atualizados
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                os1 = data.os1;
                os2 = data.os2;
                if (data.os3) {
                    os3 = data.os3;
                }
                if (data.os2os3) {
                    os2os3 = data.os2os3;
                }

                // Atualiza o calendário com os novos dados
                $('#calendar').fullCalendar('removeEvents'); // Remove os eventos antigos
                os1.forEach(function (os) {
                    // Encontra a primeira os2 associada a esta os1
                    const relatedOs2 = os2.filter(tarefa => tarefa.id_os1 == os.id);

                    if (relatedOs2.length > 0) {
                        // Constrói a descrição com os detalhes de cada os2
                        let description = "OS: " + os.id + "<br>Status: " + os.status + "<br>Cliente: " + os.cliente + "<br>Valor Total: R$ " + os.vtotal + "<br>";
                        relatedOs2.forEach(tarefa => {
                            description += "Tarefa #" + tarefa.id + ": " + tarefa.servico + ", Responsável: " + tarefa.colaborador + "<br>";
                        });

                        // Usa dataexec da primeira os2 como data de início do evento
                        $('#calendar').fullCalendar('renderEvent', {
                            id: os.id,
                            title: 'OS ' + os.id,
                            start: relatedOs2[0].dataexec,  // Define data de início do evento
                            allDay: true,
                            color: os.cor,
                            description: description
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Erro ao atualizar os dados:', error);
            }
        });
    }

    function dateToSeconds(date) {
        // Converte a data para segundos
        const localDate = new Date(date + 'T00:00:00');
        return localDate.getTime() / 1000;
    }

    function secondsToHour(seconds) {
        // Calcula as horas e os minutos
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);

        // Formata para "hh:mm"
        const formattedHours = String(hours).padStart(2, '0');
        const formattedMinutes = String(minutes).padStart(2, '0');

        return `${formattedHours}:${formattedMinutes}`;
    }

    function formatarValor(valor) {
        var numeroFormatado = parseFloat(valor).toFixed(2);
        var partes = numeroFormatado.split('.');
        partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return partes.join(',');
    }

    function limparValores() {
        $("#div-os1").find("textarea").val("");
        $("#div-os1").find("input").val("");
        $("#div-os1").find("select").val("");
        $("#container-linhas2").find("input").val("");
        $("#container-linhas2").find("select").val("");
        $("#container-linhas3").find("input").val("");
        $("#container-linhas3").find("select").val("");
        $("#tarefaseq").val("1");
        $("#materialseq").val("1");
        $("#sumservico").text("0,00");
        $("#summaterial").text("0,00");
        $('#status option:first').prop('selected', true);
        $('input[name="OS2_qtd_servico_1"]').val('1');
        $('input[name="OS2_numero_1"]').css('border', 'none');
        $('input[name="OS2_numero_1"]').attr('data-status', "0");
        $('#novaos-submit').prop("disabled", false);
        $('#status').prop("disabled", false);
        $(".medicaoOs2").css('display', 'none');
        $('#medicao_1').data('tarefamedicao', '');
        $('#medicao_1').prop('disabled', true);
        $('#medicao_1').closest('.divbtnmedicao').addClass('medicao-desabilitado').attr('data-tooltip', 'Primeiro salve a OS!');
    }

    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        viewRender: function (view, element) {
            refreshData(); // Carregar eventos para o novo intervalo
        },
        eventLimit: true,
        editable: true,
        selectable: true,
        locale: 'pt-BR',
        buttonText: {
            today: 'Hoje',
        },
        monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],

        dayClick: function (date, jsEvent, view) {
            $('.ltm').each(function () {
                if (!$(this).hasClass('original')) {
                    $(this).remove();
                }
            });

            $('.lmm').each(function () {
                if (!$(this).hasClass('original')) {
                    $(this).remove();
                }
            });
            limparValores();
            // Configura um temporizador para detectar o duplo clique
            let clickTimeout;
            if (clickTimeout) {
                clearTimeout(clickTimeout);
            }

            $('#obra').trigger('change');
            $('#cliente-os').trigger('change');
            $('#abre-obra').trigger('change');

            clickTimeout = setTimeout(() => {
                if (jsEvent.detail === 2) { // Se for um duplo clique
                    $('#modalNovaOs').modal('show'); // Abre a modal para criação de um novo evento
                    $('input[name="OS2_dataexec_1').val(date.format()); // Preenche a data selecionada no campo de data da modal
                    $('#title-edit').text('Nova O.S.');
                }
            }, 300); // Tempo para detectar um duplo clique
        },

        eventClick: function (event, jsEvent, view) {

            $('.ltm').each(function () {
                if (!$(this).hasClass('original')) {
                    $(this).remove();
                }
            });

            $('.lmm').each(function () {
                if (!$(this).hasClass('original')) {
                    $(this).remove();
                }
            });

            limparValores();

            // Atualiza o título da modal com as informações do evento
            $('#title-edit').text('Editar/Visualizar OS: ' + event.id);

            // Preencher a div-os1 com os dados da OS
            const ordem = os1.find(os => os.id == event.id);
            if (ordem) {
                $('#id_os1').val(ordem.id);
                $('#OS1_controle').val(ordem.controle);
                $('#OS1_id').val(ordem.id);
                $('#status').val(ordem.id_status);
                $('#status').attr('data-os1', ordem.id);
                $('#status').attr('data-status', ordem.id_status);
                $('#cliente-os').val(ordem.id_cli);
                $('#vtotal').val(ordem.vtotal);
                $('#obs').val(ordem.obs);

                if (ordem.id_obras) {
                    $('#abre-obra').prop('checked', true);
                } else {
                    $('#abre-obra').prop('checked', false);
                }

                $('#obra').val(ordem.id_obras);
                $('#obra').trigger('change');
                $('#cliente-os').trigger('change');
                $('#abre-obra').trigger('change');

                if (ordem.id_status == '5' || ordem.id_status == '7') {
                    $('#novaos-submit').prop("disabled", true);
                    $('#status').prop("disabled", true);
                }
            }

            var itensOs2 = os2.filter(item => item.id_os1 == event.id).length;
            var itensOs3 = 0;
            if (os3) {
                itensOs3 = os3.filter(item => item.id_os1 == event.id).length;
            }
            var itensos2os3 = 0;
            if (os2os3) {
                itensos2os3 = os2os3.filter(item => item.id_os1 == event.id).length;
            }

            let i = 0;

            $('.mat-accordion-item.os2-clone').remove();

            os2.forEach(tarefa => {
                if (tarefa.id_os1 == event.id) {
                    i++;
                    addTarefa(tarefa, i);
                    if (i < itensOs2) {
                        $("#tarefanovamodal").trigger("click");
                    }

                    // Clonar e atualizar o elemento modelo
                    var $original = $('.mat-accordion-item.os2-model');
                    var $clone = $original.clone().removeClass('os2-model').addClass('os2-clone');

                    $clone.find('#container-accordion-os2os3-').attr('id', 'container-accordion-os2os3-' + tarefa.id);
                    $clone.find('#container-accordion-os2os3-' + tarefa.id).attr('data-value', tarefa.id);
                    $clone.find('#container-accordion-os2os3-' + tarefa.id).addClass('os2os3');
                    $clone.find('.accordion-button').attr('data-bs-target', '#container-accordion-os2os3-' + tarefa.id);
                    $clone.find('.accordion-button').attr('aria-controls', 'container-accordion-os2os3-' + tarefa.id);
                    $clone.find('.accordion-button').text('Produtos/Materiais da Tarefa #' + tarefa.id);
                    $clone.find('#linha-material-').attr('id', 'linha-material-' + tarefa.id);
                    $clone.find('.accordion-button').prop("hidden", false);
                    $clone.find('[name]').each(function () {
                        var name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace('#', tarefa.id));
                        }
                    });

                    $clone.insertBefore('.mat-accordion-item.os2-model:last');
                }
            });

            i = 0;

            if (itensOs3 > 0) {
                os3.forEach(material => {
                    if (material.id_os1 == event.id) {
                        i++;
                        addMaterial(material, i);
                        if (i < itensOs3) {
                            $("#matnovomodal").trigger("click");
                        }
                    }
                });
            }

            if (itensos2os3 > 0) {
                // Seleciona todos os elementos com a classe .os2os3
                var elementos = $('.os2os3');

                // Itera sobre os elementos encontrados
                elementos.each(function (index, element) {
                    var container = $(element).attr('data-value');
                    var count = 0;
                    os2os3.forEach(material => {
                        if (material.id_os2 == container) {
                            count++;
                        }
                    });

                    i = 0;
                    os2os3.forEach(material => {
                        if (material.id_os2 == container) {
                            i++;
                            addMaterial2(material, i, container, $(element));
                            if (i < count) {
                                $(element).find("#matnovomodaltarefa").trigger("click");
                            }
                        }
                    });
                });
            }

            // Exibe a modal ao clicar no evento
            $('#modalNovaOs').modal('show');
            jsEvent.preventDefault();
        },

        eventDrop: function (event, delta, revertFunc) {
            // Obtém a nova data para a qual o evento foi arrastado
            const newDate = event.start.format('YYYY-MM-DD');

            // Atualiza o campo de data na modal com a nova data
            $('input[name="OS2_dataexec_1"]').val(newDate);

            // Envia a nova data para o backend via AJAX
            $.ajax({
                url: url, // Altere para a URL do seu backend
                method: 'POST',
                data: {
                    id: event.id,       // ID do evento para identificar no banco
                    newDate: newDate    // Nova data do evento
                },
                success: function (response) {
                    refreshData();
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao atualizar a data:', error);
                    revertFunc(); // Reverte o evento ao local original em caso de erro
                }
            });
        },

        eventRender: function (event, element) {
            element.tooltipster({
                content: event.description,
                theme: 'tooltipster-light',
                contentAsHTML: true
            });
        }

    });

    // Função para adicionar uma nova tarefa
    function addTarefa(tarefa, seq) {
        var tempo = tarefa.tempo / 60;
        var hora = secondsToHour(tarefa.horaexec);

        var $dataOriginal = dateToSeconds(tarefa.dataexec) + tarefa.horaexec;

        $('input[name="OS2_id_' + seq + '"]').val(tarefa.id);
        $('input[name="OS2_numero_' + seq + '"]').val("#" + tarefa.id);
        $('select[name="OS2_operador_' + seq + '"]').val(tarefa.id_colaborador);
        $('select[name="OS2_servico_' + seq + '"]').val(tarefa.id_servico);
        $('input[name="OS2_vtotal_servico_' + seq + '"]').val(tarefa.vtotal);
        $('input[name="OS2_vunit_servico_' + seq + '"]').val(tarefa.vunit);
        $('input[name="OS2_qtd_servico_' + seq + '"]').val(tarefa.qtde);
        $('input[name="OS2_tempo_' + seq + '"]').val(tempo);
        $('input[name="OS2_dataexec_original_' + seq + '"]').val($dataOriginal);
        $('input[name="OS2_dataexec_' + seq + '"]').val(tarefa.dataexec);
        $('input[name="OS2_horaexec_' + seq + '"]').val(hora);

        $('#medicao_' + seq).data('tarefamedicao', tarefa.id).prop('disabled', false);
        $('#medicao_' + seq).closest('.divbtnmedicao').removeClass('medicao-desabilitado').removeAttr('data-tooltip');

        $('input[name="OS2_numero_' + seq + '"]').css('border', '3px solid' + tarefa.cor);

        $('input[name="OS2_numero_' + seq + '"]').attr('data-status', tarefa.status);

        $('select[name="OS2_servico_' + seq + '"]').trigger('change');
        $('input[name="OS2_qtd_servico_' + seq + '"]').trigger("input");
    }

    // Função para adicionar um novo material
    function addMaterial(material, seq) {
        $('input[name="OS3_id_' + seq + '"]').val(material.id);
        $('select[name="OS3_material_' + seq + '"]').val(material.id_materiais);
        $('input[name="OS3_qtd_material_' + seq + '"]').val(material.qtde);
        $('input[name="OS3_valor_material_' + seq + '"]').val(formatarValor(material.vunit));
        $('input[name="OS3_vtotal_material_' + seq + '"]').val(formatarValor(material.vtotal));

        $('select[name="OS3_material_' + seq + '"]').trigger('change');
        $('input[name="OS3_qtd_material_' + seq + '"]').trigger('input');
    }

    function addMaterial2(material, seq, tarefa, container) {
        container.find('input[name="OS3_id_' + tarefa + '_' + seq + '"]').val(material.id);
        container.find('select[name="OS3_material_' + tarefa + '_' + seq + '"]').val(material.id_materiais);
        container.find('input[name="OS3_qtd_material_' + tarefa + '_' + seq + '"]').val(material.qtde);
        container.find('input[name="OS3_valor_material_' + tarefa + '_' + seq + '"]').val(formatarValor(material.vunit));
        container.find('input[name="OS3_vtotal_material_' + tarefa + '_' + seq + '"]').val(formatarValor(material.vtotal));
        container.find('input[name="OS3_id_tarefa_' + tarefa + '_' + seq + '"]').val(tarefa);

        container.find('select[name="OS3_material_' + tarefa + '_' + seq + '"]').trigger('change');
        container.find('input[name="OS3_qtd_material_' + tarefa + '_' + seq + '"]').trigger('input');

    }


    // Adiciona eventos a partir de os1
    os1.forEach(function (os) {
        const relatedOs2 = os2.filter(tarefa => tarefa.id_os1 == os.id);

        if (relatedOs2.length > 0) {
            // Constrói a descrição com os detalhes de cada os2
            let description = "OS: " + os.id + "<br>Status: " + os.status + "<br>Cliente: " + os.cliente + "<br>Valor Total: R$ " + os.vtotal + "<br>";
            relatedOs2.forEach(tarefa => {
                description += "Tarefa #" + tarefa.id + ": " + tarefa.servico + ", Responsável: " + tarefa.colaborador + "<br>";
            });

            // Usa dataexec da primeira os2 como data de início do evento
            $('#calendar').fullCalendar('renderEvent', {
                id: os.id,
                title: 'OS ' + os.id,
                start: relatedOs2[0].dataexec,  // Define data de início do evento
                allDay: true,
                color: os.cor,
                description: description
            });
        }
    });

    $('#calendar').on('eventDrop', refreshData); // Exemplo: ao mover um evento, recarrega os dados
    $('#calendar').on('eventResize', refreshData);

    $('input[name="OS2_dataexec_1"]').on('change', refreshData);
    $("#form-novaos").on('submit', function (event) {
        setTimeout(function () {
            refreshData();
        }, 500);
    });

    tooltipTarefas('.tarefanumero');

    function tooltipTarefas(input) {
        $(document).on('mouseenter', input, function (event) {
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

            const modal = $(this).closest('.modal-content'); // Seleciona o contêiner da modal
            modal.append(tooltipDiv);

            const rect = this.getBoundingClientRect();
            const modalRect = modal[0].getBoundingClientRect();

            tooltipDiv.css({
                top: (rect.top - modalRect.top - tooltipDiv.outerHeight() - 5) + 'px',
                left: (rect.left - modalRect.left + (rect.width / 2) - (tooltipDiv.outerWidth() / 2)) + 'px'
            });

            $(this).data('tooltipDiv', tooltipDiv);
        });

        $(document).on('mouseleave', input, function () {
            const tooltipDiv = $(this).data('tooltipDiv');
            if (tooltipDiv) {
                tooltipDiv.remove();
                $(this).removeData('tooltipDiv');
            }
        });
    }
});
