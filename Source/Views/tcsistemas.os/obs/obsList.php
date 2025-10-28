<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="tabela-responsive">
        <div class="fcad-form-row">
            <a href="<?= url("obs/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
            <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
        <div class="input-filtrar">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna10">
                    <label>Filtrar:</label>
                </div>
                <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                    <input type="text" id="filtrarObservacoes" name="filtrar" value="">
                </div>
            </div>
        </div>
        <table id="observacoes-list" class="tab-list table table-striped table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th hidden>Código</th>
                    <th style="width:30%;">Descrição</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($obs):
                    foreach ($obs as $vlrObs): ?>
                        <tr>
                            <td hidden><?= $vlrObs->id; ?></td>
                            <td><?= mb_strimwidth($vlrObs->descricao, 0, 150, '...'); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("obs/form/") . ll_encode($vlrObs->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#" data-post="<?= url("obs/excluir"); ?>"
                                    data-action="delete" data-confirm="Tem certeza que deseja deletar esse registro?"
                                    data-id_obs="<?= ll_encode($vlrObs->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="3">NENHUMA OBSERVAÇÃO REGISTRADA</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>