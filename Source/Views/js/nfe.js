$(document).ready(function () {

    $(document).on('submit', '#formNfe', function (e) {
        e.preventDefault();
        let url = $(this).data('url');

        $.ajax({
            type: 'POST',
            url: url,
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const xmlContent = response.xml;
                    const blob = new Blob([xmlContent], { type: 'application/xml' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = response.filename || 'nfe.xml';
                    a.click();
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('Erro ao gerar XML: ' + response.message);
                }
            }
        });
    });

    $(document).on('click', '.verifica-emit', function (e) {
        e.preventDefault();
        $('#emit').toggle();
    });

    $(document).on('click', '.verifica-dest', function (e) {
        e.preventDefault();
        $('#dest').toggle();
    });

    $('#unit').on('input', function () {
        let vlr_unit = $(this).val();
        let qtd = $('[name="prod_qtd"]').val();

        let total = parseFloat(vlr_unit) * parseFloat(qtd);
        $('[name="prod_total"]').val(total);
    });

    $('#add').on('click', function (e) {
        e.preventDefault();

        const desc = $('[name="prod_xProd"]').val();
        const qtd = $('[name="prod_qCom"]').val();
        const valor = $('[name="prod_uCom"]').val();
        const total = $('[name="prod_vProd"]').val();

        if (!desc || !qtd || !valor || !total) return;

        const rowId = Date.now();

        // 🟢 Linha da tabela
        const row = `
        <tr data-id="${rowId}">
            <td>${desc}</td>
            <td>${qtd}</td>
            <td>R$ ${parseFloat(valor).toFixed(2)}</td>
            <td>R$ ${parseFloat(total).toFixed(2)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removerProduto(${rowId})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
        $('#produtos-tbody').append(row);

        // 🟢 Inputs ocultos do produto
        const hiddenInputs = `
        <div id="hidden-${rowId}">
            <input type="hidden" name="prod_xProd[]" value="${desc}">
            <input type="hidden" name="prod_qCom[]" value="${qtd}">
            <input type="hidden" name="prod_uCom[]" value="${valor}">
            <input type="hidden" name="prod_vProd[]" value="${total}">
        </div>
    `;
        $('#inputs-ocultos').append(hiddenInputs);

        // 🔥 CLONA os impostos
        const impostos = $('#impostos-template')
            .clone()
            .removeAttr('id')
            .show()
            .attr('data-produto', rowId);

        $('#impostos-container').append(impostos);

        atualizarTotalProdutos();

    });



    $('[name="numero_nf"]').prop('readonly', true);
    $('[name="serie"]').prop('readonly', true);

    $('#cliente_busca').select2({
        placeholder: 'Buscar por nome, CPF ou CNPJ',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: $('#cliente_busca').data('url'),
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    termo: params.term || ''
                };
            },
            processResults: function (data) {

                // 🔥 AQUI É O PONTO CHAVE
                if (data.results.length === 0) {
                    // nenhum cliente
                    $('#btn-opcoes').hide();
                    $('#nfe-novocli').show();
                } else {
                    // tem cliente
                    $('#nfe-novocli').hide();
                    $('#btn-opcoes').show();
                }

                return {
                    results: data.results
                };
            },
        }
    });

    $(document).on('click', function () {
        $('#nfe-novocli').hide();
        $('#btn-opcoes').show();
    });

    $('#cliente_busca').on('select2:select', function (e) {

        let cliente = e.params.data;

        $('#cli_id').val(cliente.id).prop('readonly', true);
        $('#dest_ie').val(cliente.ie).prop('readonly', true);
        $('#dest_cnpj').val(cliente.cnpj).prop('readonly', true);
        $('#dest_razao').val(cliente.text).prop('readonly', true);
        $('#dest_cep').val(cliente.cep).prop('readonly', true);
        $('#dest_endereco').val(cliente.endereco).prop('readonly', true);
        $('#dest_num').val(cliente.numero).prop('readonly', true);
        $('#dest_bairro').val(cliente.bairro).prop('readonly', true);
        $('#dest_cidade').val(cliente.cidade).prop('readonly', true);
        $('#dest_estado').val(cliente.estado).prop('readonly', true);
        $('#dest_telefone').val(cliente.fone).prop('readonly', true);
        $('#dest_email').val(cliente.email).prop('readonly', true);

        // ✅ Achou cliente → botão normal
        $('#nfe-novocli').hide();
        $('#btn-opcoes').show();
    });

    $('#prod_xProd').select2({
        placeholder: 'Selecione o produto',
        allowClear: true,
        minimumInputLength: 1,
        ajax: {
            url: $('#prod_xProd').data('url'),
            type: 'POST',
            dataType: 'json',
            delay: 250
        }
    });

    $(document).on('click', '#impostos', function (e) {
        e.preventDefault();
        $('#impostos-div').toggle();
        if ($('#impostos-div').is(':visible')) {
            $('#prod-nfe').css('margin-top', '200px');
        } else {
            $('#prod-nfe').css('margin-top', '');
        }
    });

});

function atualizarTotalProdutos() {
    let total = 0;

    $('#produtos-tbody tr').each(function () {
        // Pega a 4ª coluna da linha (Total do Item)
        let valorTexto = $(this).find('td:nth-child(4)').text();
        // Remove "R$" e espaços e converte para número
        let valor = parseFloat(valorTexto.replace('R$', '').trim());

        if (!isNaN(valor)) {
            total += valor;
        }
    });

    // Coloca no campo Total dos Produtos
    $('[name="total_produtos"]').val(total.toFixed(2));
    $('[name="total_nota"]').val(total.toFixed(2));
    $('.base').val(total.toFixed(2));
}

function removerProduto(id) {
    $(`tr[data-id="${id}"]`).remove();
    $(`#hidden-${id}`).remove();
    atualizarTotalProdutos();
}