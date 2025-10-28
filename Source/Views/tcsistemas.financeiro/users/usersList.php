<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("users/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar" hidden>
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarUsuario" name="filtrar" value="">
            </div>
        </div>
    </div>
    <div class="tabela-responsive">
        <table id="users-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th style="width:20%;">Nome</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($users != ""):
                    foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user->nome . " " . (($user->tipo == 2) ? "(OPERADOR)" : ""); ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("users/form/") . ll_encode($user->id); ?>"
                                    class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <a class="btn btn-secondary list-del" title="" href="#"
                                    data-post="<?= url("users/excluir"); ?>" data-action="delete"
                                    data-confirm="Tem certeza que deseja apagar esse registro?"
                                    data-id_user="<?= ll_encode($user->id); ?>"><i class="fa fa-trash"></i></a>
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

</html>