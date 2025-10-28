<form method="" action="">
    <div id="cdLocalFerramenta" data-url="<?= url("equipamentos/salvar_local"); ?>" data-delete="<?= url("equipamentos/excluir_local"); ?>" class="modal modal-pag2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-2 titulo-pai">Cadastro Local</h3>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" type="button"></button>
                </div>
                <div class="modal-body" style="border: solid 1px #ccc; width: 100%;">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna50">
                            <label for="descLocal">Descrição</label>
                            <input type="text" name="descLocal" id="descLocal">
                        </div>
                        <div class="fcad-form-group">
                            <label for="stEqp">Status</label>
                            <select name="stEqp" id="stEqp">
                                <option value="">Selecione</option>
                                <option value="1">Estoque</option>
                                <option value="2">Entrada</option>
                                <option value="3">Inativo</option>
                                <option value="4">Alocados</option>
                                <option value="5">Manutenção</option>
                            </select>
                        </div>
                        <div class="fcad-form-group">
                            <button type="button" id="addLocal" class="btn btn-success newreg" style="margin-top: 25%;"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="table-container">
                        <table id="eqpLocal-list" class="tab-list table table-hover table-vendas">
                            <thead>
                                <tr>
                                    <th style="width:20%;">Grupo</th>
                                    <th style="width:20%;">Status</th>
                                    <th style="width:5%;">Edt</th>
                                    <th style="width:5%;">Excluir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($local)):
                                    foreach ($local as $g) :
                                ?>
                                        <tr>
                                            <td data-id="<?= $g->id; ?>"><?= $g->descricao; ?></td>
                                            <td><?= $g->status; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-secondary list-edt edit-chkgrupo"><i class="fa fa-pen"></i></button>
                                                <button type="button" data-id="<?= ll_encode($g->id); ?>" data-url="<?= url("checklist/salvargrupo"); ?>" class="btn btn-success confirm-edit-chkgrupo" hidden><i class="fa fa-check"></i></button>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-secondary list-del chkgrupo-delete" data-id="<?= ll_encode($g->id); ?>"><i class="fa fa-trash"></i></button>
                                                <button type="button" class="btn btn-danger cancel-edit-chkgrupo" hidden><i class="fa fa-xmark"></i></button>
                                            </td>
                                        </tr>
                                    <?php
                                    endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="100%">NENHUM REGISTRO ENCONTRADO</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>