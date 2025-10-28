<div id="parFinConfModal" class="fin-config-modal">
    <div class="fin-config-modal-content">
        <div class="fin-config-modal-header">
            Setar Padrões:
        </div>
        <div class="fin-config-modal-body">
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="finconfig-plconta">Plano de Conta:</label>
                    <select id="finconfig-plconta" name="finconfig-plconta" class="">
                        <option value="">Selecione...</option>
                        <?php
                        if (!empty($plconta)):
                            foreach ($plconta as $pl) :
                        ?>
                                <option value="<?= $pl->id; ?>" <?= $emp2->plconta_padrao == $pl->id ? 'selected' : ''; ?>><?= (!empty($pl->codigoconta) ? $pl->codigoconta . ' - ' : "") . $pl->descricao; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="finconfig-operacao">Operação:</label>
                    <select id="finconfig-operacao" name="finconfig-operacao" class="">
                        <option value="">Selecione...</option>
                        <?php
                        if (!empty($operacoes)):
                            foreach ($operacoes as $op) :
                        ?>
                                <option value="<?= $op->id; ?>" <?= $emp2->oper_padrao == $op->id ? 'selected' : ''; ?>><?= $op->descricao; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>