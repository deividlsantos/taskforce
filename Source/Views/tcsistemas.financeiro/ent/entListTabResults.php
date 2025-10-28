<?php
$msgDel = "excluir";
$iconeDel = "fa fa-trash";
$labelDel = "Excluir";
if ($tipo == 3) {
    $msgDel = "inativar";
    $iconeDel = "fa fa-ban";

    $labelDel = $ativar ? "Reativar" : "Inativar";
}
?>
<div class="tabela-responsive">
    <table id="<?= $idTable; ?>" class="tab-list table table-hover table-vendas">
        <thead>
            <tr>
                <th style="width:20%;" data-label="Nome">Nome</th>
                <th data-label="CPF/CNPJ">CPF/CNPJ</th>
                <th data-label="Fone">Fone</th>
                <th data-label="Celular">Celular</th>
                <th style="width:5%;" data-label="Ver/Edt">Ver/Edt</th>
                <th style="width:5%;" data-label="Excluir"><?= $labelDel; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($ent):
                foreach ($ent as $vlrEnt): ?>
                    <tr>
                        <!-- <?php
                                if ($tipo == 4):
                                    foreach ($filha as $vlrFilha):
                                        if ($vlrEnt->id == $vlrFilha->id_ent): ?>
                                        <td><?= $vlrFilha->banco; ?></td>
                                <?php
                                        endif;
                                    endforeach; ?>
                            <?php else: ?>
                                <td><?= $vlrEnt->nome; ?></td>
                            <?php endif; ?> -->
                        <td><?= $tipo == 3 ? $vlrEnt->nome . " " . $vlrEnt->fantasia : mb_strimwidth($vlrEnt->nome, 0, 100, '...'); ?></td>
                        <td><?= $vlrEnt->cpfcnpj; ?></td>
                        <td><?= $vlrEnt->fone1; ?></td>
                        <td><?= $vlrEnt->fone2; ?></td>
                        <td class="coluna-acoes">
                            <a href="<?= url("ent/form/") . ll_encode($vlrEnt->id); ?>"
                                class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                        </td>
                        <td class="coluna-acoes">
                            <?php
                            if ($tipo == 3 && $ativar):
                            ?>
                                <a class="btn btn-secondary list-reativar" title="" href="#"
                                    data-post="<?= url("ent/reativar"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja reativar esse registro?"
                                    data-id_ent="<?= ll_encode($vlrEnt->id); ?>"><i class="fa fa-circle-check"></i></a>
                            <?php
                            else:
                            ?>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("ent/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja <?= $msgDel; ?> esse registro?"
                                    data-id_ent="<?= ll_encode($vlrEnt->id); ?>"><i class="<?= $iconeDel ?>"></i></a>
                            <?php
                            endif;
                            ?>
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