<div class="ordem1">
    <input type="text" id="id_os1" name="id_os1" value="" hidden>
    <div class="ordens-form">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label for="OS1_id">Ordem nº</label>
                <input type="text" id="OS1_id" value="" disabled>
            </div>
            <div class="fcad-form-group coluna20">
                <label for="status">Status</label>
                <select id="status" name="OS1_status" value="">
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
            <div class="fcad-form-group coluna40">
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
            <div class="fcad-form-group direita inputreadonly coluna15">
                <label for="vtotal">Valor Total</label>
                <input class="mask-money" type="text" id="vtotal" name="OS1_vtotal" value="<?= ($ordens != "") ? moedaBR($ordens->vtotal) : ''; ?>">
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
    <label>TAREFAS</label>
    <div id="container-linhas2">
        <div class="ordens-form original linhatarefa" id="linha-tarefa">
            <input type="text" id="OS2_id" name="OS2_id_1" value="" hidden>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna05">
                    <label class="transparent" for="">_</label>
                    <input type="text" id="tarefaseq" name="" value="1" disabled>
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
                                <option value="<?= $serv->id; ?>" data-valor="<?= $serv->valor; ?>" data-tempo="<?= $serv->tempo; ?>"><?= $serv->nome; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
                <input type="text" id="vtotal_servico" name="OS2_vtotal_servico_1" value="" hidden>
                <input type="text" id="vunit_servico" name="OS2_vunit_servico_1" value="" hidden>
                <div class="fcad-form-group coluna05">
                    <label for="qtd_servico">Qtde</label>
                    <input class="mask-number" type="text" id="qtd_servico" name="OS2_qtd_servico_1" value="1">
                </div>
                <div class="fcad-form-group coluna10">
                    <label for="tempo">Duração(min)</label>
                    <input type="text" id="tempo" name="OS2_tempo_1" value="">
                </div>
                <div class="fcad-form-group coluna10">
                    <label for="dataexec">Data Execução</label>
                    <input type="date" id="dataexec" name="OS2_dataexec_1" value="" required>
                </div>
                <div class="fcad-form-group coluna10">
                    <label for="horaexec">Hora</label>
                    <input type="time" id="horaexec" name="OS2_horaexec_1" value="">
                </div>
                <div class="fcad-form-group coluna05">
                    <button type="button" class="btn btn-danger deltarefa"><i class="fa fa-minus"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group totalmat coluna20 direita">
            <h5>Total Serviços: R$ <span id="sumservico"></span></h5>
        </div>
        <div class="fcad-form-group coluna05 direita">
            <button title="NOVA TAREFA" type="button" class="btn btn-success novatarefa"><i class="fa fa-plus"></i></button>
        </div>
    </div>
</div>

<div id="div-os3" class="ordem1">
    <label>PRODUTOS/MATERIAIS</label>
    <div id="container-linhas3">
        <div class="ordens-form original" id="linha-material">
            <input type="text" id="OS3_id" name="OS3_id_1" hidden>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna05">
                    <label class="transparent" for="">_</label>
                    <input type="text" id="materialseq" name="" value="1" disabled>
                </div>
                <div class="fcad-form-group coluna30">
                    <label for="material">Material</label>
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
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group totalmat coluna20 direita">
            <h5>Total Materiais: R$ <span id="summaterial"></span></h5>
        </div>
        <div class="fcad-form-group coluna05 direita">
            <button title="INCLUIR PRODUTO/MATERIAL" type="button" class="btn btn-success novomat"><i class="fa fa-plus"></i></button>
        </div>
    </div>
</div>