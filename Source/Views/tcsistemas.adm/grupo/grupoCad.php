<?php

$this->layout("_theme", $front);

?>

<div class="telas-body">


    <div style="margin-top: 10px;" class="fcad-form-row">
        <div class="fcad-form-group">
            <a href="<?= url("emp2") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
    </div>

    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna10">
                    <label>Filtrar:</label>
                </div>
                <div class="fcad-form-group">
                    <input type="text" id="filtrarEmp1" name="filtrar" value="">
                </div>
            </div>
        </div>
        <div class="fcad-form-group esquerda">
            <button data-url="<?= url("emp1/salvar") ?>" type="button" id="new-group" class="btn btn-success newreg"><i class="fa fa-plus"></i></button>
        </div>
    </div>

    <div class="tabela-responsive">
        <table id="emp1-list" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th style="width:10%;">ID</th>
                    <th>Nome</th>
                    <th style="width:5%;">Editar</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grupos)) : ?>
                    <?php foreach ($grupos as $g) : ?>
                        <tr data-id="<?= $g->id; ?>">
                            <td><?= $g->id; ?></td>
                            <td><?= mb_strimwidth($g->descricao, 0, 40, '...'); ?></td>
                            <td class="coluna-acoes">
                                <button class="btn btn-secondary list-edt emp1-edit">
                                    <i class="fa fa-pen"></i>
                                </button>
                                <button data-id="<?= ll_encode($g->id); ?>" data-url="<?= url("emp1/salvar") ?>" class="btn btn-secondary list-add emp1-confirm-edit" hidden>
                                    <i class="fa fa-check"></i>
                                </button>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del emp1-delete" href="#"
                                    data-post="<?= url("emp1/excluir"); ?>"
                                    data-action="delete"
                                    data-confirm="Tem certeza que deseja excluir este grupo?"
                                    data-id="<?= $g->id; ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <button class="btn btn-secondary list-del emp1-cancel-edit" hidden>
                                    <i class="fa fa-xmark"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">Nenhum grupo cadastrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>

</script>