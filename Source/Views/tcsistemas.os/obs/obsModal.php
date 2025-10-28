<form id="form-obsmodal" action="<?= url("obs/salvar") ?>">
    <div class="modal modal-pag2" id="modalObs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                        Selecione as observações
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($obs): ?>
                        <table class="table table-hover table-striped table-obschecklist">
                            <tbody>
                                <?php foreach ($obs as $vlr): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="observacoes[]" value="<?= $vlr->id ?>" id="obs-<?= $vlr->id ?>">
                                        </td>
                                        <td>
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->descricao ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhuma observação cadastrada.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" id="incluir-obs">Incluir</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>