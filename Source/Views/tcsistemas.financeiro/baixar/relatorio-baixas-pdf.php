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

        .report-footer {
            margin-top: 30px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            /* joga esquerda e direita pros cantos */
            align-items: flex-start;
        }

        .totais-esquerda table td {
            padding: 2px 6px;
            text-align: left;
        }

        .totais-direita table td {
            padding: 2px 6px;
            text-align: right;
        }

        .totais-direita table td:first-child {
            text-align: left;
            /* rótulos alinhados à esquerda */
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
        <div class="section-title os-title">RELATÓRIO TÍTULOS BAIXADOS - <?= $tipo == 'pag' ? "DESPESAS" : "RECEITAS"; ?></div>
        <div class="section">
            <div class="info">
                <?php
                if (!empty($baixadoEm)):
                ?>
                    <span class="info-line"><strong>Data da baixa:</strong> <?= $baixadoEm; ?></span>
                <?php
                endif; ?>
            </div>
        </div>

        <?php
        if (!empty($dados)):
        ?>
            <table class="table table-hover bordered tabela-relatorio tab-rel-medicao-cli">
                <thead>
                    <!-- <tr>
                        <th colspan="100%" class="func-head">Servico Prestado: </th>
                    </tr> -->
                    <tr>
                        <th><?= $tipo == 'pag' ? "Fornecedor" : "Cliente"; ?></th>
                        <th>Título</th>
                        <th>Emissão</th>
                        <th style="text-align: right;">Vencimento</th>
                        <th style="text-align: right;">V.Parc(R$)</th>
                        <th style="text-align: right;">V.Desc(R$)</th>
                        <th style="text-align: right;">V.Outros(R$)</th>
                        <th style="text-align: right;">V.Baixa(R$)</th>
                        <th style="text-align: right;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_parcela = 0;
                    $total_outros  = 0;
                    $total_desc    = 0;
                    $total_baixa   = 0;
                    $total_saldo   = 0;

                    $idsSomados = [];

                    foreach ($dados as $d):

                        $indice = isset($d->id_pag) ? 'id_pag' : (isset($d->id_rec) ? 'id_rec' : null);

                        if ($indice) {
                            if (!in_array($d->{$indice}, $idsSomados)) {
                                $total_parcela += $d->valor;
                                $idsSomados[] = $d->{$indice};
                            }
                        } else {
                            // caso não tenha id_pag nem id_rec
                            $total_parcela += $d->valor;
                        }

                        $total_outros  += $d->voutros;
                        $total_desc    += $d->vdesc;
                        $total_baixa   += $d->vpago;
                        $total_saldo   += $d->saldo;
                    ?>
                        <tr <?= !$d->registro_existe ? 'style="color: red;"' : '' ?>>
                            <td><?= $d->entidade_nome . (!$d->registro_existe ? " - BAIXA ESTORNADA" : ""); ?></td>
                            <td><?= $d->titulo; ?></td>
                            <td><?= $d->datacad; ?></td>
                            <td style="text-align: right;"><?= $d->dataven; ?></td>
                            <td style="text-align: right;"><?= moedaBR($d->valor); ?></td>
                            <td style="text-align: right;"><?= moedaBR($d->vdesc); ?></td>
                            <td style="text-align: right;"><?= moedaBR($d->voutros); ?></td>
                            <td style="text-align: right;"><?= moedaBR($d->vpago); ?></td>
                            <td style="text-align: right;"><?= moedaBR($d->saldo); ?></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        <?php
        else: ?>
            <p>Nenhum resultado encontrado.</p>
        <?php
        endif;
        ?>
        <div class="report-footer">
            <div class="totais-esquerda">
                <span style="font-weight: bold; display:block; margin-bottom:5px;">Resumo Geral</span>
                <table>
                    <tr>
                        <td><strong>Total de Registros:</strong></td>
                        <td class="left"><?= count($dados); ?></td>
                    </tr>
                </table>
            </div>

            <div class="totais-direita">
                <span style="font-weight: bold; display:block; margin-bottom:5px;">Resumo Financeiro</span>
                <table>
                    <?php
                    $totais = [
                        'Total Vlr Parcela:' => $total_parcela,
                        'Total Vlr Outros:' => $total_outros,
                        'Total Vlr Parcela + Outros:' => $total_parcela + $total_outros,
                        'Total Vlr Desconto:' => $total_desc,
                        'Total Vlr Baixa:' => $total_baixa,
                        'Saldo Total:' => $total_saldo,
                    ];

                    foreach ($totais as $rotulo => $valor):
                    ?>
                        <tr>
                            <td><strong><?= $rotulo ?></strong></td>
                            <td></td>
                            <td><?= moedaBR($valor); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

    </div>
</body>

</html>