<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("plconta/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarPlconta" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="plconta-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Descricao</th>
                    <th>Tipo</th>
                    <th>Subtipo</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($plconta != ""):
                    foreach ($plconta as $conta): ?>
                        <tr>
                            <td><?= $conta->codigoconta; ?></td>
                            <td><?= mb_strimwidth($conta->descricao, 0, 90, '...'); ?></td>
                            <td><?= $conta->tipo == "D" ? "Despesa" : "Receita"; ?></td>
                            <td><?= $conta->subtipo; ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("plconta/form/") . ll_encode($conta->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("plconta/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja excluir esse registro?"
                                    data-id_plconta="<?= ll_encode($conta->id); ?>"><i class="fa fa-trash"></i></a>
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