<form id="form-chkitem" method="post" action="<?= url('checklist/salvaritem'); ?>" data-delete="<?= url("checklist/excluiritem"); ?>">
    <div class="modal modal-pag2" id="modalChkItem" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoChkItem">
                                    <span id="chkitem-sectit">Novo</span> Item Checklist
                                </h2>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input id="chkitem-id" name="id" value="" hidden>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna30">
                            <label for="chkitem-grupo">Grupo:</label>
                            <select class="form-control" id="chkitem-grupo" name="id_chkgrupo" required>
                                <option value="">SELECIONE</option>
                                <?php if (!empty($grupos)) : ?>
                                    <?php foreach ($grupos as $g) : ?>
                                        <option value="<?= $g->id; ?>"><?= $g->descricao; ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="fcad-form-group">
                            <label for="descricao">Descrição:</label>
                            <input type="text" id="chkitem-descricao" name="descricao" value="" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" id="">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>