<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("turno/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="tabela-responsive">
        <table id="turno-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>                    
                    <th>Nome do Turno</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($turnos):
                    foreach ($turnos as $turno): ?>
                        <tr>                            
                            <td><?= $turno->nome; ?></td>
                            <td><?= substr($turno->hora_ini, 0, 5); ?></td>
                            <td><?= substr($turno->hora_fim, 0, 5); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("turno/form/") . ll_encode($turno->id); ?>"
                                    class="btn btn-secondary list-edt <?= $turno->id == 13 ? 'disabled-link' : ''; ?>"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del <?= $turno->id == 13 ? 'disabled-link' : ''; ?>" title="" href="#" data-post="<?= url("turno/excluir"); ?>"
                                    data-action="delete" data-confirm="Tem certeza que deseja deletar esse turno?"
                                    data-id_turno="<?= ll_encode($turno->id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td>NENHUM TURNO REGISTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</html>