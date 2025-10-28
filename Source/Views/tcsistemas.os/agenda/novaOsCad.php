<div class="modal modal-pag modalos" id="modalNovaOs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <p class="modal-title fs-2 titulo-pai" id="title-edit">

                </p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body calendariomodal">
                <input type="text" name="modalos" value="osnova" hidden>

                <div id="div-os1" class="ordem1">
                    <input type="text" id="id_os1" name="id_os1" value="<?= ($ordens != "") ? ll_encode($ordens->id) : ''; ?>" hidden>
                    <div class="ordens-form">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna05">
                                <label for="OS1_id">Ordem nº</label>
                                <input type="text" id="OS1_id" value="<?= ($ordens != "") ? $ordens->id : ''; ?>" disabled>
                            </div>
                            <div class="fcad-form-group coluna10">
                                <label for="OS1_controle">Nº Controle</label>
                                <input type="text" id="OS1_controle" name="OS1_controle" value="<?= $ordens != "" && !empty($ordens->controle) ? $ordens->controle : ''; ?>">
                            </div>
                            <div class="fcad-form-group coluna20">
                                <label for="status">Status</label>
                                <select
                                    id="status"
                                    class="os1-status-modal"
                                    name="OS1_status"
                                    value=""
                                    data-url="<?= url("ordens/cancelar"); ?>">
                                    <?php
                                    if (!empty($status)):
                                        foreach ($status as $st) :
                                            $temp = "";
                                            if ($ordens != "" && $ordens->id_status == $st->id):
                                                $temp = "selected";
                                            endif;
                                    ?>
                                            <option value="<?= $st->id; ?>" <?= $temp; ?>><?= $st->descricao; ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="fcad-form-group coluna30">
                                <label for="cliente-os">Cliente</label>
                                <select id="cliente-os" name="OS1_cliente" value="" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    if (!empty($cliente)):
                                        foreach ($cliente as $cli) :
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
                            <div class="fcad-form-group coluna05">
                                <button type="button" class="btn btn-info newreg" id="novocli"><i class="fa fa-plus"></i></button>
                            </div>
                            <div class="fcad-form-group">
                                <label for="abre-obra">Obra</label>
                                <input class="check-obras" type="checkbox" id="abre-obra" <?= $ordens != "" && !empty($ordens->id_obras) ? "checked" : "" ?>>
                            </div>
                            <div class="fcad-form-group direita inputreadonly coluna15">
                                <label for="vtotal">Valor Total</label>
                                <input class="mask-money" type="text" id="vtotal" name="OS1_vtotal" value="<?= ($ordens != "") ? moedaBR($ordens->vtotal) : ''; ?>">
                            </div>
                        </div>
                        <div class="fcad-form-row" id="obra-container" style="display: none;">
                            <div class="fcad-form-group">
                                <label for="obra">Obra</label>
                                <select id="obra" name="OS1_obra">
                                    <option value="">Selecione</option>
                                    <?php
                                    if (!empty($obras)):
                                        foreach ($obras as $obra) :
                                            $temp = "";
                                            if ($ordens != "" && $ordens->id_obras == $obra->id):
                                                $temp = "selected";
                                            endif;
                                    ?>
                                            <option data-ent="<?= $obra->id_ent_cli ?>" value="<?= $obra->id; ?>" <?= $temp; ?>><?= $obra->nome . " - " . $obra->endereco; ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="fcad-form-group coluna05">
                                <button type="button" class="btn btn-info newreg" id="pickobra"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="fcad-form-row">
                            <div class="fcad-form-group">
                                <label for="obs">Obs</label>
                                <textarea type="text" id="obs" name="OS1_obs"><?= ($ordens != "") ? $ordens->obs : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="div-os2" class="ordem1">
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
                        <button type="button" class="btn btn-info" id="ordenar"><i class="fa-solid fa-arrow-down-short-wide"></i></button>
                    </div>
                    <div id="container-linhas2">
                        <div class="ordens-form ltm original linhatarefa" id="linha-tarefa">
                            <input type="text" id="OS2_id" name="OS2_id_1" value="" hidden>
                            <div class="fcad-form-row">
                                <div class="fcad-form-group coluna05" style="margin-right: -10px !important;">
                                    <label class="transparent" for="">_</label>
                                    <input type="text" id="tarefaseq" name="" value="1" disabled>
                                </div>
                                <div class="fcad-form-group colunatarefanumero">
                                    <label class="" for="">Tarefa</label>
                                    <input data-status="0" class="tarefanumero" type="text" id="tarefanumero" name="OS2_numero_1" value="" readonly>
                                </div>
                                <div class="fcad-form-group coluna20">
                                    <label for="operador">Operador</label>
                                    <select type="text" class="selectOperador" id="selectNovoOperador" name="OS2_operador_1" value="" required>
                                        <option value="">Selecione</option>
                                        <?php
                                        if (!empty($operador)):
                                            foreach ($operador as $oper) :
                                        ?>
                                                <option value="<?= $oper->id; ?>"><?= $oper->nome; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="fcad-form-group">
                                    <label for="servico">Serviço</label>
                                    <select type="text" class="selectServico" id="selectNovoServico" name="OS2_servico_1" value="" required>
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
                                                    data-unidade="<?= $serv->medida; ?>">
                                                    <?= $serv->nome; ?>
                                                </option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="fcad-form-group coluna10">
                                    <label for="dataexec">Data Execução</label>
                                    <input type="hidden" name="OS2_dataexec_original_1" value="">
                                    <input type="date" id="dataexec" name="OS2_dataexec_1" value="" required>
                                </div>
                                <div class="fcad-form-group coluna10">
                                    <label for="horaexec">Hora</label>
                                    <input type="time" id="horaexec" name="OS2_horaexec_1" value="">
                                </div>
                                <div class="fcad-form-group coluna05">
                                    <button type="button" class="btn btn-danger deltarefa"><i class="fa fa-minus"></i></button>
                                </div>
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05">
                                        <label class="phantom-margin" for="">_</label>
                                        <input class="phantom-margin" type="text" disabled>
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <label for="qtd_servico">Qtde</label>
                                        <input class="mask-number" type="text" id="qtd_servico" name="OS2_qtd_servico_1" value="1">
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <span class="unidade-servico" name="OS2_und_servico_1"></span>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="vunit_servico">V.Unit.</label>
                                        <input type="text" id="vunit_servico" name="OS2_vunit_servico_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna10 inputreadonly">
                                        <label for="vtotal_servico">V.Total</label>
                                        <input type="text" id="vtotal_servico" name="OS2_vtotal_servico_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="tempo">Duração(min)</label>
                                        <input type="text" id="tempo" name="OS2_tempo_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna10 medicaoOs2">
                                        <label style="text-align: right;" for="">Medições</label>
                                        <p class="medicao-os2-parcial"><span class="medicao-os2-totalfeito">0</span>/<span class="medicao-os2-totalcontratado"></span></p>
                                    </div>
                                    <div class="fcad-form-group coluna05 medicaoOs2 divbtnmedicao medicao-desabilitado" data-tooltip="Primeiro salve a OS!">
                                        <button type="button" class="btn btn-info btn-os2-medicao" data-url="<?= url("medicao/atualiza") ?>" id="medicao_1" disabled><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group totalmat coluna20 direita">
                            <h5>Total Serviços: R$ <span id="sumservico"></span></h5>
                        </div>
                        <div class="fcad-form-group coluna05 direita">
                            <button id="tarefanovamodal" title="NOVA TAREFA" type="button" class="btn btn-success novatarefa ntm"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>

                <div id="div-os3" class="ordem1">
                    <label>PRODUTOS/MATERIAIS</label>
                    <div class="mat-accordion-item">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#container-linhas3" aria-expanded="true" aria-controls="container-linhas3">
                            Produtos/Materiais da OS
                        </button>
                        <div id="container-linhas3" class="accordion-collapse collapse show">
                            <div class="ordens-form lmm original" id="linha-material">
                                <input type="text" id="OS3_id" name="OS3_id_1" value="" hidden>
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05">
                                        <label class="transparent" for="">_</label>
                                        <input type="text" id="materialseq" name="" value="1" disabled>
                                    </div>
                                    <div class="fcad-form-group coluna30">
                                        <label for="material">Produto/Material</label>
                                        <select type="text" class="selectMaterial" id="selectNovoMaterial" name="OS3_material_1" value="">
                                            <option value="">Selecione</option>
                                            <?php
                                            if (!empty($material)):
                                                foreach ($material as $mat) :
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
                                        <input type="number" id="qtd_material" name="OS3_qtd_material_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <span class="unidade-mat" name="OS3_und_material_1"></span>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="valor_material">V.Unit.(R$)</label>
                                        <input class="mask-money" id="valor_material" name="OS3_valor_material_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna10 inputreadonly">
                                        <label for="vtotal_material">V.Total(R$)</label>
                                        <input class="mask-money" id="vtotal_material" name="OS3_vtotal_material_1" value="" readonly>
                                    </div>
                                    <div class="fcad-form-group coluna05 divdelete" data-seq="1">
                                        <button type="button" class="btn btn-danger deletemat"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="fcad-form-group coluna05 direita">
                                <button id="matnovomodal" title="INCLUIR PRODUTO/MATERIAL" type="button" class="btn btn-success novomat nmm"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="mat-accordion-item os2-model">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#container-accordion-os2os3-os3" aria-expanded="true" aria-controls="container-accordion-os2os3-os3" hidden>
                            Produtos/Materiais da Tarefa #
                        </button>
                        <div id="container-accordion-os2os3-" class="accordion-collapse collapse">
                            <div class="ordens-form lmm original" id="linha-material-">
                                <input type="text" id="OS3_id" name="OS3_id_#_1" hidden>
                                <div class="fcad-form-row">
                                    <div class="fcad-form-group coluna05">
                                        <label class="transparent" for="">_</label>
                                        <input type="text" id="materialseq" name="" value="1" disabled>
                                    </div>
                                    <div class="fcad-form-group coluna30">
                                        <label for="selectNovoMaterial">Produto/Material</label>
                                        <select type="text" class="selectMaterial" id="selectNovoMaterial" name="OS3_material_#_1" value="">
                                            <option value="">Selecione</option>
                                            <?php
                                            if (!empty($material)):
                                                foreach ($material as $mat) :
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
                                        <input type="number" id="qtd_material" name="OS3_qtd_material_#_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna05">
                                        <span class="unidade-mat" name="OS3_und_material_#_1"></span>
                                    </div>
                                    <div class="fcad-form-group coluna10">
                                        <label for="valor_material">V.Unit.(R$)</label>
                                        <input class="mask-money
                                    " id="valor_material" name="OS3_valor_material_#_1" value="">
                                    </div>
                                    <div class="fcad-form-group coluna10 inputreadonly">
                                        <label for="vtotal_material">V.Total(R$)</label>
                                        <input class="mask-money
                                    " id="vtotal_material" name="OS3_vtotal_material_#_1" value="" readonly>
                                    </div>
                                    <div class="fcad-form-group coluna05 divdelete" data-seq="1">
                                        <button type="button" class="btn btn-danger deletemattarefa"><i class="fa fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="fcad-form-group coluna05 direita">
                                <button id="matnovomodaltarefa" title="INCLUIR PRODUTO/MATERIAL" type="button" class="btn btn-success novomattarefa nmm"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group totalmat coluna20 direita">
                            <h5>Total Produtos/Materiais: R$ <span id="summaterial"></span></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" id="novaos-submit" class="btn btn-primary">Gravar</button>
            </div>
        </div>
    </div>
</div>
<section>
    <?php
    $this->insert("tcsistemas.os/ordens/ordensModalMedicaoOs", []);
    ?>
</section>