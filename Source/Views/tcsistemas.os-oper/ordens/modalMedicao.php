<!-- Modal -->
<div class="modal fade modal-opermedicao" id="opermedicaoModal" data-url="<?= url("medicao") ?>" tabindex="-1" role="dialog" aria-labelledby="opermedicaoModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Medição</h2>
                <h2 class="modal-title2" id="tarefaMedicao">Tarefa da medicao</h2>
                <button type="button" id="close-opermedicao" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body" id="opermedicaoBody">
                <form class="form-cadastros">
                    <div class="medicao-body" id="insertMedicaoForm">
                        <div class="fcad-form-group coluna20">
                            <label style="margin-left: 3%;" for="medicaoOperador">Operador</label>
                            <select type="text" id="medicaoOperador" name="medicaoOperador" value="" required>
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
                                <label style="margin-left: 3%;" for="medicaoEquipamento">Equipamento</label>
                                <select type="text" id="medicaoEquipamento" name="medicaoEquipamento" value="" required>
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
                        <div class="quantity-input medicao-row">
                            <div class="fcad-form-group medicao-data">
                                <label class="opmat-text" for="medicao-obs">Obs</label>
                                <textarea class="form-control" id="medicao-obs" name="medicao-obs"></textarea>
                            </div>
                        </div>
                        <div class="quantity-input medicao-row">
                            <div class="fcad-form-group medicao-data">
                                <label class="opmat-text" for="medicao-datai">Início</label>
                                <input type="datetime-local" class="form-control" id="medicao-datai" name="medicao-datai" required>
                            </div>
                            <div class="fcad-form-group medicao-data">
                                <label class="opmat-text" for="medicao-dataf">Fim</label>
                                <input type="datetime-local" class="form-control" id="medicao-dataf" name="medicao-dataf" required>
                            </div>
                        </div>
                        <div class="quantity-input medicao-qtde medicao-row">
                            <p style="font-weight: bold;" class="qtde-medicao opmat-text" for="medicao">Qtde.</p>
                            <input type="number" class="form-control" id="quantityInputMedicao" value="1" min="1">
                            <button type="button" data-idOs2="" class="btnSaveOs3" id="saveMedicao"><i class="fa-solid fa-check"></i></button>
                        </div>
                    </div>

                    <div class="medicao-header">
                        <div>
                            <p class="opmedicao-htit" for="medicao">Total:</p>
                            <p class="opmedicao-hvalue"><span id="medicaoTotal"></span><span class="opmedicao-unidade">m²</span></p>
                        </div>
                        <div>
                            <p class="opmedicao-htit" id="qtd-medicao">Medido:</p>
                            <p class="opmedicao-hvalue"><span id="medicaoRealizado"></span><span class="opmedicao-unidade">m²</span></p>
                        </div>
                        <div>
                            <p class="opmedicao-htit" id="qtd-medicao">A Medir:</p>
                            <p class="opmedicao-hvalue"><span id="medicaoPendente"></span><span class="opmedicao-unidade">m²</span></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="opermedicao-container" style="display: none;">
                <div class="opermedicao-cabecalho">
                    <span class='cabecalhomedicao-periodo'>Período</span>
                    <span class='cabecalhomedicao-quantidade'>Qtde</span>
                </div>
            </div>
        </div>
    </div>
</div>