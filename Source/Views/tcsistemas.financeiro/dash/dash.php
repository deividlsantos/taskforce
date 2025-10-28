<?php
$this->layout("_theme", $front);
?>

<div class="painel-financeiro ">
    <div class="d-flex flex-column" style="margin: 0; padding: 0; width: 100%;">
        <div id="url-dash" data-url="<?= url('dash/graficos') ?>" hidden></div>
        <input id="mes-escolhido" name="mes-escolhido" value="<?= date("m/Y"); ?>" hidden>
        <input id="dia-escolhido" name="dia-escolhido" value="<?= date("Y-m-d"); ?>" hidden>
        <input id="semana-escolhida" name="semana-escolhida" value="<?= date("Y-m-d"); ?>" hidden>
        <div class="filtrosdash1">
            <div class="filtro-situacao filtro-item coluna15">
                <label for="situacao">Situação:</label>
                <select id="situacao" class="input-categoria">
                    <option value="todas">Todas</option>
                    <option value="N" selected>Aberto</option>
                    <option value="S">Baixado</option>
                </select>
            </div>
            <button type="button" class="btn btn-filtro fdash-mprev"><span class="fa-solid fa-chevron-left"></span></button>
            <button type="button" data-valor="<?= date("m/Y"); ?>" class="btn btn-filtro fdash-matu coluna15">Mês Atual</button>
            <button type="button" class="btn btn-filtro fdash-mnext"><span class="fa-solid fa-chevron-right"></span></button>

            <button type="button" class="btn btn-filtro fdash-tprev"><span class="fa-solid fa-chevron-left"></span></button>
            <button type="button" data-valor="<?= date("Y-m-d"); ?>" class="btn btn-filtro fdash-today">Hoje</button>
            <button type="button" class="btn btn-filtro fdash-tnext"><span class="fa-solid fa-chevron-right"></span></button>

            <button type="button" class="btn btn-filtro fdash-wprev"><span class="fa-solid fa-chevron-left"></span></button>
            <button type="button" data-valor="<?= date("Y-m-d"); ?>" class="btn btn-filtro fdash-week">Semana</button>
            <button type="button" class="btn btn-filtro fdash-wnext"><span class="fa-solid fa-chevron-right"></span></button>

            <button type="button" class="btn btn-filtro fdash-all">Tudo</button>
        </div>
        <div class="filtrosdash fcad-form-row" style="width: fit-content;">
            <div class="filtro-periodo filtro-item direita coluna10">
                <label for="periodo">Período:</label>
                <input type="date" id="data-inicio" class="input-data" required>
            </div>
            <div class="filtro-periodo filtro-item coluna10">
                <label for="periodo">até:</label>
                <input type="date" id="data-fim" class="input-data" required>
            </div>
            <div class="filtro-item">
                <button type="button" id="filtrar-periodo-dash" class="btn btn-filtro"><i class="fa fa-filter"></i></button>
            </div>
        </div>
    </div>
    <div class="painel-cartoes fcad-form-row">
        <div class="cartao vfiltro fcad-form-row">
            <div class="valor-vfiltro coluna100">Tudo - (situação: Aberto)</div>
        </div>
    </div>

    <div class="painel-cartoes fcad-form-row">
        <div class="cartao entradas">
            <div class="titulo-cartao">Receitas</div>
            <div class="valor-cartao">R$ <?= moedaBR($totalreceitas->data->totalrec); ?></div>
        </div>
        <div class="cartao despesas">
            <div class="titulo-cartao">Despesas</div>
            <div class="valor-cartao">R$ <?= moedaBR($totaldespesas->data->totalpag); ?></div>

        </div>
        <div class="cartao saldo">
            <div class="titulo-cartao">Saldo</div>
            <?php
            $saldo = $totalreceitas->data->totalrec - $totaldespesas->data->totalpag;
            $class = $saldo < 0 ? 'negativo' : 'positivo'; // Adiciona a classe com base no saldo
            ?>
            <div class="valor-cartao <?= $class ?>">R$ <?= moedaBR($totalreceitas->data->totalrec - $totaldespesas->data->totalpag); ?></div>

        </div>
    </div>

    <div class="painel-grafico">
        <div class="grafico-container-tabela">
            <div class="tabela-wrapper" style="overflow-x: auto;">
                <table class="table table-striped table-hover minitable">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>Receitas</th>
                            <th>Despesas</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $valoresDia = [];
                        $dataAtual = date("Y-m-d");

                        if (!empty($receitas)) {
                            foreach ($receitas as $rec) {
                                $data = $rec->data_mov;
                                $valoresDia[$data]['receitas'] = $rec->totalrec;
                            }
                        }

                        if (!empty($despesas)) {
                            foreach ($despesas as $desp) {
                                $data = $desp->data_mov;
                                $valoresDia[$data]['despesas'] = $desp->totalpag;
                            }
                        }

                        ksort($valoresDia);

                        // Encontre a data mais próxima da data atual (igual ou anterior)
                        $datas = array_keys($valoresDia);
                        $linhaDestacada = null;
                        foreach (array_reverse($datas) as $data) {
                            if ($data <= $dataAtual) {
                                $linhaDestacada = $data;
                                break;
                            }
                        }

                        // Exibir os dados na tabela
                        foreach ($valoresDia as $data => $valores) :
                            $receitas = isset($valores['receitas']) ? $valores['receitas'] : 0;
                            $despesas = isset($valores['despesas']) ? $valores['despesas'] : 0;
                            $isLinhaDestacada = ($data == $linhaDestacada) ? 'font-weight: bold;' : '';
                            $idLinha = ($data == $linhaDestacada) ? 'id="linha-atual"' : '';
                        ?>
                            <tr style="<?= $isLinhaDestacada; ?>" <?= $idLinha; ?>>
                                <td style="width: 30%; margin: 0;"><?= date_fmt($data, "d/m/Y"); ?></td>
                                <td><?= moedaBR($receitas); ?></td>
                                <td><?= moedaBR($despesas); ?></td>
                                <td><?= moedaBR($receitas - $despesas); ?></td>
                            </tr>
                        <?php
                        endforeach;
                        ?>
                </table>
            </div>
        </div>
        <div class="grafico-container">
            <label>Receitas x Despesas</label>
            <canvas id="graficoDespesas"></canvas>
        </div>
        <div class="grafico-container">
            <label>Evolução Mensal</label>
            <canvas id="graficoEvolucaoMensal"></canvas>
        </div>
    </div>
</div>