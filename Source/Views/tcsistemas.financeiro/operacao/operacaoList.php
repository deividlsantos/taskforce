<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("operacao/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarOperacao" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="operacao-list" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;">Nome</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($operacao != ""):
                    foreach ($operacao as $oper): ?>
                        <tr>
                            <td><?= mb_strimwidth($oper->descricao, 0, 100, '...'); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("operacao/form/") . ll_encode($oper->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("operacao/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_oper="<?= ll_encode($oper->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="3">NENHUM REGISTRO ENCONTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>