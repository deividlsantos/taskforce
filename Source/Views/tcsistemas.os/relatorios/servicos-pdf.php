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

        .header .info {
            flex-grow: 1;
            text-align: left;
            font-size: 12px;
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
            margin-left: 0;
            margin-right: 10px;
            padding-right: 10px;
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

        @media print {
            .report-footer {
                page-break-before: always;
                /* Garante que o rodapé comece em uma nova página */
            }
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

    <div class="content">
        <div class="section-title os-title">SERVIÇOS PRESTADOS</div>
        <div class="section">
            <div class="info">
                <?php
                if (!empty($periodo)):
                ?>
                    <span class="info-line"><strong>Período:</strong> <?= $periodo; ?></span>
                <?php
                endif; ?>
                <span class="info-line"><strong>Cliente:</strong> <?= $cliente->nome; ?></span>
            </div>
        </div>

        <?php if (!empty($dados)):
            $resumo = [];
            foreach ($servico as $s):
        ?>
                <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
                    <thead>
                        <tr>
                            <th colspan="8" class="func-head">Servico Prestado: <?= $s->nome; ?></th>
                        </tr>
                        <tr>
                            <th style="width: 4%;">OS</th>
                            <th style="width: 4%;">Ctrl.OS</th>
                            <th>Cód.<?= str_to_single($emp->labelFiliais); ?></th>
                            <th>Nome <?= str_to_single($emp->labelFiliais); ?></th>
                            <th style="text-align: right;">Qtde (<?= $s->medida; ?>)</th>
                            <th style="text-align: right;">Vlr.Unit.(R$)</th>
                            <th style="text-align: right;">Vlr.Total (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($dados as $vlr):
                            if ($vlr->id_servico == $s->id):

                                $totalServico = array_sum(array_map(function ($vlr) use ($s) {
                                    return $vlr->id_servico == $s->id ? $vlr->vtotal : 0;
                                }, $dados));
                        ?>
                                <tr>
                                    <td style="width: 4%;"><?= $vlr->id_os1; ?></td>
                                    <td style="width: 4%;"><?= $vlr->os1controle; ?></td>
                                    <td><?= $vlr->segmento_controle; ?></td>
                                    <td><?= mb_strimwidth($vlr->segmento_nome, 0, 25, "..."); ?></td>
                                    <td style="text-align: right;"><?= fmt_numeros($vlr->qtde) . " " . $vlr->medida; ?></td>
                                    <td style="text-align: right;">R$ <?= moedaBR($vlr->vunit); ?></td>
                                    <td style="text-align: right;">R$ <?= moedaBR($vlr->vtotal); ?></td>
                                </tr>
                        <?php
                            endif;
                        endforeach;
                        $resumo[$s->nome] = $totalServico;
                        ?>
                        <tr>
                            <td><strong>Total Itens:</strong></td>
                            <td><strong><?= count(array_filter($dados, function ($vlr) use ($s) {
                                            return $vlr->id_servico == $s->id;
                                        })); ?></strong></td>
                            <td style="text-align: right;" colspan="2"><strong>Sub Total:</strong></td>
                            <td style="text-align: right;"><strong><?= array_sum(array_map(function ($vlr) use ($s) {
                                                                        return $vlr->id_servico == $s->id ? $vlr->qtde : 0;
                                                                    }, $dados)) . " " . $s->medida; ?></strong></td>
                            <td style="text-align: right;" colspan="2"><strong>R$ <?= moedaBR($totalServico); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            <?php
            endforeach;
        else: ?>
            <p>Nenhum resultado encontrado.</p>
        <?php endif;
        $totalGeral = array_sum(array_map(function ($vlr) {
            return $vlr->vtotal;
        }, $dados)); ?>
        <div class="report-footer">
            <div>
                <strong>Resumo por Serviço:</strong>
                <table class="table">
                    <?php foreach ($resumo as $servico => $total): ?>
                        <tr>
                            <td><?= $servico; ?></td>
                            <td> R$ <?= moedaBR($total); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <strong>Total Geral: R$ <?= moedaBR($totalGeral); ?></strong>
        </div>
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