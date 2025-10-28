<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            /* Ajuste a margem para garantir que o conteúdo não sobreponha o cabeçalho */
        }

        @page {
            size: A4 portrait;
            /* Espaço reservado para o cabeçalho */
            margin-top: 110px;
            margin-bottom: 10px;
            /* Espaço para o rodapé */
        }

        .header {
            width: 100%;
            position: fixed;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding: 0;
            margin-right: 10%;
            height: 80px;
            margin-top: 10px;
            /* Altura fixa para evitar sobreposição */
            background-color: white;
            /* Evita transparência */
            top: -110px;
            /* Ajuste para garantir que o cabeçalho esteja na posição correta */
        }

        .header .info {
            flex-grow: 1;
            text-align: left;
            font-size: 12px;
        }

        .content {
            margin-top: 20px;
            /* Evita sobreposição com o cabeçalho */
        }

        .info-line {
            display: block;
            margin-bottom: 5px;
            /* Espaçamento entre as linhas */
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .tableheader {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
        }

        .tableheader td {
            vertical-align: middle;
            margin: 0;
            padding: 0;
        }

        .tableheader td:last-child {
            text-align: right;
            width: 1%;
            white-space: nowrap;
        }

        .logo {
            width: 80px;
            /* Ajuste conforme necessário */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border-bottom: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        .table th {
            border-bottom: 1px solid #000;
        }

        .report-footer {
            margin-top: 20px;
            font-size: 12px;
        }

        /* .capa {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            page-break-after: always;
        }

        .capa h1 {
            font-size: 36px;
            margin-bottom: 10px;
            margin-top: 300px;
        }

        .info-capa {
            font-size: 20px;
            line-height: 1.5;
        }

        .info-capa strong {
            font-weight: bold;
        } */

        .os-title {
            font-size: 20px;
            border-top: solid 2px #000;
            border-bottom: solid 2px #000;
            text-align: center;
        }

        .sectionOs {
            margin-bottom: 30px;
        }

        .func-head {
            font-size: 16px;
            font-weight: bold;
            background-color: #f0f0f0;
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="tableheader">
            <tr>
                <td>
                    <div class="info">
                        <strong><?= $emp->razao ?></strong><br>
                        <?= $emp->endereco; ?>, nº <?= $emp->numero; ?> – <?= $emp->bairro ?> | <?= $emp->cidade . " - " . $emp->uf; ?> | CEP: <?= $emp->cep; ?><br>
                        <?= !empty($emp->fone1) ? $emp->fone1 . "<br>" : ""; ?>
                        <?= !empty($emp->email) ? $emp->email . "<br>" : ""; ?>
                        CPF/CNPJ: <?= $emp->cnpj; ?>
                    </div>
                </td>
                <td>
                    <img src="<?= url("storage/uploads/") . $emp->logo; ?>" class="logo" alt="Logo">
                </td>
            </tr>
        </table>
    </div>

    <!-- <div class="capa">
        <h1>Acompanhamento Físico Financeiro</h1>
        <div class="info-capa">
            <p><strong>Obra: </strong></p>
            <p></p>
            <p><strong>Endereço: </strong> </p>
        </div>
    </div> -->

    <div class="content">
        <div class="section-title os-title">CUSTO DE PRODUÇÃO POR OPERADOR/RESPONSÁVEL</div>
        <?php if (!empty($dados)):
            foreach ($func as $f):
        ?>
                <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
                    <thead>
                        <tr>
                            <th colspan="8" class="func-head">Responsável: <?= $f->nome; ?></th>
                        </tr>
                        <tr>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Serviço</th>
                            <th><?= str_to_single($emp->labelFiliais); ?></th>
                            <th>Obs</th>
                            <th style="text-align: right;">Qtde</th>
                            <th style="text-align: right;">Vlr.Unit.(R$)</th>
                            <th style="text-align: right;">Vlr.Total (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($dados as $medicao):
                            if ($medicao->id_operador == $f->id):
                        ?>
                                <tr>
                                    <td><?= $medicao->inicio; ?></td>
                                    <td><?= $medicao->fim; ?></td>
                                    <td><?= mb_strimwidth($medicao->tarefa, 0, 30, '...'); ?></td>
                                    <td><?= !empty($medicao->obra) ? mb_strimwidth($medicao->obra, 0, 30, '...') : ""; ?></td>
                                    <td><?= mb_strimwidth($medicao->obs, 0, 30, '...'); ?></td>
                                    <td style="text-align: right;"><?= fmt_numeros($medicao->qtde) . " " . $medicao->medida; ?></td>
                                    <td style="text-align: right;">R$ <?= moedaBR($medicao->unitario); ?></td>
                                    <td style="text-align: right;">R$ <?= moedaBR($medicao->total); ?></td>
                                </tr>
                        <?php
                            endif;
                        endforeach;
                        ?>
                        <tr>
                            <td><strong>Total Itens:</strong></td>
                            <td><strong><?= count(array_filter($dados, function ($medicao) use ($f) {
                                            return $medicao->id_operador == $f->id;
                                        })); ?></strong></td>
                            <td style="text-align: right;" colspan="5"><strong>Custo Total:</strong></td>
                            <td style="text-align: right;"><strong>R$ <?= moedaBR(array_sum(array_map(function ($medicao) use ($f) {
                                                                            return $medicao->id_operador == $f->id ? $medicao->total : 0;
                                                                        }, $dados))); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            endforeach;
        else: ?>
            <p>Nenhum resultado encontrado.</p>
        <?php endif; ?>
    </div>
</body>

<script>
    $("table").tablesorter({
        headers: 'th:first-child',
        sortList: [
            [0, 0]
        ]
    });
</script>

</html>