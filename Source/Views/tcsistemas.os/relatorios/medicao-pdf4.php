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

        .os-title {
            font-size: 20px;
            border-top: solid 2px #000;
            border-bottom: solid 2px #000;
            text-align: center;
        }

        .sectionOs {
            margin-bottom: 30px;
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

    <div class="capa">
        <h1>Acompanhamento Físico Financeiro</h1>
        <div class="info-capa">
            <p><strong><?= str_to_single($emp->labelFiliais); ?>: <?= $obra->nome; ?></strong></p>
            <p><?= $tema ?></p>
            <p><strong>Endereço: <?= $obra->endereco; ?></strong> </p>
        </div>
    </div>

    <div class="content">
        <?php
        if (!empty($obra)):
            foreach ($os1 as $os):
        ?>
                <div class="section sectionOs">
                    <div class="section-title os-title">Ordem de Serviço: <?= $os->id; ?></div>
                    <?php
                    $index = 0;
                    foreach ($os->tarefas as $tarefa):
                        if ($tarefa->mede != 1) continue;
                        $index++;
                    ?>
                        <div class="section">
                            <div class="section-title">(<?= $index; ?>) <?= $tarefa->tarefa; ?></div>
                            <div>
                                <span class="info-line"><strong>Planejamento:</strong><?= date("d/m/y", $tarefa->inicio); ?> - <?= date("d/m/y", $tarefa->fim); ?> </span>
                                <span class="info-line"><strong>Quantidade orçada:</strong><?= fmt_numeros($tarefa->qtde) . " " . $tarefa->medida ?></span>
                                <span class="info-line"><strong>Valor orçado:</strong> R$ <?= moedaBR($tarefa->vtotal) ?></span>
                                <span class="info-line"><strong>Custo unitário:</strong> R$ <?= moedaBR($tarefa->vunit) . "/" . $tarefa->medida ?></span>
                                <span class="info-line"><strong>Quantidade restante:</strong> <?= $tarefa->valorPendente . " " . $tarefa->medida; ?></span>
                                <span class="info-line"><strong>Porcentagem concluída:</strong> <?php echo number_format(($tarefa->valorMedido / $tarefa->qtde) * 100, 1) . "%"; ?></span>
                            </div>
                            <table class="table">
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>%</th>
                                    <th style="text-align: right;">Quantidade executada</th>
                                    <th style="text-align: right;">Valor medido</th>
                                </tr>
                                <?php
                                if (!empty($tarefa->medicoes)):
                                    foreach ($tarefa->medicoes as $medicao):
                                ?>
                                        <tr>
                                            <td><?= date("d/m/Y", strtotime($medicao->datai)); ?> - <?= date("d/m/Y", strtotime($medicao->dataf)); ?></td>
                                            <td><?= $medicao->obs; ?></td>
                                            <td><?= number_format(($medicao->qtde / $tarefa->qtde) * 100, 1) . "%"; ?></td>
                                            <td style="text-align: right;"><?= fmt_numeros($medicao->qtde) . " " . $tarefa->medida; ?></td>
                                            <td style="text-align: right;">R$ <?= moedaBR($medicao->qtde * $tarefa->vunit); ?></td>
                                        </tr>
                                    <?php
                                    endforeach;
                                    ?>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td style="text-align: right;"><strong>Total Quantidade:</strong> <?= fmt_numeros(array_sum(array_map(function ($medicao) {
                                                                                                                return $medicao->qtde;
                                                                                                            }, $tarefa->medicoes))) . " " . $tarefa->medida; ?></td>
                                        <td style="text-align: right;"><strong>Total:</strong> R$ <?= moedaBR(array_sum(array_map(function ($medicao) use ($tarefa) {
                                                                                                        return $medicao->qtde * $tarefa->vunit;
                                                                                                    }, $tarefa->medicoes))); ?></td>
                                    </tr>
                                <?php
                                endif;
                                ?>
                            </table>
                        </div>
                    <?php
                    endforeach;
                    ?>
                </div>
            <?php
            endforeach;
            ?>
            <div class="section section2">
                <div class="section-title">Consolidado de Medições</div>
                <div>
                    <span class="info-line"><strong><?= str_to_single($emp->labelFiliais); ?>: </strong><?= $obra->nome; ?></span>
                </div>
                <table class="table">
                    <tr>
                        <th style="width: 60%">Etapa</th>
                        <th style="text-align: right;">Qtd.</th>
                        <th style="text-align: right;">R$ unt.</th>
                        <th style="text-align: right;">Total (R$)</th>
                    </tr>
                    <?php
                    $total = 0;
                    $itens = [];

                    foreach ($os1 as $os) {
                        foreach ($os->tarefas as $tarefa) {
                            if ($tarefa->mede != 1) continue;
                            if (empty($tarefa->medicoes)) continue;

                            if (!isset($itens[$tarefa->tarefa])) {
                                $itens[$tarefa->tarefa] = [
                                    'qtde' => 0,
                                    'vunit' => $tarefa->vunit,
                                    'total' => 0,
                                    'medida' => $tarefa->medida
                                ];
                            }

                            $itens[$tarefa->tarefa]['qtde'] += array_sum(array_map(function ($medicao) {
                                return $medicao->qtde;
                            }, $tarefa->medicoes));
                            $itens[$tarefa->tarefa]['total'] += array_sum(array_map(function ($medicao) use ($tarefa) {
                                return $medicao->qtde * $tarefa->vunit;
                            }, $tarefa->medicoes));
                        }
                    }

                    foreach ($itens as $tarefa => $dados) {
                        $total += $dados['total'];
                    ?>
                        <tr>
                            <td><?= $tarefa; ?></td>
                            <td style="text-align: right;"><?= fmt_numeros($dados['qtde']) . " " . $dados['medida']; ?></td>
                            <td style="text-align: right;">R$ <?= moedaBR($dados['vunit']); ?></td>
                            <td style="text-align: right;">R$ <?= moedaBR($dados['total']); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong>R$ <?= moedaBR($total); ?></strong></td>
                    </tr>

                </table>
            </div>
        <?php
        else:
        ?>
            <p>Nenhum resultado encontrado.</p>
        <?php endif; ?>
    </div>


</body>

</html>