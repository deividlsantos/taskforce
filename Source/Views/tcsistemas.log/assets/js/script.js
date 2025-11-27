$(document).ready(function () {

    // =========================
    // 🔹 Função para carregar usuários
    // =========================
    function carregarUsuarios(emp_id = '', url = '') {
        $.ajax({
            url: url,
            method: "post",
            data: { id: emp_id },
            dataType: 'json',
            success: function (data) {
                let html = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(usuario => {
                        html += `
                        <label>
                            <input type="checkbox" value="${usuario.id}">
                            <span>${usuario.nome}</span>
                        </label>`;
                    });
                } else {
                    html = "<span style='color:red;'>Nenhum usuário encontrado.</span>";
                }
                $('#opt-usuario').html(html);
            },
            error: function () {
                $('#opt-usuario .dropdown').html("<span style='color:red;'>Erro ao carregar usuários</span>");
            }
        });
    }

    // Já carrega todos os usuários ao abrir
    carregarUsuarios();

    // Recarrega usuários ao mudar empresa
    $('#empresaSelect').on('change', function () {
        let emp_id = [];
        let url = $("#empresaSelect input[type=checkbox]").first().data("url");
        $('#empresaSelect input[type=checkbox]:checked').each(function () {
            emp_id.push($(this).val());
        });
        carregarUsuarios(emp_id, url);
    });

    // =========================
    // 🔹 Variáveis globais para o scroll infinito
    // =========================
    let offset = 0;
    const limit = 50;
    let carregando = false;
    let filtrosAtuais = {};
    let fimDosDados = false;

    // =========================
    // 🔹 Submissão do formulário de filtros
    // =========================
    $('#formLog').on('submit', function (e) {
        e.preventDefault();

        let url = $(this).data("url");
        let id_emp2 = [];
        let acao = [];
        let usuario = [];
        let campo = [];

        $('#empresaSelect input[type=checkbox]:checked').each(function () {
            id_emp2.push($(this).val());
        });
        $('#acao input[type=checkbox]:checked').each(function () {
            acao.push($(this).val());
        });
        $('#usuario input[type=checkbox]:checked').each(function () {
            usuario.push($(this).val());
        });
        $('#campo input[type=checkbox]:checked').each(function () {
            campo.push($(this).val());
        });

        let inicial = $('#inicial').val();
        let final = $('#final').val();

        filtrosAtuais = { url, id_emp2, acao, usuario, campo, inicial, final };

        // Reinicia offset e limpa tabela
        offset = 0;
        fimDosDados = false;
        $('#resultado').empty();

        // Primeira carga
        carregarLogs(false);
    });

    // =========================
    // 🔹 Função para carregar logs com paginação
    // =========================
    function carregarLogs(append = false) {
        if (carregando || fimDosDados) return;
        carregando = true;
        $("#loading").show();

        $.ajax({
            url: filtrosAtuais.url,
            method: "post",
            data: {
                id_emp2: filtrosAtuais.id_emp2,
                acao: filtrosAtuais.acao,
                usuario: filtrosAtuais.usuario,
                campo: filtrosAtuais.campo,
                inicial: filtrosAtuais.inicial,
                final: filtrosAtuais.final,
                offset: offset,
                limit: limit
            },
            dataType: 'json',
            success: function (logs) {
                const tbody = $('#resultado');
                if (!append) tbody.empty();

                if (logs.length === 0) {
                    if (offset === 0) {
                        tbody.append('<tr><td colspan="7" style="color:red;">Nenhum log encontrado.</td></tr>');
                    } else {
                        fimDosDados = true;
                    }
                    $("#loading").hide();
                    carregando = false;
                    return;
                }

                logs.forEach(log => {
                    const dataHora = new Date(log.data_hora);
                    const data = dataHora.toLocaleDateString('pt-BR');
                    const hora = dataHora.toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    tbody.append(`
                    <tr data-id="${log.id}">
                        <td>${log.id}</td>
                        <td>${data}</td>
                        <td>${hora}</td>
                        <td>${log.empresa_razao}</td>
                        <td><a class="link-acao" data-status="${log.acao}" data-url="${$('.url').val()}">${log.acao}</a></td>
                        <td>${log.tabela}</td>
                        <td>${log.usuario_nome}</td>
                    </tr>
                `);
                });

                offset += limit;
                $("#loading").hide();
                carregando = false;
            },
            error: function () {
                $("#loading").hide();
                carregando = false;
                $('<div class="alert alert-danger" style="position:fixed;top:20px;right:20px;z-index:9999;background:red;color:white;padding:10px;border-radius:10px;">Erro ao buscar dados.</div>')
                    .appendTo('body')
                    .delay(3000)
                    .fadeOut(400, function () { $(this).remove(); });
            }
        });
    }

    // =========================
    // 🔹 Scroll controlado (carrega só enquanto rola)
    // =========================
    let scrollTimer = null;
    let isScrolling = false;

    // Detecta o scroll dentro do container da tabela
    $(".informacao .table-container").on("scroll", function () {
        const container = this;
        const scrollTop = container.scrollTop;
        const visibleHeight = container.clientHeight;
        const contentHeight = container.scrollHeight;

        // Marca que o usuário está rolando
        isScrolling = true;

        // Enquanto estiver rolando e próximo do fim, carrega mais
        if (isScrolling && scrollTop + visibleHeight >= contentHeight - 120 && !carregando && !fimDosDados) {
            carregarLogs(true);
        }

        // Quando o usuário parar de rolar (300ms sem movimento), desativa
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
            isScrolling = false;
        }, 300);
    });


    // =========================
    // 🔹 Exibe logs de antes/depois ao clicar em AÇÃO
    // =========================
    $(document).on('click', '.link-acao', function (e) {
        e.preventDefault();
        let id = $(this).closest("tr").data("id");
        let status = $(this).data('status');
        let url = $(this).data("url");

        $.ajax({
            url: url,
            method: "POST",
            data: { id: id, status: status },
            dataType: "json",
            success: function (response) {
                $("#tabelaAntes tbody, #tabelaAntes thead").empty();
                $("#tabelaDepois tbody, #tabelaDepois thead").empty();

                let dadosAntes = {};
                let dadosDepois = {};
                let alterados = {};

                $.each(response, function (campo, valores) {
                    dadosAntes[campo] = valores.antes;
                    dadosDepois[campo] = valores.depois;
                    alterados[campo] = valores.alterado;
                });

                montarTabela("#tabelaAntes", dadosAntes, alterados);
                montarTabela("#tabelaDepois", dadosDepois, alterados);
                $(".vlrAcao").removeAttr("hidden");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Erro na requisição:", textStatus, errorThrown);
            }
        });
    });

    // =========================
    // 🔹 Monta tabelas de comparação antes/depois
    // =========================
    function montarTabela(selector, dados, alterados) {
        let label = selector === "#tabelaAntes" ? "Antes" : "Depois";
        let campos = Object.keys(dados);
        let thead = `<tr><th colspan="${campos.length}">${label}</th></tr><tr>`;
        let tbody = "<tr>";

        $.each(dados, function (campo, valor) {
            thead += "<th>" + campo + "</th>";

            let textoCompleto = (valor || "").trim();
            let textoCurto = textoCompleto.length > 10 ? textoCompleto.substring(0, 10) + "..." : textoCompleto;

            if (campo === "obs" && textoCompleto.length > 10) {
                textoCurto = textoCompleto.substring(0, 10) + "...";
            }

            let destaque = alterados && alterados[campo] ? " style='background-color: #fff3cd;'" : "";
            tbody += `<td${destaque} title="${textoCompleto.replace(/"/g, '&quot;').replace(/\n/g, ' ')}">${textoCurto}</td>`;
        });

        thead += "</tr>";
        tbody += "</tr>";

        $(selector + " thead").html(thead);
        $(selector + " tbody").html(tbody);
    }
});
