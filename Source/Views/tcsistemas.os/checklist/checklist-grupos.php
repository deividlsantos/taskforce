<?php
$this->layout("_theme", $front);
?>
<div class="d-flex">
    <div class="tabela-responsive coluna50">
        <form id="form-chkgrupo" class="form-cadastros" data-delete="<?= url("checklist/excluirgrupo"); ?>" action="<?= url('checklist/salvargrupo'); ?>">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna50">
                    <label for="descricao">Novo Grupo</label>
                    <input type="text" id="descricao" name="descricao" value="">
                </div>
                <div class="fcad-form-group coluna10 justify-content-end">
                    <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                </div>
                <div class="fcad-form-group coluna15 direita justify-content-end">
                    <a href="<?= url("checklist") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
                </div>
            </div>
        </form>
        <table id="chkgrupo-list" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;">Grupo</th>
                    <th style="width:5%;">Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($grupos)):
                    foreach ($grupos as $g) :
                ?>
                        <tr>
                            <td data-id="<?= $g->id; ?>"><?= $g->descricao; ?></td>
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