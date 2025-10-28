<!-- Modal de Edição -->
<div class="modal fade editmedicao-modal" id="editMedicaoModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="editForm">
                    <div class="form-group">
                        <label class="editarItem">Editar Medição: <span id="editItemName"></span></label>
                    </div>
                    <div class="form-group" hidden>
                        <label for="editItemId">ID do Item</label>
                        <input type="text" class="form-control" id="editItemMedicaoId" readonly>
                        <input type="text" class="form-control" id="editOs2Medicao" readonly>
                    </div>
                    <div class="fcad-form-group coluna20">
                        <label style="margin-left: 3%;" class="opmat-text" for="medicaoOperador">Operador</label>
                        <select id="medicaoOperador-edit" name="medicaoOperador" value="" required>
                            <option value="">Selecione</option>
                            <?php
                            if (!empty($operador)):
                                foreach ($operador as $oper) :
                            ?>
                                    <option value="<?= $oper->id_ent; ?>"><?= $oper->nome; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <?php
                    if ($empresa->servicosComEquipamentos == 'X') :
                    ?>
                        <div class="fcad-form-group coluna20">
                            <label style="margin-left: 3%;" class="opmat-text" for="medicaoEquipamento">Equipamento</label>
                            <select id="medicaoEquipamento-edit" name="medicaoEquipamento" value="" required>
                                <option value="">Selecione</option>
                                <?php
                                if (!empty($equipamentos)):
                                    foreach ($equipamentos as $eqp) :
                                ?>
                                        <option value="<?= $eqp->id; ?>"><?= $eqp->descricao; ?></option>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    <?php
                    endif;
                    ?>
                    <div class="quantity-input">
                        <div class="fcad-form-group medicao-data">
                            <label class="opmat-text" for="medicao-obs">Obs</label>
                            <textarea class="form-control" id="medicao-obs-edit" name="medicao-obs"></textarea>
                        </div>
                    </div>
                    <div class="quantity-input">
                        <div class="fcad-form-group medicao-data">
                            <label class="opmat-text" for="medicao-datai">Início</label>
                            <input type="datetime-local" class="form-control" id="medicao-datai-edit" name="medicao-datai" required>
                        </div>
                        <div class="fcad-form-group medicao-data">
                            <label class="opmat-text" for="medicao-dataf">Fim</label>
                            <input type="datetime-local" class="form-control" id="medicao-dataf-edit" name="medicao-dataf" required>
                        </div>
                    </div>
                    <div class="quantity-input medicao-qtde">
                        <p class="qtde-medicao opmat-text" for="medicao">Qtde.</p>
                        <input type="number" class="form-control" id="quantityInputMedicao2" value="1" min="1">
                        <button type="button" data-idOs2="" class="btnSaveMedicaoEdit btnSaveOs3" id="saveMedicaoEdit"><i class="fa-solid fa-check"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>