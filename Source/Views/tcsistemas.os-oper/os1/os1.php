<?php
$this->layout("_theme_oper", $front);
?>

<div class="conteudo-mob">
    <input id="url-acoes" value="<?= url("oper_ordens/activity") ?>" hidden>
    <div class="oper-tab-content scrollable-container" id="ordens-content">
        <div class="filtros">
            <div class="fcad-form-row filtro-status">
                <input class="input-busca-ordens" id="os1-busca-ordens" placeholder="Buscar">
                <input type="checkbox" id="os1-chk-concluidas" class="os1-chk-status">
                <label for="os1-chk-concluidas">Concluídas</label>
                <input type="checkbox" id="os1-chk-canceladas" class="os1-chk-status">
                <label for="os1-chk-canceladas">Canceladas</label>
                <input type="checkbox" id="chk-canceladas" class="os1-chk-status" checked hidden>
                <input type="checkbox" id="chk-concluidas" class="os1-chk-status" checked hidden>
            </div>
        </div>
        <?php
        if (!empty($os1)) :
            foreach ($os1 as $os) :
        ?>
                <div class="item-total">
                    <div class="os1-mob-item" data-status="<?= $os->status; ?>" data-date="<?= date_fmt($os->dataexec, "Y-m-d"); ?>" data-id="<?= $os->id; ?>" data-url="<?= url("oper_ordens/ordem"); ?>">
                        <div class="os1-barra-status barra-<?= $os->id_status == "2" ? "blue" : ($os->id_status == "3" ? "orange" : ($os->id_status == "4" ? "red" : ($os->id_status == "5" ? "green" : "purple"))); ?>">
                            <i class="fas fa-clipboard"></i>
                            <p><?= $os->id; ?></p>
                        </div>
                        <div class="os1-mob-item-content">
                            <div class="os1-mob-info fcad-form-row">
                                <span class="<?= strlen($os->cliente) > 15 ? 'os1-mob-txt-small' : 'os1-mob-txt'; ?>"><?= $os->cliente; ?></span>
                            </div>
                            <div class="os1-mob-info fcad-form-row">
                                <span class="os1-mob-stxt"><?= $os->status; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="mobsection" id="mobsection<?= $os->id; ?>">
                        <div class="mobsection-item">
                            <h2 class="mobsection-header" id="heading<?= $os->id; ?>">
                                <button class="mobsection-button" type="button">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </h2>
                            <div id="collapse<?= $os->id; ?>" class="">
                                <div class="mobsection-body">
                                    <?php
                                    foreach ($os2 as $tarefas) :
                                        if ($tarefas->id_os1 != $os->id) continue;
                                        $dataStatus = "";
                                        if ($tarefas->status == 'A') {
                                            $dataStatus = (date_fmt($tarefas->dataexec, "Y-m-d") > date("Y-m-d")) ? "futuras" : "pendentes";
                                        } elseif ($tarefas->status == 'I') {
                                            $dataStatus = "andamento";
                                        } elseif ($tarefas->status == 'P') {
                                            $dataStatus = "pausadas";
                                        } elseif ($tarefas->status == 'C') {
                                            $dataStatus = "concluidas";
                                        } elseif ($tarefas->status == 'D') {
                                            $dataStatus = "canceladas";
                                        }
                                    ?>
                                        <div class="mob-item" data-status="<?= $dataStatus ?>" data-date="<?= date_fmt($tarefas->dataexec, "Y-m-d"); ?>" data-id="<?= $tarefas->id; ?>" data-url="<?= url("oper_ordens/ordem"); ?>">
                                            <div class="barra-status barra-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"></div>
                                            <div class="mob-item-content">
                                                <div class="mob-info fcad-form-row">
                                                    <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"><?= date_fmt($tarefas->dataexec, "d/m/Y")  . " (#" . $tarefas->id . ")"; ?></span>
                                                    <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?> direita"><?= secondsToTime($tarefas->horaexec); ?></span>
                                                </div>
                                                <div class="mob-info fcad-form-row">
                                                    <span class="mob-stxt coluna20">OS: <?= $tarefas->id_os1; ?></span>
                                                    <span class="mob-stxt direita">
                                                        <?= $dataStatus == 'andamento' ? 'EM ANDAMENTO' : ($dataStatus == 'pausadas' ? 'PAUSADA' : ($dataStatus == 'pendentes' ? 'AGUARDANDO INÍCIO' : ($dataStatus == 'concluidas' ? 'CONCLUÍDA' : ($dataStatus == 'futuras' ? 'AGUARDANDO INÍCIO' : 'CANCELADA')))); ?>
                                                    </span>
                                                </div>
                                                <div class="mob-info fcad-form-row">
                                                    <div class="mob-stxt"><?= $tarefas->cliente; ?></div>
                                                </div>
                                                <div class="mob-info fcad-form-row">
                                                    <div class="mob-stxt <?= strlen($tarefas->servico) > 30 ? 'small-font' : ''; ?>"><?= $tarefas->servico; ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            endforeach;
        endif;
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