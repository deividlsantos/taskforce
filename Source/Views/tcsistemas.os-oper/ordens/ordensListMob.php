<?php
$this->layout("_theme_oper", $front);

$status = $status ?? null;

$dataMap = [
    'andamento' => $andamento,
    'pausadas' => $pausadas,
    'pendentes' => $pendentes,
    'futuras' => $futuras,
    'concluidas' => $concluidas,
    'canceladas' => $canceladas,
];

if ($status && isset($dataMap[$status])) {
    $filteredData = $dataMap[$status];
} else {
    $filteredData = array_merge($andamento, $pausadas, $pendentes, $futuras, $concluidas, $canceladas);
}

?>
<input id="url-acoes" value="<?= url("oper_ordens/activity") ?>" hidden>
<input id="initial-status" value="<?= $status ?>" hidden>
<div class="conteudo-mob">
    <div class="oper-tab-content scrollable-container" id="ordens-content">
        <div class="filtros">
            <div class="fcad-form-row filtro-status">
                <input class="input-busca-ordens" id="busca-ordens" placeholder="Buscar">
                <input type="checkbox" id="chk-concluidas" class="chk-status">
                <label for="chk-concluidas">Concluídas</label>
                <input type="checkbox" id="chk-canceladas" class="chk-status">
                <label for="chk-canceladas">Canceladas</label>
            </div>
            <div class="fcad-form-row filtro-data">
                <select class="form-select coluna30" id="filter">
                    <option value="any">Qualquer período</option>
                    <option value="hoje">Hoje</option>
                    <option value="last7">Últimos 7 dias</option>
                    <option value="last30">Últimos 30 dias</option>
                    <option value="custom">Personalizado</option>
                </select>
                <div class="fcad-form-group coluna70">
                    <div class="fcad-form-row custom-dates d-none" id="custom-dates">
                        <div class="fcad-form-group">
                            <input type="date" class="form-control" id="start-date" placeholder="Data inicial">
                        </div>
                        <div class="fcad-form-group">
                            <input type="date" class="form-control" id="end-date" placeholder="Data Final">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        foreach ($filteredData as $os) :
            $dataStatus = "";
            if ($os->status == 'A') {
                $dataStatus = (date_fmt($os->dataexec, "Y-m-d") > date("Y-m-d")) ? "futuras" : "pendentes";
            } elseif ($os->status == 'I') {
                $dataStatus = "andamento";
            } elseif ($os->status == 'P') {
                $dataStatus = "pausadas";
            } elseif ($os->status == 'C') {
                $dataStatus = "concluidas";
            } elseif ($os->status == 'D') {
                $dataStatus = "canceladas";
            }
        ?>
            <div class="mob-item" data-status="<?= $dataStatus ?>" data-date="<?= date_fmt($os->dataexec, "Y-m-d"); ?>" data-id="<?= $os->id; ?>" data-url="<?= url("oper_ordens/ordem"); ?>">
                <div class="barra-status barra-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"></div>
                <div class="mob-item-content">
                    <div class="mob-info fcad-form-row">
                        <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"><?= date_fmt($os->dataexec, "d/m/Y")  . " (#" . $os->id . ")"; ?></span>
                        <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?> direita"><?= secondsToTime($os->horaexec); ?></span>
                    </div>
                    <?php
                    if (!empty($os->segmento)):
                    ?>
                        <div class="mob-info fcad-form-row">
                            <span class="mob-stxt"><?= strtoupper(str_to_single(($empresa->labelFiliais))); ?>: <?= mb_strimwidth($os->segmento, 0, 40, '...'); ?>
                            </span>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="mob-info fcad-form-row">
                        <span class="mob-stxt">OS: <?= $os->id_os1; ?></span>
                        <?php
                        if (!empty($os->controle)):
                        ?>
                            <span class="mob-stxt direita">
                                <strong>CONTROLE: <?= !empty($os->controle) ? $os->controle : "-"; ?></strong>
                            </span>
                        <?php
                        endif;
                        ?>
                    </div>
                    <div class="mob-info fcad-form-row">
                        <div class="mob-stxt"><?= mb_strimwidth($os->cliente, 0, 45, "..."); ?></div>
                    </div>
                    <div class="mob-info fcad-form-row">
                        <div class="mob-stxt"><?= mb_strimwidth($os->servico, 0, 25, "..."); ?></div>
                        <span class="mob-stxt direita">
                            <?= $dataStatus == 'andamento' ? 'EM ANDAMENTO' : ($dataStatus == 'pausadas' ? 'PAUSADA' : ($dataStatus == 'pendentes' ? 'AGUARDANDO INÍCIO' : ($dataStatus == 'concluidas' ? 'CONCLUÍDA' : ($dataStatus == 'futuras' ? 'AGUARDANDO INÍCIO' : 'CANCELADA')))); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php
        endforeach;
        ?>
    </div>
</div>
<section>
    <?php
    $this->insert("ordens/ordemMob", [
        "operador" => $operador,
        "servico" => $servico,
        "equipamentos" => $equipamentos,
        "empresa" => $empresa
    ]);
    ?>
</section>