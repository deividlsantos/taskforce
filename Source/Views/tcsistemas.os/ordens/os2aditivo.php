<div id="div-os2-aditivo" class="ordem1 <?= $user->tipo == 3 ? 'os2-all-disabled' : ''; ?>">
    <div class="fcad-form-row">
        <label>TAREFAS ADITIVAS</label>
        <label class="direita">STATUS:</label>
        <?php

        use Source\Models\Os2;
        use Source\Models\Os2_1;
        use Source\Models\Os2_2;
        use Source\Models\Os3;
        use Source\Models\Status;

        if (!empty($status)):
            foreach ($status as $st):
        ?>
                <label><i style="color: <?= $st->cor ?>" class="fa-solid fa-circle"></i><?= $st->descricao ?></label>
        <?php
            endforeach;
        endif;
        ?>
        <button type="button" class="btn btn-info" id="ordenar-add"><i class="fa-solid fa-arrow-down-short-wide"></i></button>
    </div>
    <div id="container-linhasaditivo">
        <?php
        if ($ordens != ""):
            $os2 = (new Os2())->find(
                "id_os1 = :id_os1 AND aditivo = :aditivo",
                "id_os1={$ordens->id}&aditivo=S",
                "*",
                false
            )->fetch(true);
        endif;

        $temTipo3 = true;
        if (!empty($os2) && $user->tipo == 3):
            $temTipo3 = false;
            foreach ($os2 as $tarefa):
                if ($tarefa->id_colaborador == $user->id_ent) {
                    $temTipo3 = true;
                    break;
                }
            endforeach;
        endif;


        if (!empty($os2) && $temTipo3):
            $seq = 1;
            $medicoes = new Os2_1();
            $materiaisPorTarefa = new Os3();
            $equipamentosDaTarefa = new Os2_2();
            foreach ($os2 as $tarefa):
                if ($user->tipo == 3 && $tarefa->id_colaborador != $user->id_ent) continue;
                $medicoesLista = $medicoes->findByOs2($tarefa->id);

                $materiaisLista = $materiaisPorTarefa->find(
                    "id_os2 = :id_os2",
                    "id_os2={$tarefa->id}",
                    "*",
                    false
                )->fetch(true);

                $totalMateriaisTarefa = 0;
                if (!empty($materiaisLista)) {
                    foreach ($materiaisLista as $material) {
                        $totalMateriaisTarefa += $material->vtotal;
                    }
                }

                $equipamentosLista = "";
                $equipamentosLista = $equipamentosDaTarefa->findByOs2($tarefa->id);

                $travaSelectServico = "";
                $corBotaoMedicoes = "";
                $corBotaoMateriais = "";
                $corBotaoEquipamentos = "";
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

                <div class="ordens-form linhaaditivo <?= $seq == 1 ? 'original' : ""; ?>" id="linha-aditivo">
                    <input type="text" id="add_id" name="add_id_<?= $seq; ?>" value="<?= $tarefa->id; ?>" hidden>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna07">
                            <div class="fcad-form-row">
                                <div class="fcad-form-group">
                                    <input type="text" id="addseq" name="" value="<?= $seq; ?>" disabled>
                                </div>
                                <div class="fcad-form-group">
                                    <button type="button" data-tooltip="ALTERAR STATUS" data-status="<?= $tarefa->status; ?>" data-tarefa="<?= $tarefa->id; ?>" class="btn btn-info btn-os2-att-status" <?= $disable; ?> <?= $disable != 'disabled' && $ordens->id_status == 8 ? "disabled" : ""; ?>><i class="fa fa-sliders"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="fcad-form-group colunatarefanumero">
                            <label class="" for="">Tarefa</label>
                            <input type="text" data-status="<?= $tarefa->status; ?>" style="border: 3px solid <?= $tarefa->cor; ?>" class="tarefanumero0" id="tarefanumero" name="add_numero_1" value="<?= "#" . $tarefa->id; ?>" readonly>
                        </div>
                        <div class="fcad-form-group coluna20 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Operador</label>
                            <select class="selectAddOperador <?= $tarefa->status != 'A' ? 'select-readonly' : ''; ?>" id="selectAddOperador" name="add_operador_<?= $seq; ?>" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($operador)):
                                    foreach ($operador as $oper) :
                                        if ($user->tipo == 3 && $oper->id != $user->id_ent) continue;
                                ?>
                                        <option value="<?= $oper->id; ?>" <?= $oper->id == $tarefa->id_colaborador ? 'selected' : ''; ?>>
                                            <?= "{$oper->nome} {$oper->fantasia}"; ?>
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
                            <select class="selectAddServico <?= $travaSelectServico; ?>" data-url="<?= url("recorrencias/verifica"); ?>" id="selectAddServico" name="add_servico_<?= $seq; ?>" value="" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($servico)):
                                    foreach ($servico as $serv) :
                                        $temp = "";
                                        if ($serv->id == $tarefa->id_servico):
                                            $temp = "selected";
                                        endif;
                                ?>
                                        <option
                                            value="<?= $serv->id; ?>"
                                            data-valor="<?= $serv->valor; ?>"
                                            data-tempo="<?= $serv->tempo; ?>"
                                            data-medicao="<?= $serv->medicao; ?>"
                                            data-unidade="<?= $serv->medida; ?>"
                                            data-recorrencia="<?= $serv->id_recorrencia; ?>"
                                            data-diarecorrencia="<?= $serv->dia ?>"
                                            data-datalegal="<?= !empty($serv->datalegal) ? date_fmt(calculaDataRecorrente($serv->datalegal, $serv->recor_datalegal), "d/m/Y") : ""; ?>"
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
                            <input type="hidden" name="add_dataexec_original_<?= $seq; ?>" value="<?= strtotime($tarefa->dataexec) + $tarefa->horaexec; ?>">
                            <input type="date" id="dataexec" name="add_dataexec_<?= $seq; ?>" value="<?= date_fmt($tarefa->dataexec, "Y-m-d"); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna07">
                            <label for="">Hora</label>
                            <input type="time" id="horaexec" name="add_horaexec_<?= $seq; ?>" value="<?= $horaexec; ?>" <?= $disable; ?>>
                        </div>
                        <?php
                        if (ll_decode($_SESSION['mostraDataLegal']) == 'X'):
                        ?>
                            <div class="fcad-form-group coluna10">
                                <label for="">Data Legal</label>
                                <input type="date" id="datalegal" name="add_datalegal_<?= $seq; ?>" value="<?= !empty($tarefa->datalegal) ? date_fmt($tarefa->datalegal, "Y-m-d") : ""; ?>" <?= $disable; ?>>
                            </div>
                        <?php
                        endif;
                        ?>
                        <div class="fcad-form-group coluna05 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <button type="button" class="btn btn-danger deltarefa-add" <?= $disable; ?> <?= $tarefa->status != "A" || !empty($travaSelectServico) ? "hidden" : ""; ?>><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna05 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Qtde</label>
                            <input type="text" id="qtd_aditivo" name="add_qtd_servico_<?= $seq; ?>" value="<?= fmt_numeros($tarefa->qtde); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna02 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <span class="unidade-servico" name="add_und_servico_<?= $seq; ?>"></span>
                        </div>
                        <div class="fcad-form-group coluna07 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">V.Unit.</label>
                            <input type="text" id="vunit_servico" name="add_vunit_servico_<?= $seq; ?>" value="<?= moedaBR($tarefa->vunit); ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna07 inputreadonly">
                            <label for="">V.Total</label>
                            <input type="text" id="vtotal_servico" name="add_vtotal_servico_<?= $seq; ?>" value="<?= moedaBR($tarefa->vtotal); ?>" readonly>
                        </div>
                        <div class="fcad-form-group coluna07 <?= $user->tipo == 3 ? 'os2-item-disabled' : ''; ?>">
                            <label for="">Duração(min)</label>
                            <input type="text" id="tempo" name="add_tempo_<?= $seq; ?>" value="<?= $tarefa->tempo / 60; ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna10 medicaoOs2">
                            <?php
                            if (!empty($tarefa->medicoes)) :
                                $totalMedido = 0;
                                foreach ($tarefa->medicoes as $medicao) :
                                    $totalMedido += $medicao->qtde;
                                endforeach;
                            else:
                                $totalMedido = 0;
                            endif;
                            ?>
                            <label style="text-align: right;" for="">Medições</label>
                            <p class="medicao-os2-parcial"><span class="medicao-os2-totalfeito"><?= $totalMedido ? $totalMedido : 0; ?></span>/<span class="medicao-os2-totalcontratado"><?= $tarefa->qtde; ?></span></p>
                        </div>
                        <div class="fcad-form-group coluna05 medicaoOs2">
                            <button type="button" style="<?= $corBotaoMedicoes ?>" class="btn btn-info btn-os2-medicao" data-url="<?= url("medicao/atualiza") ?>" data-tarefamedicao="<?= $tarefa->id; ?>" id="medicao_1" <?= $disable; ?>><i class="fa fa-pen-ruler"></i></button>
                        </div>
                        <div class="fcad-form-group coluna10 recorrencia">
                            <label for="">Recorrência</label>
                            <select name="add_recorrencia_<?= $seq; ?>" id="recorrencia-add" data-loaded="false" <?= $disable; ?>>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($recorrencias)):
                                    foreach ($recorrencias as $recorrencia) :
                                        $temp = "";
                                        if ($recorrencia->id == $tarefa->id_recorrencia):
                                            $temp = "selected";
                                        endif;
                                ?>
                                        <option data-dia="<?= $recorrencia->dia ?>" value="<?= $recorrencia->id ?>" <?= $temp; ?>><?= $recorrencia->descricao; ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna10 datafixa">
                            <label for="">Dia Recorrência</label>
                            <input type="text" id="datafixa-add" name="add_datafixa_<?= $seq; ?>" value="<?= $tarefa->dia_recorrencia; ?>" <?= $disable; ?>>
                        </div>
                        <div class="fcad-form-group coluna05 direita item-modal">
                            <label for="">Prod/Mat</label>
                            <button type="button" style="<?= $corBotaoMateriais ?>" class="btn btn-info btn-os2-itensModal btn-os2mat" data-url="<?= url("ordens/verificamateriais") ?>" data-tarefa="<?= $tarefa->id; ?>" data-total="<?= $totalMateriaisTarefa; ?>" <?= $disable; ?>><i class="fa fa-box"></i></button>
                        </div>
                        <div class="fcad-form-group coluna05 item-modal">
                            <label for="">Equip.</label>
                            <button type="button" style="<?= $corBotaoEquipamentos ?>" class="btn btn-info btn-os2-itensModal btn-os2eqp" data-url="<?= url("ordens/verificaequipamentos") ?>" data-tarefa="<?= $tarefa->id; ?>" <?= $disable; ?>><i class="fa fa-tractor"></i></button>
                        </div>
                        <div style="margin: 0;" class="fcad-form-group coluna05 item-modal">
                            <label for="">Obs</label>
                            <button type="button" style="<?= $corBotaoObs; ?>" class="btn btn-info btn-os2-itensModal" data-bs-toggle="collapse" data-bs-target="#obs-accordion-add-<?= $seq; ?>" aria-expanded="false" aria-controls="obs-add-accordion-<?= $seq; ?>" <?= $disable; ?>><i class="fa fa-sticky-note"></i></button>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div style="padding:10px" class="fcad-form-group">
                            <div id="obs-accordion-add-<?= $seq; ?>" class="accordion-collapse collapse">
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05"><label>Obs</label></div>
                                    <div class="fcad-form-group">
                                        <textarea class="" name="add_obs_<?= $seq; ?>" <?= $disable; ?>><?= isset($tarefa->obs) ? $tarefa->obs : ''; ?></textarea>
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
            <div class="ordens-form linhaaditivo original" id="linha-aditivo">
                <input type="text" id="add_id" name="add_id_1" value="" hidden>
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna05">
                        <label class="transparent" for="">_</label>
                        <input type="text" id="addseq" name="" value="1" disabled>
                    </div>
                    <div class="fcad-form-group coluna20">
                        <label for="">Operador<span><button type="button" class="btn btn-info btn-opr-search" <?= $disable; ?>><i class="fa fa-search"></i></button></span></label>
                        <select class="selectAddOperador" id="selectAddOperador" name="add_operador_1" value="" <?= $disable; ?>>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($operador)):
                                foreach ($operador as $oper) :
                                    if ($user->tipo == 3 && $oper->id != $user->id_ent) continue;
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
                        <select class="selectAddServico" data-url="<?= url("recorrencias/verifica"); ?>" id="selectAddServico" name="add_servico_1" value="" <?= $disable; ?>>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($servico)):
                                foreach ($servico as $serv) :
                            ?>
                                    <option
                                        value="<?= $serv->id; ?>"
                                        data-valor="<?= $serv->valor; ?>"
                                        data-tempo="<?= $serv->tempo; ?>"
                                        data-medicao="<?= $serv->medicao; ?>"
                                        data-unidade="<?= $serv->medida; ?>"
                                        data-recorrencia="<?= $serv->id_recorrencia; ?>"
                                        data-diarecorrencia="<?= $serv->dia; ?>"
                                        data-datalegal="<?= !empty($serv->datalegal) ? date_fmt(calculaDataRecorrente($serv->datalegal, $serv->recor_datalegal), "d/m/Y") : ""; ?>">
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
                        <input type="date" id="dataexec" name="add_dataexec_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="">Hora</label>
                        <input type="time" id="horaexec" name="add_horaexec_1" value="" <?= $disable; ?>>
                    </div>
                    <?php
                    if (ll_decode($_SESSION['mostraDataLegal']) == 'X'):
                    ?>
                        <div class="fcad-form-group coluna10">
                            <label for="">Data Legal</label>
                            <input type="date" id="datalegal" name="add_datalegal_1" value="" <?= $disable; ?>>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="fcad-form-group coluna05">
                        <button type="button" class="btn btn-danger deltarefa-add" <?= $disable; ?>><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna05">
                        <label for="qtd_aditivo">Qtde</label>
                        <input class="mask-number" type="text" id="qtd_aditivo" name="add_qtd_servico_1" value="1" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna02">
                        <span class="unidade-servico" name="add_und_servico_1"></span>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="vunit_servico">V.Unit.</label>
                        <input type="text" id="vunit_servico" name="add_vunit_servico_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna07 inputreadonly">
                        <label for="vtotal_servico">V.Total</label>
                        <input type="text" id="vtotal_servico" name="add_vtotal_servico_1" value="" readonly>
                    </div>
                    <div class="fcad-form-group coluna07">
                        <label for="tempo">Duração(min)</label>
                        <input type="text" id="tempo" name="add_tempo_1" value="" <?= $disable; ?>>
                    </div>
                    <div class="fcad-form-group coluna10 medicaoOs2">
                        <label style="text-align: right;" for="">Medições</label>
                        <p class="medicao-os2-parcial"><span class="medicao-os2-totalfeito">0</span>/<span class="medicao-os2-totalcontratado"></span></p>
                    </div>
                    <div class="fcad-form-group coluna05 medicaoOs2 medicao-desabilitado" data-tooltip="Primeiro salve a OS!">
                        <button type="button" data-seq="1" class="btn btn-info btn-os2-medicao" id="medicao_1" disabled><i class="fa fa-pen-ruler"></i></button>
                    </div>
                    <div class="fcad-form-group coluna10 recorrencia">
                        <label for="">Recorrência</label>
                        <select name="add_recorrencia_1" id="recorrencia-add2" data-loaded="false">
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($recorrencias)):
                                foreach ($recorrencias as $recorrencia) :
                            ?>
                                    <option data-dia="<?= $recorrencia->dia ?>" value="<?= $recorrencia->id ?>"><?= $recorrencia->descricao; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="fcad-form-group coluna10 datafixa">
                        <label for="">Dia Recorrência</label>
                        <input type="text" id="datafixa-add2" name="add_datafixa_1" value="">
                    </div>
                    <div class="fcad-form-group coluna05 direita item-modal medicao-desabilitado" data-tooltip="Primeiro salve a OS!">
                        <label for="">Prod/Mat</label>
                        <button type="button" class="btn btn-info btn-os2-itensModal" disabled><i class="fa fa-box"></i></button>
                    </div>
                    <div class="fcad-form-group coluna10 item-modal medicao-desabilitado" data-tooltip="Primeiro salve a OS!">
                        <label for="">Equip.</label>
                        <button type="button" class="btn btn-info btn-os2-itensModal" disabled><i class="fa fa-tractor"></i></button>
                    </div>
                    <div style="margin: 0;" class="fcad-form-group coluna05 item-modal">
                        <label for="">Obs</label>
                        <button type="button" class="btn btn-info btn-os2-itensModal" data-bs-toggle="collapse" data-bs-target="#obs-accordion-add-1" aria-expanded="false" aria-controls="obs-accordion-add-1" <?= $disable; ?>><i class="fa fa-sticky-note"></i></button>
                    </div>
                </div>
                <div class="fcad-form-row">
                    <div style="padding:10px" class="fcad-form-group">
                        <div id="obs-accordion-add-1" class="accordion-collapse collapse">
                            <div class="fcad-form-row">
                                <div class="fcad-form-group coluna05"><label>Obs</label></div>
                                <div class="fcad-form-group">
                                    <textarea name="add_obs_1" class="" <?= $disable; ?>></textarea>
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
            <h5>Total Produtos/Materiais Tarefas Aditivas: R$ <span id="sumMatOs2Add"></span></h5>
        </div>
        <div class="fcad-form-group totalmat coluna20 direita <?= $user->tipo == 3 ? 'tphantom' : ''; ?>">
            <h5>Total Tarefas: R$ <span id="sumAddservico"></span></h5>
        </div>
        <div class="fcad-form-group coluna05 direita">
            <button title="NOVA TAREFA" type="button" class="btn btn-success novatarefa-add" <?= $disable; ?>><i class="fa fa-plus"></i></button>
        </div>
    </div>
</div>