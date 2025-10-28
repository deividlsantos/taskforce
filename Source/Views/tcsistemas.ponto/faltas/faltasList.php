<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="tabela-responsive" style="width:50%;">
        <div class="fcad-form-row">
            <a href="<?= url("faltas/obsForm") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
            <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
        <table class="tab-list table table-striped table-hover bordered table-vendas">
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
                if ($faltas):
                    foreach ($faltas as $vlrFalta): ?>
                        <tr>
                            <td hidden><?= $vlrFalta->id_faltas; ?></td>
                            <td><?= $vlrFalta->descricao; ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("faltas/obsForm/") . ll_encode($vlrFalta->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#" data-post="<?= url("faltas/apagar"); ?>"
                                    data-action="delete" data-confirm="Tem certeza que deseja deletar esse registro?"
                                    data-id_faltas="<?= ll_encode($vlrFalta->id); ?>"><i class="fa fa-trash"></i></a>
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