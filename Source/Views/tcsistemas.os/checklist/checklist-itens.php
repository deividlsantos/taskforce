<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <button class="btn btn-success" id="novo-chkitem"><i class="fa fa-plus"></i></button>
        <a href="<?= url("checklist") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarChkItens" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="chkitens-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;">Grupo</th>
                    <th>Descrição</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($itens)):
                    foreach ($itens as $vlr): ?>
                        <tr>
                            <td><?= mb_strimwidth($vlr->grupo, 0, 30, '...'); ?></td>
                            <td><?= mb_strimwidth($vlr->descricao, 0, 90, '...'); ?></td>
                            <td class="coluna-acoes">
                                <button data-id="<?= ll_encode($vlr->id); ?>" data-url="<?= url("checklist/retornaitem"); ?>" class="btn btn-secondary list-edt chkitem-edit"><i class="fa fa-pen"></i></button>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("materiais/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_item="<?= ll_encode($vlr->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="100%">NENHUM REGISTRO ENCONTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<section>
    <?php
    $this->insert("tcsistemas.os/checklist/chkitemCadModal", [
        "grupos" => $grupos
    ]);
    ?>
</section>


</html>