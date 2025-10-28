<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist - Ordem de Serviço</title>
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

        .table-chkeqp {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            border: 1px solid #333;
        }

        .table-chkeqp th,
        .table-chkeqp td {
            border: 1px solid #333;
            text-align: left;
            vertical-align: top;
            padding-left: 10px;
        }

        /* Estilos para o Checklist */
        .grupo-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .grupo-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f0f0f0;
            border-left: 4px solid #333;
        }

        /* Estilos existentes para info-section e signature-section */
        .info-section {
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-section {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
            flex-direction: row;
        }

        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        .signature-box {
            width: 50%;
            text-align: center;
            padding: 40px 50px;
        }

        .signature-box div {
            border-top: 1px solid #333;
            padding-top: 10px;
            font-size: 12px;
        }

        .grupo-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .grupo-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            padding: 4px 8px;
            background-color: #f0f0f0;
            border-left: 3px solid #333;
        }

        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #333;
            font-size: 11px;
        }

        .checklist-table th,
        .checklist-table td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }

        .header-row {
            background-color: #f5f5f5;
        }

        .header-row th {
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            padding: 6px 4px;
            line-height: 1.2;
        }

        .item-header {
            width: 20%;
            text-align: left;
        }

        .status-header {
            width: 12%;
            text-align: center;
        }

        .obs-header {
            text-align: center;
        }

        .item-row td {
            padding: 5px 6px;
        }

        .item-cell {
            font-weight: normal;
            line-height: 1.3;
            font-size: 0.8em;
        }

        .status-cell {
            text-align: center !important;
            font-size: 1em;
            font-weight: bold;
            width: 12%;
            vertical-align: middle;
        }

        .status-cell.conforme {
            color: #006600;
        }

        .status-cell.nao-conforme {
            color: #cc0000;
        }

        .status-cell.nao-aplicado {
            color: #666666;
        }

        .obs-cell {
            font-size: 10px;
            color: #333;
            line-height: 1.2;
            text-align: left;
        }

        /* Reduzir espaçamentos gerais */
        .section {
            margin: 20px 0;
            text-align: center;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        /* Estilos para impressão */
        @media print {
            body {
                font-size: 12px;
                margin: 10px;
            }

            .checklist-table {
                page-break-inside: avoid;
                font-size: 10px;
            }

            .grupo-section {
                page-break-inside: avoid;
                margin-bottom: 15px;
            }

            .grupo-title {
                font-size: 11px;
                margin-bottom: 6px;
                padding: 3px 6px;
            }

            .header-row {
                background-color: #f5f5f5 !important;
                -webkit-print-color-adjust: exact;
            }

            .header-row th {
                font-size: 9px;
                padding: 4px 3px;
            }

            .item-row td {
                padding: 3px 4px;
            }

            .item-cell {
                font-size: 10px;
            }

            .obs-cell {
                font-size: 9px;
            }

            .status-cell {
                font-size: 12px;
            }

            .status-cell.conforme {
                color: #006600 !important;
                -webkit-print-color-adjust: exact;
            }

            .status-cell.nao-conforme {
                color: #cc0000 !important;
                -webkit-print-color-adjust: exact;
            }

            .status-cell.nao-aplicado {
                color: #666666 !important;
                -webkit-print-color-adjust: exact;
            }

            .section {
                margin-bottom: 15px;
            }

            .section-title {
                font-size: 13px;
                margin-bottom: 6px;
            }
        }
    </style>
</head>

<?php
if (!empty($chkItens)):
    // Agrupar itens por grupo
    $itensPorGrupo = [];
    foreach ($chkItens as $item) {
        $itensPorGrupo[$item->grupo][] = $item;
    }

    // Ordenar cada grupo pelo id_chkitens em ordem crescente
    foreach ($itensPorGrupo as $grupoId => $itens) {
        usort($itensPorGrupo[$grupoId], function ($a, $b) {
            return $a->id_chkitens <=> $b->id_chkitens;
        });
    }
endif;
?>


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
                    <img src="<?= url("storage/uploads/") . $emp->logo; ?>" class="logo" alt="logo">
                </td>
            </tr>
        </table>
    </div>

    <?php
    //var_dump($chkItens);
    //var_dump($itensPorGrupo);
    ?>

    <?php if (!empty($chkItens)): ?>
        <div class="section">
            <div class="section-title">CHECKLIST - <?= $equipamento->descricao; ?></div>
            <div class="info-line">
                <table width="100%" class="table-chkeqp">
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>N.Série</th>
                        <th>Patrimônio</th>
                        <th>Setor</th>
                        <th>OS</th>
                    </tr>
                    <tr>
                        <td><?= $equipamento->fabricante; ?></td>
                        <td><?= $equipamento->modelofab; ?></td>
                        <td><?= $equipamento->serie; ?></td>
                        <td><?= $equipamento->tag; ?></td>
                        <td> <?= "Setor"; ?></td>
                        <td><?= $os2_2->id_os2; ?></td>
                    </tr>
                </table>
            </div>

            <?php foreach ($itensPorGrupo as $grupoId => $itens): ?>
                <?php
                // Pegar o nome do grupo do primeiro item
                $nomeGrupo = !empty($itens) ? $itens[0]->descGrupo : 'Grupo ' . $grupoId;
                ?>

                <div class="grupo-section">
                    <div class="grupo-title"><?= strtoupper($nomeGrupo) ?></div>

                    <table class="checklist-table">
                        <thead>
                            <tr class="header-row">
                                <th class="item-header">ITEM</th>
                                <th class="status-header">CONFORME</th>
                                <th class="status-header">NÃO CONFORME</th>
                                <th class="status-header">NÃO APLICADO</th>
                                <th class="obs-header">OBSERVAÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr class="item-row">
                                    <td class="item-cell">
                                        <?= htmlspecialchars($item->descricao) ?>
                                    </td>
                                    <td class="status-cell conforme">
                                        <?= $item->status == 1 ? 'X' : '' ?>
                                    </td>
                                    <td class="status-cell nao-conforme">
                                        <?= $item->status == 2 ? 'X' : '' ?>
                                    </td>
                                    <td class="status-cell nao-aplicado">
                                        <?= $item->status == 3 ? 'X' : '' ?>
                                    </td>
                                    <td class="obs-cell">
                                        <?= !empty($item->obs) ? htmlspecialchars($item->obs) : '' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="info-section">
        <div class="info-label">Observações:</div>
        <div style="min-height: 120px; border: 1px solid #ccc; margin-top: 5px; padding: 5px;">
            <?= !empty($os2_2) ? $os2_2->chkobs : ''; ?>
        </div>
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td class="signature-box">
                    <div>Técnico Executante</div>
                </td>
                <td class="signature-box">
                    <div>Responsável pelo Setor</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>