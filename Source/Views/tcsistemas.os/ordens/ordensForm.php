<?php

use Source\Models\Obras;
use Source\Models\Os2;
use Source\Models\Os2_1;
use Source\Models\Os2_2;
use Source\Models\Os3;
use Source\Models\Status;

?>
<?php
$disable = "";
if ($ordens == ""):
?>
    <div class="fcad-form-row" style="margin-bottom: -30px;">
        <div class="checkbox-group coluna05 direita">
            <label for="orcamento-os1">Orçamento</label>
            <input type="checkbox" id="orcamento-os1">
        </div>
    </div>
<?php
else:
    if ($ordens->id_status == 5 || $ordens->id_status == 7):
        $disable = "disabled";
    endif;
endif;
?>
<div class="ordem1 <?= $user->tipo == 3 ? 'os1-all-disabled' : ''; ?>">
    <input type="text" id="id_os1" name="id_os1" value="<?= ($ordens != "") ? ll_encode($ordens->id) : ''; ?>" hidden>
    <div class="ordens-form">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna05">
                <label for="">Ordem nº</label>
                <input type="text" id="OS1_id" value="<?= ($ordens != "") ? $ordens->id : ''; ?>" disabled>
            </div>
            <div class="fcad-form-group coluna10">
                <label for="">Nº Controle</label>
                <input type="text" id="OS1_controle" name="OS1_controle"
                    value="<?= $ordens != "" && !empty($ordens->controle) ? $ordens->controle : ''; ?>" <?= $disable; ?>>
            </div>
            <div class="fcad-form-group coluna15">
                <label for="">Status</label>
                <select <?php
                        $readonly = "";
                        if ($ordens == ""):
                            $readonly = "select-readonly";
                        endif;
                        ?> id="status"
                    class="os1-status <?= $readonly; ?>" data-os1="<?= ($ordens != "") ? $ordens->id : '0'; ?>"
                    data-status="<?= ($ordens != "") ? $ordens->id_status : '0'; ?>" name="OS1_status" value=""
                    data-url="<?= url("ordens/cancelar"); ?>" <?= $disable; ?>>
                    <?php
                    if (!empty($status)):
                        foreach ($status as $st):
                            $temp = "";
                            $hidden = "";
                            if ($ordens != "" && $ordens->id_status == $st->id):
                                $temp = "selected";
                            endif;
                            if ($ordens != "" && $ordens->id_status != 5):
                                if ($st->id == 5):
                                    $hidden = "hidden-option";
                                endif;
                            endif;
                            if (!empty($ordens)):
                                if ($st->id == 8):
                                    $hidden = "hidden-option";
                                endif;
                            endif;
                    ?>
                            <option class="<?= $hidden ?>" value="<?= $st->id; ?>" <?= $temp; ?>><?= $st->descricao; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <?php
            if (!empty($tipo)):
                if (count($tipo) > 1):
            ?>
                    <div class="fcad-form-group">
                        <label for="">Tipo</label>
                        <select id="tipo-os" name="OS1_tipo" value="" required
                            <?= $disable; ?>>
                            <?php
                            if (!empty($tipo)):
                                foreach ($tipo as $t):
                                    $temp = "";
                                    if ($ordens != "" && $ordens->id_tipo == $t->id):
                                        $temp = "selected";
                                    endif;
                            ?>
                                    <option value="<?= $t->id; ?>" <?= $temp; ?>><?= $t->descricao; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
            <?php
                endif;
            endif;
            ?>
            <div class="fcad-form-group coluna20">
                <label for="">Cliente</label>
                <select id="cliente-os" name="OS1_cliente" value="" required
                    class="<?= $ordens != "" ? "select-readonly" : ""; ?>" <?= $disable; ?>>
                    <option value="">Selecione</option>
                    <?php
                    if (!empty($cliente)):
                        foreach ($cliente as $cli):
                            $temp = "";
                            if ($ordens != "" && $ordens->id_cli == $cli->id):
                                $temp = "selected";
                            endif;
                    ?>
                            <option value="<?= $cli->id; ?>" <?= $temp; ?>><?= $cli->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div class="fcad-form-group coluna05" style="margin-left: -10px; display: flex; flex-direction: row;">
                <button type="button" style="margin-right: 5px" class="btn btn-info newreg" id="findcli" <?= $disable; ?>
                    <?= $ordens != "" ? "hidden" : ""; ?>><i class="fa fa-search"></i></button>
                <button type="button" class="btn btn-info newreg" id="novocli" <?= $disable; ?> <?= $disable; ?>
                    <?= $ordens != "" ? "hidden" : ""; ?>><i class="fa fa-plus"></i></button>
            </div>
            <div class="fcad-form-group check-cabecalho-os">
                <label for=""><?= $label; ?></label>
                <input class="check-obras" type="checkbox" id="abre-obra" <?= $ordens != "" && !empty($ordens->id_obras) ? "checked" : "" ?>>
            </div>
            <div class="fcad-form-group direita inputreadonly coluna15 <?= $user->tipo == 3 ? 'tphantom' : ''; ?>">
                <label for="">Valor Total</label>
                <input class="mask-money" type="text" id="vtotal" name="OS1_vtotal"
                    value="<?= ($ordens != "") ? moedaBR($ordens->vtotal) : ''; ?>">
            </div>
        </div>
        <div class="fcad-form-row" id="obra-container" style="display: none;">
            <div class="fcad-form-group coluna90">
                <label for="">Descrição <?= $label; ?></label>
                <select id="obra" name="OS1_obra" <?= $disable; ?>>
                    <option value="">Selecione</option>
                    <?php
                    if (!empty($obras)):
                        foreach ($obras as $obra):
                            $temp = "";
                            if ($ordens != "" && $ordens->id_obras == $obra->id):
                                $temp = "selected";
                            endif;
                    ?>
                            <option data-ent="<?= $obra->id_ent_cli ?>" value="<?= $obra->id; ?>" <?= $temp; ?>>
                                <?= $obra->nome . " - " . $obra->endereco; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div class="fcad-form-group coluna05" style="margin-left: -10px; display: flex; flex-direction: row;">
                <button type="button" style="margin-right: 5px" class="btn btn-info newreg" id="findobra"
                    data-url="<?= url("obras/listar"); ?>" <?= $disable; ?>><i class="fa fa-search"></i></button>
                <button type="button" class="btn btn-info newreg" id="pickobra" <?= $disable; ?>><i
                        class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna90">
                <label for="">Obs</label>
                <textarea type="text" id="obs" name="OS1_obs" <?= $disable; ?>><?= ($ordens != "") ? $ordens->obs : ''; ?></textarea>
            </div>
            <div class="fcad-form-group coluna05">
                <button type="button" class="btn btn-info newreg" id="pickobs" <?= $disable; ?>><i
                        class="fa fa-comment-dots"></i></button>
            </div>
        </div>
    </div>
</div>


<div id="div-os2" class="ordem1 <?= $user->tipo == 3 ? 'os2-all-disabled' : ''; ?>">
    <div class="fcad-form-row">
        <label>TAREFAS</label>
        <label class="direita">STATUS:</label>
        <?php
        if (!empty($status)):
            foreach ($status as $st):
        ?>
                <label><i style="color: <?= $st->cor ?>" class="fa-solid fa-circle"></i><?= $st->descricao ?></label>
        <?php
            endforeach;
        endif;
        ?>
        <button type="button" class="btn btn-info" id="ordenar"><i
                class="fa-solid fa-arrow-down-short-wide"></i></button>
    </div>
    <div id="container-linhas2">
        <?php
        if ($ordens != ""):
            $os2 = (new Os2())->find(
                "id_os1 = :id_os1 AND (aditivo IS NULL OR aditivo = 'N')",
                "id_os1={$ordens->id}",
                "*",
                false
            )->fetch(true);
        endif;
        if (!empty($os2)):
            $seq = 1;
            $medicoes = new Os2_1();
            $materiaisPorTarefa = new Os3();
            $equipamentosDaTarefa = new Os2_2();
            foreach ($os2 as $tarefa):
                if ($user->tipo == 3 && $tarefa->id_colaborador != $user->id_ent)
                    continue;

                $medicoesLista = $medicoes->findByOs2($tarefa->id);

                $materiaisLista = "";
                $materiaisLista = $materiaisPorTarefa->find(
                    "id_os2 = :id_os2",
                    "id_os2={$tarefa->id}",
                    "*",
                    false
                )->fetch(true);

                $totalMateriaisTarefa = 0;
                if (!empty($materiaisLista)) {
                    foreach ($materiaisLista as $os3mat) {
                        $totalMateriaisTarefa += $os3mat->vtotal;
                    }
                }

                $equipamentosLista = "";
                $equipamentosLista = $equipamentosDaTarefa->findByOs2($tarefa->id);

                $travaSelectServico = '';
                $corBotaoMedicoes = '';
                $corBotaoMateriais = '';
                $corBotaoEquipamentos = '';
                $corBotaoObs = '';
                if (!empty($medicoesLista)) {
                    $tarefa->medicoes = $medicoesLista;
                    $travaSelectServico = 'select-readonly';
                    $corBotaoMedicoes = 'color: chartreuse;';
                } else {
                    $tarefa->medicoes = []; // Garante que a chave exista mesmo se não houver medições
                }

                if (!empty($materiaisLista)) {
                    $travaSelectServico = 'select-readonly';
                    $corBotaoMateriais = 'color: chartreuse;';
                }

                if (!empty($equipamentosLista)) {
                    $travaSelectServico = 'select-readonly';
                    $corBotaoEquipamentos = 'color: chartreuse;';
                }

                if (!empty($tarefa->obs)) {
                    $corBotaoObs = 'color: chartreuse;';
                }

                $horaexec = secondsToTime($tarefa->horaexec);

                if ($tarefa->status == "A") {
                    $tarefa->cor = (new Status())->findById(2, 'cor')->cor;
                } else if ($tarefa->status == "I") {
                    $tarefa->cor = (new Status())->findById(3, 'cor')->cor;
                } else if ($tarefa->status == "P") {
                    $tarefa->cor = (new Status())->findById(4, 'cor')->cor;
                } else if ($tarefa->status == "C") {
                    $tarefa->cor = (new Status())->findById(5, 'cor')->cor;
                } else if ($tarefa->status == "D") {
                    $tarefa->cor = (new Status())->findById(7, 'cor')->cor;
                }
        ?>

                <div class="ordens-form linhatarefa <?= $seq == 1 ? 'original' : ""; ?>" id="linha-tarefa">
                    <input type="text" id="OS2_id" name="OS2_id_<?= $seq; ?>" value="<?= $tarefa->id; ?>" hidden>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna07">
                            <div class="fcad-form-row">
                                <div class="fcad-form-group">
                                    <input type="text" id="tarefaseq" name="" value="<?= $seq; ?>" disabled>
                                </div>
                                <div class="fcad-form-group">
                                    <button type="button" data-tooltip="ALTERAR STATUS" data-status="<?= $tarefa->status; ?>"
                                        data-tarefa="<?= $tarefa->id; ?>" class="btn btn-info btn-os2-att-status" <?= $disable; ?> <?= $disable != 'disabled' && $ordens->id_status == 8 ? "disabled" : ""; ?>><i class="fa fa-sliders"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="fcad-form-group colunatarefanumero">
                            <label class="" for="">Tarefa</label>
                            <input type="text" data-status="<?= $tarefa->status; ?>"
                                style="border: 3px solid <?= $tarefa->cor; ?>" class="tarefanumero0" id="tarefanumero"
                                name="OS2_numero_1" value="<?= "#" . $tarefa->id; ?>" readonly>
                        </div>
                        <div class="fcad-form-group coluna20 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Operador<span><button type="button" class="btn btn-info btn-opr-search" <?= $disable; ?>><i class="fa fa-search"></i></button></span></label>
                            <select class="selectOperador <?= $tarefa->status != 'A' ? 'select-readonly' : ''; ?>"
                                id="selectNovoOperador" name="OS2_operador_<?= $seq; ?>" value="" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($operador)):
                                    foreach ($operador as $oper):
                                        $temp = "";
                                        if ($oper->id == $tarefa->id_colaborador):
                                            $temp = "selected";
                                        endif;
                                ?>
                                        <option value="<?= $oper->id; ?>" <?= $temp; ?>><?= $oper->nome . " " . $oper->fantasia; ?>
                                        </option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Serviço
                                <span><button type="button" class="btn btn-info btn-srv-search" <?= $travaSelectServico == 'select-readonly' ? "hidden" : ""; ?> <?= $disable; ?>><i class="fa fa-search"></i></button></span>
                                <span><button type="button" class="btn btn-info btn-srv-novo" <?= $travaSelectServico == 'select-readonly' ? "hidden" : ""; ?> <?= $disable; ?>><i class="fa fa-plus"></i></button></span>
                            </label>
                            <select class="selectServico <?= $travaSelectServico; ?>"
                                data-url="<?= url("recorrencias/verifica") ?>" id="selectNovoServico"
                                name="OS2_servico_<?= $seq; ?>" value="" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($servico)):
                                    foreach ($servico as $serv):
                                        $temp = "";
                                        if ($serv->id == $tarefa->id_servico):
                                            $temp = "selected";
                                        endif;
                                ?>
                                        <option value="<?= $serv->id; ?>" data-valor="<?= $serv->valor; ?>"
                                            data-tempo="<?= $serv->tempo; ?>" data-medicao="<?= $serv->medicao; ?>"
                                            data-unidade="<?= $serv->medida; ?>" data-recorrencia="<?= $serv->id_recorrencia; ?>"
                                            data-diarecorrencia="<?= $serv->dia; ?>"
                                            data-datalegal="<?= !empty($serv->datalegal) ? calculaDataRecorrente($serv->datalegal, $serv->recor_datalegal) : ""; ?>"
                                            <?= $temp; ?>>
                                            <?= $serv->nome; ?>
                                        </option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna10">
                            <label for="">Data Execução</label>
                            <input type="hidden" name="OS2_dataexec_original_<?= $seq; ?>"
                                value="<?= !empty($tarefa->dataexec) ? strtotime($tarefa->dataexec) + $tarefa->horaexec : ""; ?>">
                            <input type="date" id="dataexec" name="OS2_dataexec_<?= $seq; ?>"
                                value="<?= date_fmt($tarefa->dataexec, "Y-m-d"); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna07">
                            <label for="">Hora</label>
                            <input type="time" id="horaexec" name="OS2_horaexec_<?= $seq; ?>" value="<?= $horaexec; ?>"
                                <?= $disable; ?>>
                        </div>
                        <?php
                        if (ll_decode($_SESSION['mostraDataLegal']) == 'X'):
                        ?>
                            <div class="fcad-form-group coluna10">
                                <label for="">Data Legal</label>
                                <input type="date" id="datalegal" name="OS2_datalegal_<?= $seq; ?>"
                                    value="<?= !empty($tarefa->datalegal) ? date_fmt($tarefa->datalegal, "Y-m-d") : ""; ?>"
                                    <?= $disable; ?>>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div class="fcad-form-group coluna05">
                            <button
                                type="button"
                                class="btn btn-danger deltarefa"
                                <?= $disable; ?>
                                <?= $tarefa->status != "A" || !empty($travaSelectServico) ? "hidden" : ""; ?>>
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna05 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Qtde</label>
                            <input type="text" id="qtd_servico" name="OS2_qtd_servico_<?= $seq; ?>"
                                value="<?= fmt_numeros($tarefa->qtde); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna02">
                            <span class="unidade-servico" name="OS2_und_servico_<?= $seq; ?>"></span>
                        </div>
                        <div class="fcad-form-group coluna07 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">V.Unit.</label>
                            <input class="mask-money2" id="vunit_servico" name="OS2_vunit_servico_<?= $seq; ?>"
                                value="<?= moedaBR($tarefa->vunit); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna07 inputreadonly">
                            <label for="">V.Total</label>
                            <input type="text" id="vtotal_servico" name="OS2_vtotal_servico_<?= $seq; ?>"
                                value="<?= moedaBR($tarefa->vtotal); ?>" readonly>
                        </div>
                        <div class="fcad-form-group coluna07 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Duração(min)</label>
                            <input type="text" id="tempo" name="OS2_tempo_<?= $seq; ?>" value="<?= $tarefa->tempo / 60; ?>"
                                <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna10 medicaoOs2">
                            <?php
                            if (!empty($tarefa->medicoes)):
                                $totalMedido = 0;
                                foreach ($tarefa->medicoes as $medicao):
                                    $totalMedido += $medicao->qtde;
                                endforeach;
                            else:
                                $totalMedido = 0;
                            endif;
                            ?>
                            <label style="text-align: right;">Medições</label>
                            <p class="medicao-os2-parcial"><span class="medicao-os2-totalfeito"><?= $totalMedido ? $totalMedido : 0; ?></span>/<span class="medicao-os2-totalcontratado"><?= $tarefa->qtde; ?></span></p>
                        </div>
                        <div class="fcad-form-group coluna05 medicaoOs2">
                            <button type="button" style="<?= $corBotaoMedicoes; ?>" class="btn btn-info btn-os2-medicao"
                                data-url="<?= url("medicao/atualiza") ?>" data-tarefamedicao="<?= $tarefa->id; ?>"
                                id="medicao_1" <?= $disable; ?>><i class="fa fa-pen-ruler"></i></button>
                        </div>
                        <div class="fcad-form-group coluna10 recorrencia <?= $user->tipo == 3 ? 'tphantom os2-item-disabled' : ''; ?>">
                            <label for="">Recorrência</label>
                            <select name="OS2_recorrencia_<?= $seq; ?>" id="recorrencia-os2" data-loaded="false" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($recorrencias)):
                                    foreach ($recorrencias as $recorrencia):
                                        $temp = "";
                                        if ($recorrencia->id == $tarefa->id_recorrencia):
                                            $temp = "selected";
                                        endif;
                                ?>
                                        <option data-dia="<?= $recorrencia->dia ?>" value="<?= $recorrencia->id ?>" <?= $temp; ?>>
                                            <?= $recorrencia->descricao; ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div
                            class="fcad-form-group coluna10 datafixa <?= $user->tipo == 3 ? 'tphantom os2-item-disabled' : ''; ?>">
                            <label for="">Dia Recorrência</label>
                            <input type="text" id="datafixa" name="OS2_datafixa_<?= $seq; ?>"
                                value="<?= $tarefa->dia_recorrencia; ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna05 direita item-modal">
                            <label for="">Prod/Mat</label>
                            <button type="button" style="<?= $corBotaoMateriais; ?>"
                                class="btn btn-info btn-os2-itensModal btn-os2mat"
                                data-url="<?= url("ordens/verificamateriais") ?>" data-tarefa="<?= $tarefa->id; ?>"
                                data-total="<?= $totalMateriaisTarefa; ?>" <?= $disable; ?>><i class="fa fa-box"></i></button>
                        </div>
                        <?php
                        if (ll_decode($_SESSION['servicosComEquipamentos']) == 'X'):
                        ?>
                            <div class="fcad-form-group coluna05 item-modal">
                                <label for="">Equip.</label>
                                <button type="button" style="<?= $corBotaoEquipamentos; ?>"
                                    class="btn btn-info btn-os2-itensModal btn-os2eqp"
                                    data-url="<?= url("ordens/verificaequipamentos") ?>" data-tarefa="<?= $tarefa->id; ?>"
                                    <?= $disable; ?>><i class="fa fa-tractor"></i></button>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div style="margin: 0;" class="fcad-form-group coluna05 item-modal">
                            <label for="">Obs</label>
                            <button type="button" style="<?= $corBotaoObs; ?>" class="btn btn-info btn-os2-itensModal"
                                data-bs-toggle="collapse" data-bs-target="#obs-accordion-<?= $seq; ?>" aria-expanded="false"
                                aria-controls="obs-accordion-<?= $seq; ?>" <?= $disable; ?>><i
                                    class="fa fa-sticky-note"></i></button>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div style="padding:10px" class="fcad-form-group">
                            <div id="obs-accordion-<?= $seq; ?>" class="accordion-collapse collapse">
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05"><label>Obs</label></div>
                                    <div class="fcad-form-group">
                                        <textarea class="" name="OS2_obs_<?= $seq; ?>" <?= $disable; ?>><?= isset($tarefa->obs) ? $tarefa->obs : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $seq++;
            endforeach;
        else:
            ?>
            <div class="ordens-form linhatarefa original" id="linha-tarefa">
                <input type="text" id="OS2_id" name="OS2_id_1" value="" hidden>
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna05">
                        <label class="transparent" for="">_</label>
                        <input type="text" id="tarefaseq" name="" value="1" disabled>
                    </div>
                    <div class="fcad-form-group coluna20">
                        <label for="">Operador<span><button type="button" class="btn btn-info btn-opr-search" <?= $disable; ?>><i class="fa fa-search"></i></button></span></label>
                        <select class="selectOperador" id="selectNovoOperador" name="OS2_operador_1" value="" <?= $disable; ?>>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($operador)):
                                foreach ($operador as $oper):
                            ?>
                                    <option value="<?= $oper->id; ?>"><?= $oper->nome . " " . $oper->fantasia; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-group">
                        <label for="">Serviço
                            <span><button type="button" class="btn btn-info btn-srv-search" <?= $disable; ?>><i class="fa fa-search"></i></button></span>
                            <span><button type="button" class="btn btn-info btn-srv-novo" <?= $disable; ?>><i class="fa fa-plus"></i></button></span>
                        </label>
                        <select class="selectServico" data-url="<?= url("recorrencias/verifica") ?>" id="selectNovoServico"
                            name="OS2_servico_1" value="" <?= $disable; ?>>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($servico)):
                                foreach ($servico as $serv):
                            ?>
                                    <option value="<?= $serv->id; ?>" data-valor="<?= $serv->valor; ?>"
                                        data-tempo="<?= $serv->tempo; ?>" data-medicao="<?= $serv->medicao; ?>"
                                        data-unidade="<?= $serv->medida; ?>" data-recorrencia="<?= $serv->id_recorrencia; ?>"
                                        data-diarecorrencia="<?= $serv->dia; ?>"
                                        data-datalegal="<?= !empty($serv->datalegal) ? calculaDataRecorrente($serv->datalegal, $serv->recor_datalegal) : ""; ?>">
                                        <?= $serv->nome; ?>
                                    </option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-group coluna10">
                        <label for="">Data Execução</label>
                        <input type="date" id="dataexec" name="OS2_dataexec_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="">Hora</label>
                        <input type="time" id="horaexec" name="OS2_horaexec_1" value="" <?= $disable; ?>>
                    </div>
                    <?php
                    if (ll_decode($_SESSION['mostraDataLegal']) == 'X'):
                    ?>
                        <div class="fcad-form-group coluna10">
                            <label for="">Data Legal</label>
                            <input type="date" id="datalegal" name="OS2_datalegal_1" value="" <?= $disable; ?>>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="fcad-form-group coluna05">
                        <button type="button" class="btn btn-danger deltarefa"><i class="fa fa-minus" <?= $disable; ?>></i></button>
                    </div>
                </div>
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna05">
                        <label for="qtd_servico">Qtde</label>
                        <input class="mask-number" type="text" id="qtd_servico" name="OS2_qtd_servico_1" value="1"
                            <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna02">
                        <span class="unidade-servico" name="OS2_und_servico_1"></span>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="vunit_servico">V.Unit.</label>
                        <input class="mask-money2" id="vunit_servico" name="OS2_vunit_servico_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna07 inputreadonly">
                        <label for="vtotal_servico">V.Total</label>
                        <input type="text" id="vtotal_servico" name="OS2_vtotal_servico_1" value="" readonly>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="tempo">Duração(min)</label>
                        <input type="text" id="tempo" name="OS2_tempo_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna10 medicaoOs2">
                        <label style="text-align: right;" for="">Medições</label>
                        <p class="medicao-os2-parcial"><span class="medicao-os2-totalfeito">0</span>/<span
                                class="medicao-os2-totalcontratado"></span></p>
                    </div>
                    <div class="fcad-form-group coluna05 medicaoOs2 medicao-desabilitado"
                        data-tooltip="Primeiro salve a OS!">
                        <button type="button" data-seq="1" class="btn btn-info btn-os2-medicao" id="medicao_1" disabled><i
                                class="fa fa-pen-ruler"></i></button>
                    </div>
                    <div class="fcad-form-group coluna10 recorrencia">
                        <label for="">Recorrência</label>
                        <select name="OS2_recorrencia_1" id="recorrencia-os2" data-loaded="false">
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($recorrencias)):
                                foreach ($recorrencias as $recorrencia):
                            ?>
                                    <option data-dia="<?= $recorrencia->dia ?>" value="<?= $recorrencia->id ?>">
                                        <?= $recorrencia->descricao; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-group coluna10 datafixa">
                        <label for="">Dia Recorrência</label>
                        <input type="text" id="datafixa" name="OS2_datafixa_1" value="">
                    </div>
                    <div class="fcad-form-group coluna05 direita item-modal medicao-desabilitado"
                        data-tooltip="Primeiro salve a OS!">
                        <label for="">Prod/Mat</label>
                        <button type="button" class="btn btn-info btn-os2-itensModal" disabled><i
                                class="fa fa-box"></i></button>
                    </div>
                    <?php
                    if (ll_decode($_SESSION['servicosComEquipamentos']) == 'X'):
                    ?>
                        <div class="fcad-form-group coluna05 item-modal medicao-desabilitado"
                            data-tooltip="Primeiro salve a OS!">
                            <label for="">Equip.</label>
                            <button type="button" class="btn btn-info btn-os2-itensModal" disabled><i
                                    class="fa fa-tractor"></i></button>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div style="margin: 0;" class="fcad-form-group coluna05 item-modal">
                        <label for="">Obs</label>
                        <button type="button" class="btn btn-info btn-os2-itensModal" data-bs-toggle="collapse"
                            data-bs-target="#obs-accordion-1" aria-expanded="false" aria-controls="obs-accordion-1"
                            <?= $disable; ?>><i class="fa fa-sticky-note"></i></button>
                    </div>
                </div>
                <div class="fcad-form-row">
                    <div style="padding:10px" class="fcad-form-group">
                        <div id="obs-accordion-1" class="accordion-collapse collapse">
                            <div class="fcad-form-row">
                                <div class="fcad-form-group coluna05"><label>Obs</label></div>
                                <div class="fcad-form-group">
                                    <textarea name="OS2_obs_1" class="" <?= $disable; ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        endif;
        ?>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group totalmat coluna30 <?= $user->tipo == 3 ? 'tphantom' : ''; ?>">
            <h5>Total Produtos/Materiais Tarefas: R$ <span id="sumMatOs2"></span></h5>
        </div>
        <div class="fcad-form-group totalmat coluna20 direita <?= $user->tipo == 3 ? 'tphantom' : ''; ?>">
            <h5>Total Serviços: R$ <span id="sumservico"></span></h5>
        </div>
        <div class="fcad-form-group coluna05 direita">
            <button title="NOVA TAREFA" type="button" class="btn btn-success novatarefa" <?= $disable; ?>><i
                    class="fa fa-plus"></i></button>
        </div>
    </div>
</div>

<?php
//* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
if ($user->tipo != 3):
?>
    <div id="div-os3" class="ordem1">
        <label>PRODUTOS/MATERIAIS</label>
        <div class="mat-accordion-item">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#container-linhas3"
                aria-expanded="true" aria-controls="container-linhas3">
                Produtos/Materiais da OS
            </button>
            <div id="container-linhas3" class="accordion-collapse collapse show">
                <?php
                if ($ordens != ""):
                    $os3 = (new Os3())->find(
                        "id_os1 = :id_os1 AND id_os2 IS NULL",
                        "id_os1={$ordens->id}",
                        "*",
                        false
                    )->fetch(true);
                    if ($os3 != null):
                        $seq = 1;
                        foreach ($os3 as $osmat):
                ?>
                            <div class="ordens-form linha-material" id="linha-material">
                                <div class="fcad-form-row">
                                    <input type="text" id="OS3_id" name="OS3_id_<?= $seq; ?>" value="<?= $osmat->id; ?>" hidden>
                                    <div class="fcad-form-group coluna05">
                                        <label class="transparent" for="">_</label>
                                        <input type="text" id="materialseq" name="" value="<?= $seq; ?>" disabled>
                                    </div>
                                    <div class="fcad-form-group coluna30">
                                        <label for="material">Produto/Material</label>
                                        <select class="selectMaterial" id="selectNovoMaterial" name="OS3_material_<?= $seq; ?>" value=""
                                            <?= $disable; ?>>
                                            <option value="">Selecione</option>
                                            <?php
                                            if (!empty($material)):
                                                foreach ($material as $mat):
                                                    $temp = "";
                                                    if ($mat->id == $osmat->id_materiais):
                                                        $temp = "selected";
                                                    endif;
                                            ?>
                                                    <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>"
                                                        data-vfloat="<?= $mat->valor; ?>" data-valor="<?= moedaBR($mat->valor); ?>" <?= $temp; ?>><?= $mat->descricao; ?></option>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="">Qtde</label>
                                        <input class="qtde_material num-decimal3" type="text" name="OS3_qtd_material_<?= $seq; ?>"
                                            value="<?= $osmat->qtde; ?>" <?= $disable; ?>>
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <span class="unidade-mat" name="OS3_und_material_<?= $seq; ?>"></span>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="">V.Unit.(R$)</label>
                                        <input class="vunit_material num-decimal2" name="OS3_valor_material_1"
                                            value="<?= moedaBR($osmat->vunit); ?>" <?= $disable; ?>>
                                    </div>
                                    <div class="fcad-form-group coluna10 inputreadonly">
                                        <label for="">V.Total(R$)</label>
                                        <input class="vtotal_material num-decimal2" name="OS3_vtotal_material_1"
                                            value="<?= moedaBR($osmat->vtotal); ?>" readonly>
                                    </div>
                                    <div class="fcad-form-group coluna05 divdelete" data-seq="<?= $seq; ?>">
                                        <button type="button" class="btn btn-danger deletemat" <?= $disable; ?>><i
                                                class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $seq++;
                        endforeach;
                    else:
                        ?>
                        <div class="ordens-form linha-material" id="linha-material">
                            <input type="text" id="OS3_id" name="OS3_id_1" value="" hidden>
                            <div class="fcad-form-row">
                                <div class="fcad-form-group coluna05">
                                    <label class="transparent" for="">_</label>
                                    <input type="text" id="materialseq" name="" value="1" disabled>
                                </div>
                                <div class="fcad-form-group coluna30">
                                    <label for="material">Produto/Material</label>
                                    <select class="selectMaterial" id="selectNovoMaterial" name="OS3_material_1" value=""
                                        <?= $disable; ?>>
                                        <option value="">Selecione</option>
                                        <?php
                                        if (!empty($material)):
                                            foreach ($material as $mat):
                                        ?>
                                                <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>"
                                                    data-vfloat="<?= $mat->valor; ?>" data-valor="<?= moedaBR($mat->valor); ?>">
                                                    <?= $mat->descricao; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="fcad-form-group coluna10">
                                    <label for="">Qtde</label>
                                    <input class="qtde_material num-decimal3" type="text" name="OS3_qtd_material_1" value=""
                                        <?= $disable; ?>>
                                </div>
                                <div class="fcad-form-group coluna05">
                                    <span class="unidade-mat" name="OS3_und_material_1"></span>
                                </div>
                                <div class="fcad-form-group coluna10">
                                    <label for="">V.Unit.(R$)</label>
                                    <input class="vunit_material num-decimal2" name="OS3_valor_material_1" value="" <?= $disable; ?>>
                                </div>
                                <div class="fcad-form-group coluna10 inputreadonly">
                                    <label for="">V.Total(R$)</label>
                                    <input class="vtotal_material num-decimal2" name="OS3_vtotal_material_1" value="" readonly>
                                </div>
                                <div class="fcad-form-group coluna05 divdelete" data-seq="1">
                                    <button type="button" class="btn btn-danger deletemat" <?= $disable; ?>><i
                                            class="fa fa-minus"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php
                    endif;
                else:
                    ?>
                    <div class="ordens-form linha-material" id="linha-material">
                        <input type="text" id="OS3_id" name="OS3_id_1" value="" hidden>
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna05">
                                <label class="transparent" for="">_</label>
                                <input type="text" id="materialseq" name="" value="1" disabled>
                            </div>
                            <div class="fcad-form-group coluna30">
                                <label for="material">Produto/Material</label>
                                <select class="selectMaterial" id="selectNovoMaterial" name="OS3_material_1" value=""
                                    <?= $disable; ?>>
                                    <option value="">Selecione</option>
                                    <?php
                                    if (!empty($material)):
                                        foreach ($material as $mat):
                                    ?>
                                            <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>"
                                                data-vfloat="<?= $mat->valor; ?>" data-valor="<?= moedaBR($mat->valor); ?>">
                                                <?= $mat->descricao; ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="fcad-form-group coluna10">
                                <label for="">Qtde</label>
                                <input type="text" class="qtde_material num-decimal3" name="OS3_qtd_material_1" value=""
                                    <?= $disable; ?>>
                            </div>
                            <div class="fcad-form-group coluna05">
                                <span class="unidade-mat" name="OS3_und_material_1"></span>
                            </div>
                            <div class="fcad-form-group coluna10">
                                <label for="">V.Unit.(R$)</label>
                                <input class="vunit_material num-decimal2" name="OS3_valor_material_1" value="" <?= $disable; ?>>
                            </div>
                            <div class="fcad-form-group coluna10 inputreadonly">
                                <label for="">V.Total(R$)</label>
                                <input class="vtotal_material num-decimal2" name="OS3_vtotal_material_1" value="" readonly>
                            </div>
                            <div class="fcad-form-group coluna05 divdelete" data-seq="1">
                                <button type="button" class="btn btn-danger deletemat" <?= $disable; ?>><i
                                        class="fa fa-minus"></i></button>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
                <div class="fcad-form-group coluna05 direita">
                    <button title="INCLUIR PRODUTO/MATERIAL" type="button" class="btn btn-success novomat" <?= $disable; ?>><i
                            class="fa fa-plus"></i></button>
                </div>
            </div>
        </div>

        <!-- ACCORDIONS MATERIAIS POR TAREFA-->

        <!-- <?php
                if ($ordens != ""):
                    $tarefas = (new Os2())->findByIdOs($ordens->id);
                    if (!empty($tarefas)):
                        foreach ($tarefas as $tarefa):
                ?>
                <div class="mat-accordion-item">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#container-accordion-os2os3-<?= $tarefa->id; ?>" aria-expanded="true" aria-controls="#container-accordion-os2os3-<?= $tarefa->id; ?>">
                        Produtos/Materiais da Tarefa #<?= $tarefa->id; ?>
                    </button>
                    <div id="container-accordion-os2os3-<?= $tarefa->id; ?>" class="accordion-collapse collapse">
                        <?php
                            $os3 = (new Os3())->find(
                                "id_os1 = :id_os1 AND id_os2 = :id_os2",
                                "id_os1={$ordens->id}&id_os2={$tarefa->id}",
                                "*",
                                false
                            )->fetch(true);
                            if ($os3 != null):
                                $seq = 1;
                                foreach ($os3 as $osmat):
                        ?>
                                <div class="ordens-form" id="linha-material-<?= $tarefa->id; ?>">
                                    <div class="fcad-form-row">
                                        <input type="text" id="OS3_id" name="OS3_id_<?= $tarefa->id; ?>_<?= $seq; ?>" value="<?= $osmat->id; ?>" hidden>
                                        <div class="fcad-form-group coluna05">
                                            <label class="transparent" for="">_</label>
                                            <input type="text" id="materialseq" name="" value="<?= $seq; ?>" disabled>
                                        </div>
                                        <div class="fcad-form-group coluna30">
                                            <label for="selectNovoMaterial">Material</label>
                                            <select class="selectMaterial" id="selectNovoMaterial" name="OS3_material_<?= $tarefa->id; ?>_<?= $seq; ?>" value="" <?= $disable; ?>>
                                                <option value="">Selecione</option>
                                                <?php
                                                if (!empty($material)):
                                                    foreach ($material as $mat):
                                                        $temp = "";
                                                        if ($mat->id == $osmat->id_materiais):
                                                            $temp = "selected";
                                                        endif;
                                                ?>
                                                        <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>" data-vfloat="<?= $mat->valor; ?>" data-valor="<?= moedaBR($mat->valor); ?>" <?= $temp; ?>><?= $mat->descricao; ?></option>
                                                <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </select>
                                        </div>
                                        <div class="fcad-form-group coluna10">
                                            <label for="qtd_material">Qtde</label>
                                            <input type="number" name="OS3_qtd_material_<?= $tarefa->id; ?>_<?= $seq; ?>" value="<?= $osmat->qtde; ?>" <?= $disable; ?>>
                                        </div>
                                        <div class="fcad-form-group coluna05">
                                            <span class="unidade-mat" name="OS3_und_material_<?= $tarefa->id; ?>_<?= $seq; ?>"></span>
                                        </div>
                                        <div class="fcad-form-group coluna10">
                                            <label for="valor_material">V.Unit.(R$)</label>
                                            <input class="mask-money2" name="OS3_valor_material_<?= $tarefa->id; ?>_<?= $seq; ?>" value="<?= moedaBR($osmat->vunit); ?>" <?= $disable; ?>>
                                        </div>
                                        <div class="fcad-form-group coluna10 inputreadonly">
                                            <label for="vtotal_material">V.Total(R$)</label>
                                            <input class="mask-money" name="OS3_vtotal_material_<?= $tarefa->id; ?>_<?= $seq; ?>" value="<?= moedaBR($osmat->vtotal); ?>" readonly>
                                        </div>
                                        <div class="fcad-form-group coluna05 divdelete" data-seq="<?= $seq; ?>">
                                            <button type="button" class="btn btn-danger deletemattarefa" <?= $disable; ?>><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php

                                    $seq++;
                                endforeach;
                            else:
                            ?>
                            <div class="ordens-form" id="linha-material-<?= $tarefa->id; ?>">
                                <input type="text" id="OS3_id" name="OS3_id_<?= $tarefa->id; ?>_1" value="" hidden>
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05">
                                        <label class="transparent" for="">_</label>
                                        <input type="text" id="materialseq" name="" value="1" disabled>
                                    </div>
                                    <div class="fcad-form-group coluna30">
                                        <label for="selectNovoMaterial">Material</label>
                                        <select class="selectMaterial" id="selectNovoMaterial" name="OS3_material_<?= $tarefa->id; ?>_1" value="" <?= $disable; ?>>
                                            <option value="">Selecione</option>
                                            <?php
                                            if (!empty($material)):
                                                foreach ($material as $mat):
                                            ?>
                                                    <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>" data-vfloat="<?= $mat->valor; ?>" data-valor="<?= moedaBR($mat->valor); ?>"><?= $mat->descricao; ?></option>
                                            <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="qtd_material">Qtde</label>
                                        <input type="number" name="OS3_qtd_material_<?= $tarefa->id; ?>_1" value="" <?= $disable; ?>>
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <span class="unidade-mat" name="OS3_und_material_<?= $tarefa->id; ?>_1"></span>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="valor_material">V.Unit.(R$)</label>
                                        <input class="mask-money2" name="OS3_valor_material_<?= $tarefa->id; ?>_1" value="" <?= $disable; ?>>
                                    </div>
                                    <div class="fcad-form-group coluna10 inputreadonly">
                                        <label for="vtotal_material">V.Total(R$)</label>
                                        <input class="mask-money
                                    " name="OS3_vtotal_material_<?= $tarefa->id; ?>_1" value="" readonly>
                                    </div>
                                    <div class="fcad-form-group coluna05 divdelete" data-seq="1">
                                        <button type="button" class="btn btn-danger deletemattarefa" <?= $disable; ?>><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php
                            endif;
                        ?>
                        <div class="fcad-form-group coluna05 direita">
                            <button title="INCLUIR PRODUTO/MATERIAL" type="button" class="btn btn-success novomattarefa" <?= $disable; ?>><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
    <?php
                        endforeach;
                    endif;
                endif;
    ?>
    -->
        <div class="fcad-form-row">
            <div class="fcad-form-group totalmat">
                <h5>Total Produtos/Materiais: R$ <span id="summaterial"></span></h5>
            </div>
        </div>
    </div>
<?php
endif;
if ($empresa->tarefasAditivas == 'X' && $user->tipo != 3):

    $this->insert("tcsistemas.os/ordens/os2aditivo", [
        "ordens" => $ordens,
        "operador" => $operador,
        "servico" => $servico,
        "material" => $material,
        "status" => $status,
        "cliente" => $cliente,
        "obras" => $obras,
        "recorrencias" => $recorrencias,
        "disable" => $disable,
        "user" => $user
    ]);
endif;
?>