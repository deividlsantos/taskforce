<form id="form-obralistmodal">
    <div class="modal modal-pag2" id="modalObraList" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                                    Selecionar <?= $label ?>
                                </h2>
                            </div>
                        </div>
                        <div class="fcad-form-row input-obralist-filtrar">
                            <div class="fcad-form-group coluna10">
                                <label>Filtrar:</label>
                            </div>
                            <div style="height: 5px;" class="fcad-form-group coluna50">
                                <input type="text" id="filtrarObraListModal" name="filtrar" value="">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($obras): ?>
                        <table class="table table-hover table-striped" id="obra_lst">
                            <tbody>
                                <!-- <?php foreach ($obras as $vlr): ?>
                                    <tr data-ent="<?= $vlr->id_ent_cli; ?>">
                                        <td style="width: 10%;">
                                            <button type="button" data-id="<?= $vlr->id ?>" class="btn btn-info btn-pick-obra" id="btn-obra-<?= $vlr->id ?>"><i class="fa-solid fa-check"></i></button>
                                        </td>
                                        <td style="width: 90%;">
                                            <label for="obs-<?= $vlr->id ?>"><?= $vlr->nome ?></label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?> -->
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning" role="alert">
                            Nenhuma <?= mb_strtolower($label) ?> cadastrada.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" id="incluir-obra" hidden>Incluir</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>