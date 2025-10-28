<div class="modal fade" id="modalCabecalhoPdf" data-url="<?= url("ordens/pdf"); ?>" tabindex="-1" aria-labelledby="modalCabecalhoPdfLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCabecalhoPdfLabel">Gerar PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formCabecalhoPdf">
                    <div id="empresasOpcoes" style="width: 100%;">
                        <?php if (!empty($empresasDoGrupo)): ?>
                            <p>Selecione a empresa do cabeçalho do PDF:</p>
                            <?php foreach ($empresasDoGrupo as $empresa): ?>
                                <div class="form-check" style="width: 100%;">
                                    <input class="form-check-input"
                                        type="radio"
                                        name="empresa"
                                        id="empresa_<?php echo $empresa->id; ?>"
                                        value="<?php echo $empresa->id; ?>"
                                        <?php echo (isset($empresa_id) && $empresa->id == $empresa_id) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="empresa_<?php echo $empresa->id; ?>">
                                        <?php echo $empresa->razao; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                        <?php endif; ?>
                        <div>
                            <p>Mostrar no PDF:</p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="servicos" id="servicos" value="servicos">
                                <label class="form-check-label" for="servicos">
                                    Serviços
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="produtos" id="produtos" value="produtos">
                                <label class="form-check-label" for="produtos">
                                    Produtos/Materiais
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGerarPdf">Gerar PDF</button>
            </div>
        </div>
    </div>
</div>