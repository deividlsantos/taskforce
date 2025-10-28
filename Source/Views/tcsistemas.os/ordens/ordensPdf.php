<?php

use Source\Models\Ent;
use Source\Models\Materiais;
use Source\Models\Obras;
use Source\Models\Servico;

?>
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
            margin-top: 0;
            /* Evita sobreposição com o cabeçalho */
        }

        .info-line {
            display: block;
            margin-bottom: 5px;
            width: 200px;
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

        .section2 {
            margin-top: 120px;
            page-break-before: always;
        }

        .capa {
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
        }

        table thead tr {
            border-bottom: 2px solid #000;
        }

        .assinaturas {
            margin-top: 150px;
        }

        .orcamento {
            padding-top: 0;
            text-align: center;
            font-size: 1em;
            font-weight: bold;
            margin-top: 0;
        }

        .observacoes {
            font-size: 0.7em;
            margin-top: 10px;
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
                        <?php
                        $linhas = [
                            "{$emp->endereco}, nº {$emp->numero} – {$emp->bairro} | {$emp->cidade} - {$emp->uf} | CEP: {$emp->cep}",
                            !empty($emp->fone1) ? $emp->fone1 : null,
                            !empty($emp->email) ? $emp->email : null,
                            !empty($emp->cnpj) ? 'CPF/CNPJ: ' . $emp->cnpj : null
                        ];
                        echo implode('<br>', array_filter($linhas));
                        ?>
                    </div>
                </td>
                <td>
                    <?php if (!empty($emp->logo) && file_exists("storage/uploads/" . $emp->logo)) : ?>
                        <img src="<?= url("storage/uploads/") . $emp->logo; ?>" class="logo" alt="">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="content">
        <?php
        if (!empty($os1)):
            if ($os1->id_status == 8):
                echo "<div class='orcamento'><h1>**ORÇAMENTO**</h1></div>";
            endif;
        ?>
            <div class="section">
                <div class="section-title"></div>
                <table>
                    <tr>
                        <td class="info-line"><strong>Cliente:</strong></td>
                        <td> <?= (new Ent())->findById($os1->id_cli)->nome; ?></td>
                    <tr>
                    <tr>
                        <td class="info-line"><strong>Data:</strong></td>
                        <td><?= date_fmt($os1->datacad, "d/m/Y"); ?></td>
                    </tr>
                    <tr>
                        <td class="info-line"><strong>Data da Emissão:</strong></td>
                        <td><?= date("d/m/Y"); ?></td>
                    </tr>

                    <?= !empty($os1->id_obras) ? '<tr><td class="info-line"><strong>' . str_to_single($emp->labelFiliais) . ':</strong> </td> <td>' . (new Obras())->findById($os1->id_obras)->nome . '</td></tr>' : ""; ?>
                    <tr>
                        <td class="info-line"><strong>Endereço:</strong></td>
                        <td><?= !empty($os1->id_obras) ? (new Obras())->findById($os1->id_obras)->endereco : (new Ent())->findById($os1->id_cli)->endereco; ?></td>
                    </tr>
                </table>
            </div>
            <div class="section">
                <table class="table">
                    <tr>
                        <th>Item</th>
                        <th style="width: 60%">Descrição</th>
                        <th style="text-align: right">Qtde</th>
                        <th style="text-align: right">Medida</th>
                        <th style="text-align: right">Valor</th>
                        <th style="text-align: right">Total</th>
                    </tr>
                    <?php
                    $index = 0;
                    $total = 0;
                    if (!empty($os2)):
                        foreach ($os2 as $tarefa):
                            $index++;
                    ?>
                            <tr>
                                <td><?= $index; ?></td>
                                <td><?= (new Servico())->findById($tarefa->id_servico)->nome; ?></td>
                                <td style="text-align: right"><?= fmt_numeros($tarefa->qtde); ?></td>
                                <td style="text-align: right"><?= (new Servico())->findById($tarefa->id_servico)->medida; ?></td>
                                <td style="text-align: right">R$ <?= moedaBR($tarefa->vunit); ?></td>
                                <td style="text-align: right">R$ <?= moedaBR($tarefa->vtotal); ?></td>
                            </tr>
                        <?php
                        $total += $tarefa->vtotal;
                        endforeach;
                    endif;

                    if (!empty($os3)):
                        foreach ($os3 as $mat):
                            $index++;
                        ?>
                            <tr>
                                <td><?= $index; ?></td>
                                <td><?= (new Materiais())->findById($mat->id_materiais)->descricao; ?></td>
                                <td style="text-align: right"><?= fmt_numeros($mat->qtde); ?></td>
                                <td style="text-align: right"><?= (new Materiais())->findById($mat->id_materiais)->unidade; ?></td>
                                <td style="text-align: right">R$ <?= moedaBR($mat->vunit); ?></td>
                                <td style="text-align: right">R$ <?= moedaBR($mat->vtotal); ?></td>
                            </tr>
                        <?php
                        $total += $mat->vtotal;
                        endforeach;
                    endif;

                    if ($index == 0):
                        ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Nenhuma tarefa registrada.</td>
                        </tr>
                    <?php
                    else:
                    ?>
                        <tr>
                            <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                            <td style="text-align: right"><strong>R$ <?= moedaBR($total); ?></strong></td>
                        </tr>
                    <?php
                    endif;
                    ?>
                </table>
            </div>
            <?php
            if (!empty($os1->obs)):
            ?>
                <div class="section">
                    <table class="table-obs-os" width="100%">
                        <thead>
                            <tr>
                                <td style="border-bottom: 2px solid #000;"><strong>Observações:</strong></td>
                            </tr>
                        </thead>
                    </table>
                    <p class="observacoes" style="white-space: pre-wrap;"><?= htmlspecialchars($os1->obs); ?></p>
                </div>
            <?php
            endif;
            ?>

            <div class="section assinaturas">
                <table width="100%">
                    <tr>
                        <td width="10%"></td>
                        <td style="text-align: center; border-top: 1px solid #000; padding: 10px;">
                            <strong><?= $emp->razao; ?></strong>
                        </td>
                        <td width="10%"></td>

                        <td style="text-align: center; border-top: 1px solid #000; padding: 10px;">
                            <strong><?= (new Ent())->findById($os1->id_cli)->nome; ?></strong>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
            </div>
        <?php
        endif;
        ?>
    </div>
</body>

</html>