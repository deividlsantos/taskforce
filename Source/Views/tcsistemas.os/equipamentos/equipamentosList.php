<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row form-buttons">
        <a href="<?= url("equipamentos/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("equipamentos/gestaoeqp") ?>" class="btn btn-success">Gestão</a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarEquip" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="equip-list" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;" data-label="Nome">Equipamento</th>
                    <th>Descrição</th>
                    <th>Placa/Tag</th>
                    <th>Status</th>
                    <th>Inventário</th>
                    <th style="width:5%;" data-label="Ver/Edt">Ver/Edt</th>
                    <th style="width:5%;" data-label="Excluir">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($equipamento)):
                    foreach ($equipamento as $eqp) :
                ?>
                        <tr>
                            <td><?= $eqp->equipamento; ?></td>
                            <td><?= $eqp->descricao; ?></td>
                            <td><?= $eqp->equipamento == 'VEÍCULO' ? $eqp->placa : $eqp->tag; ?></td>
                            <td><?= $eqp->status; ?></td>
                            <td><?= $eqp->inventario == '1' ? "SIM" : "NÃO"; ?></td>
                            <td><a href="<?= url("equipamentos/form/" . ll_encode($eqp->id)); ?>" class="btn btn-secondary list-edt"><i class="fa fa-pencil"></i></a></td>
                            <td><a class="btn btn-secondary list-del"
                                    title="APAGAR REGITRO" href="#"
                                    data-post="<?= url("equipamentos/excluir"); ?>"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_equipamento="<?= ll_encode($eqp->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
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