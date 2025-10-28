$(document).ready(function () {
    // função para carregar todos os usuarios no select
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

                // INSERE dentro da .dropdown da .custom-select
                $('#opt-usuario').html(html);
            },
            error: function () {
                $('#opt-usuario .dropdown').html("<span style='color:red;'>Erro ao carregar usuários</span>");
            }
        });
    }


    // já carrega todos os usuários quando a página abre
    carregarUsuarios();

    // quando mudar a empresa, recarrega usuários
    $('#empresaSelect').on('change', function () {
        let emp_id = [];
        let url = $("#empresaSelect input[type=checkbox]").first().data("url");
        $('#empresaSelect input[type=checkbox]:checked').each(function () {
            emp_id.push($(this).val());
        });
        carregarUsuarios(emp_id, url);
    });

    let offset = 0;       // deslocamento inicial
    const limit = 50;     // quantidade por vez
    let carregando = false;
    let filtrosAtuais = {}; // guarda os filtros da última pesquisa

    //pega as informaçoes do fumulario e envia pelo ajax para fazer a consulta
    $('#formLog').on('submit', function (e) {
        e.preventDefault();
        let url = $(this).data("url");
        let id_emp2 = [];
        let acao = [];
        let usuario = [];
        let campo = [];
        console.log(url);
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

        // 🔹 Reinicia offset e limpa a tabela
        offset = 0;

        carregarLogs(false); // primeira carga

        $(window).off("scroll").on("scroll", function () {
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                carregarLogs(true); // busca mais dados
            }
        });
        function carregarLogs(append = false) {
            if (carregando) return;
            carregando = true;

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
                    if (!append) tbody.empty(); // limpa só na primeira chamada

                    if (logs.length === 0) {
                        if (offset === 0) {
                            tbody.append('<tr><td colspan="7" style="color:red;">Nenhum log encontrado.</td></tr>');
                        } else {
                            $(window).off("scroll");
                        }
                        carregando = false;
                        return;
                    }

                    // 🔹 Mantém seu formato original
                    logs.forEach(log => {
                        const dataHora = new Date(log.data_hora);
                        const data = dataHora.toLocaleDateString('pt-BR');
                        const hora = dataHora.toLocaleTimeString('pt-BR', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        const tr = `
                    <tr data-id="${log.id}">
                        <td>${log.id}</td>
                        <td>${data}</td>
                        <td>${hora}</td>
                        <td>${log.empresa_razao}</td>
                        <td><a class="link-acao" data-status="${log.acao}">${log.acao}</a></td>
                        <td>${log.tabela}</td>
                        <td>${log.usuario_nome}</td>
                    </tr>
                `;
                        tbody.append(tr);
                        $(".vlrAcao").attr("hidden", true);
                    });

                    // 🔹 Atualiza o deslocamento
                    offset += limit;
                    carregando = false;
                },
                error: function () {
                    carregando = false;
                    $('<div class="alert alert-danger" style="position:fixed;top:20px;right:20px;z-index:9999;background: red;color:white; padding: 10px; font-size: 1.2em; border:1px solid #ccc; border-radius: 10px;">Erro ao buscar dados.</div>')
                        .appendTo('body')
                        .delay(3000)
                        .fadeOut(400, function () { $(this).remove(); });
                }
            });
        }
        });

    //captura as informações do enviada pelo link verificando por ação |Criação|Alteração|Exclusão|
    $(document).on('click', '.link-acao', function (e) {
        e.preventDefault();
        let id = $(this).closest("tr").data("id");
        let status = $(this).data('status');
        //console.log(id, status);

        $.ajax({
            url: "listaAcao.php",
            method: "POST",
            data: {
                id: id,
                status: status
            },
            dataType: "json",
            success: function (response) { // <-- aqui estava 'sucess'
                $("#tabelaAntes tbody, #tabelaAntes thead").empty();
                $("#tabelaDepois tbody, #tabelaDepois thead").empty();

                let dadosAntes = {};
                let dadosDepois = {};
                let alterados = {};

                // separa os dados
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

    //função que cria a tabela de comparação de dados
    function montarTabela(selector, dados, alterados) {
        let label = selector === "#tabelaAntes" ? "Antes" : "Depois";
        let thead = `<tr><th colspan="${Object.keys(dados).length}">${label}</th></tr><tr>`;
        let tbody = "<tr>";

        $.each(dados, function (campo, valor) {
            thead += "<th>" + campo + "</th>";

            // verifica se esse campo foi alterado
            let destaque = alterados && alterados[campo] ? " style='background-color: #fff3cd;'" : "";
            tbody += "<td" + destaque + ">" + (valor || "") + "</td>";
        });

        thead += "</tr>";
        tbody += "</tr>";

        $(selector + " thead").html(thead);
        $(selector + " tbody").html(tbody);
    }
});