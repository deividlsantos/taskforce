<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("materiais/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarMateriais" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="materiais-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;">Descrição</th>
                    <th>Unidade</th>
                    <th>Custo</th>
                    <th>Valor</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($materiais != ""):
                    foreach ($materiais as $vlr): ?>
                        <tr>
                            <td><?= mb_strimwidth($vlr->descricao, 0, 90, '...'); ?></td>
                            <td><?= $vlr->unidade; ?></td>
                            <td><?= moedaBR($vlr->custo); ?></td>
                            <td><?= moedaBR($vlr->valor); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("materiais/form/") . ll_encode($vlr->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("materiais/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_materiais="<?= ll_encode($vlr->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="6">NENHUM REGISTRO ENCONTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>