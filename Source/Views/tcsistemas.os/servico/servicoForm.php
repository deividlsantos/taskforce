<input type="text" id="id_servico" name="id_servico"
    value="<?= ($servico != "") ? ll_encode($servico->id) : ''; ?>" hidden>
<div class="servico-form">
    <div class="fcad-from-row" style="display: flex; justify-content: flex-end; align-items: center;">
        <?php
        if ($empresa->servicosComMedicoes == 'X'):
        ?>
            <div style="margin-right: 10px;">
                <label><input type="checkbox" id="active-medicao" name="medicao" value="1" <?= ($servico != "" && $servico->medicao == 1) ? "checked" : ''; ?>> Medição</label>
            </div>
        <?php
        endif;
        ?>
        <div class="">
            <label><input type="checkbox" id="active-recorrente" name="recorrente" value="1" <?= ($servico != "" && $servico->recorrente == 1) ? "checked" : ''; ?>> Recorrente</label>
        </div>
    </div>
    <div class="fcad-form-row">
        <div id="" class="fcad-form-group">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= ($servico != "") ? $servico->nome : ''; ?>">
        </div>
        <div id="" class="fcad-form-group coluna10">
            <label for="tempo">Tempo(min):</label>
            <input type="text" id="tempo" name="tempo"
                value="<?= ($servico != "") ? ($servico->tempo / 60) : ''; ?>">
        </div>
        <div class="fcad-form-group coluna10" id="medida-container">
            <label for="medida">UN:</label>
            <input type="text" id="medida" name="medida"
                value="<?= ($servico != "") ? $servico->medida : ''; ?>">
        </div>
        <div class="fcad-form-group coluna20">
            <label for="id_plconta">Grupo de Receita</label>
            <select id="id_plconta" name="id_plconta">
                <option value="">Selecione</option>
                <?php
                if (!empty($plconta)):
                    foreach ($plconta as $grupo) :
                        $temp = "";
                        if ($servico != "" && $servico->id_plconta == $grupo->id):
                            $temp = "selected";
                        endif;
                ?>
                        <option value="<?= $grupo->id; ?>" <?= $temp; ?>><?= $grupo->descricao; ?></option>
                <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna20">
            <label for="valor">Valor:</label>
            <input class="mask-money" type="text" id="valor" name="valor"
                value="<?= ($servico != "") ? moedaBR($servico->valor) : ''; ?>">
        </div>
        <div class="fcad-form-group coluna20">
            <label for="custo">Custo:</label>
            <input class="mask-money" type="text" id="custo" name="custo"
                value="<?= ($servico != "") ? moedaBR($servico->custo) : ''; ?>">
        </div>
        <?php
        if ($empresa->mostraDataLegal == 'X'):
        ?>
            <div class="fcad-form-group coluna15">
                <label for="datalegal">Data Legal(DL):<i class="fa fa-info-circle ferias-info"
                        data-tooltip="Dia base para a data legal. Formato:<br>'dd/mm'(ex: 15/10)."></i></label><span>

                </span>
                <input type="text" id="datalegal" name="datalegal" class="mask-daymonth"
                    value="<?= ($servico != "") ? date_fmt($servico->datalegal, "d/m") : ''; ?>">
            </div>
            <div class="fcad-form-group coluna15">
                <label for="recor_datalegal">Frequência DL:</label>
                <select name="recor_datalegal" id="recor_datalegal">
                    <option value="livre" <?= !empty($servico) && $servico->recor_datalegal == 'livre' ? 'selected' : ""; ?>>Livre</option>
                    <option value="mensal" <?= !empty($servico) && $servico->recor_datalegal == 'mensal' ? 'selected' : ""; ?>>Mensal</option>
                    <option value="trimestral" <?= !empty($servico) && $servico->recor_datalegal == 'trimestral' ? 'selected' : ""; ?>>Trimestral</option>
                    <option value="anual" <?= !empty($servico) && $servico->recor_datalegal == 'anual' ? 'selected' : ""; ?>>Anual</option>
                </select>
            </div>
        <?php
        endif;
        ?>
        <div class="fcad-form-group coluna10" id="recorrencia-container">
            <label for="recorrencia">Recorrência:</label>
            <select name="recorrencia" id="recorrencia">
                <option value="">Selecione</option>
                <?php
                foreach ($recorrencias as $recorrencia):
                ?>
                    <option value="<?= $recorrencia->id ?>" <?= ($servico != "" && $servico->id_recorrencia == $recorrencia->id) ? "selected" : ''; ?>>
                        <?= $recorrencia->descricao; ?>
                    </option>
                <?php
                endforeach;
                ?>
            </select>
        </div>
        <div class="fcad-form-group coluna05" id="datafixa-container">
            <label for="datafixa">Dia</label>
            <input type="text" id="datafixa" name="datafixa"
                value="<?= ($servico != "") ? $servico->dia : ''; ?>" placeholder="0-30" maxlength="2"
                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
        </div>
        <div class="fcad-form-group coluna15" id="intervalo-container">
            <label for="intervalo">Intervalo(dias):</label>
            <input type="text" id="intervalo" name="intervalo"
                value="<?= ($servico != "") ? $servico->intervalo : ''; ?>">
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <label for="obs">Observação:</label>
            <textarea style="height: 33px;" type="text" id="obs"
                name="obs"><?= ($servico != "") ? $servico->obs : ''; ?></textarea>
        </div>
    </div>
</div>