<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("obras/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarObras" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="obras-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th style="width:10%;">Controle</th>
                    <th style="width:20%;">Nome</th>
                    <th style="width:20%;">Cliente</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($obras != ""):
                    foreach ($obras as $vlr): ?>
                        <tr>
                            <td><?= $vlr->controle; ?></td>
                            <td><?= mb_strimwidth($vlr->nome, 0, 90, '...'); ?></td>
                            <td>
                                <?= mb_strimwidth($vlr->cliente, 0, 90, '...'); ?>
                            </td>
                            <td class="coluna-acoes">
                                <a href="<?= url("obras/form/") . ll_encode($vlr->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("obras/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_obras="<?= ll_encode($vlr->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="4">NENHUM REGISTRO ENCONTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>