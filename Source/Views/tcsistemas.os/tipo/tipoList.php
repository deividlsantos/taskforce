<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="tabela-responsive">
        <div class="fcad-form-row">
            <a href="<?= url("tipo/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
            <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
        <div class="input-filtrar">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna10">
                    <label>Filtrar:</label>
                </div>
                <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                    <input type="text" id="filtrarTipo" name="filtrar" value="">
                </div>
            </div>
        </div>
        <table id="tipo-list" class="tab-list table table-striped table-hover bordered table-vendas">
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
                if ($tipo):
                    // Ordenar o array pelo campo descrição
                    usort($tipo, function ($a, $b) {
                        return strcasecmp($a->descricao, $b->descricao);
                    });
                    foreach ($tipo as $vlrTipo): ?>
                        <tr>
                            <td hidden><?= $vlrTipo->id; ?></td>
                            <td><?= mb_strimwidth($vlrTipo->descricao, 0, 150, '...'); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("tipo/form/") . ll_encode($vlrTipo->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del <?= $vlrTipo->padrao == 'S' ? 'disabled-link' : ""; ?>"
                                    title="" href="#"
                                    data-post="<?= url("tipo/excluir"); ?>"
                                    data-action="delete"
                                    data-confirm="Tem certeza que deseja deletar esse registro?"
                                    data-id_tipo="<?= ll_encode($vlrTipo->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="3">NENHUM TIPO REGISTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>