<?php

use Source\Models\Emp1;

$this->layout("_theme", $front);
?>
<div class="container-fl-review">
    <div class="fcad-form-row">
        <a href="<?= url("emp2/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i> Cad. Empresa</a>
        <a href="<?= url("emp1") ?>" class="btn btn-success"><i class="fa fa-plus"></i> Cad. Grupo</a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>

    <div class="fcad-form-row">
        <div class="fcad-form-group">
            <div class="input-filtrar">
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna10">
                        <label for="grupo">Grupo:</label>
                    </div>
                    <div class="fcad-form-group coluna50">
                        <select id="grupo" name="grupo">
                            <option value="">Selecione um grupo</option>
                            <?php if (!empty($grupos)) : ?>
                                <?php foreach ($grupos as $grupo) : ?>
                                    <option value="<?= $grupo->id; ?>"><?= $grupo->descricao; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="fcad-form-group">
            <div class="input-filtrar">
                <div class="fcad-form-row">
                    <div class="fcad-form-group coluna10 direita">
                        <label>Filtrar:</label>
                    </div>
                    <div class="fcad-form-group coluna30">
                        <input type="text" id="filtrarEmp2" name="filtrar" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tabela-responsive">
        <table id="emp2-list" class="tab-list table table-hover bordered table-vendas">
            <thead>
                <tr>
                    <th style="width:5%;" hidden>ID Grupo</th>
                    <th style="width:20%;">Grupo</th>
                    <th style="width:5%;">ID</th>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>E-mail</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <th style="width:5%;">Inativar</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($empresas)) : ?>
                    <?php foreach ($empresas as $emp) :
                        if ($emp->id == '1') continue;
                    ?>
                        <tr class="<?= $emp->plano == 'vencido' ? 'vermelho' : ''; ?>" data-id_emp1="<?= $emp->id_emp1; ?>">
                            <td hidden><?= $emp->id_emp1; ?></td>
                            <td><?= (new Emp1())->findById($emp->id_emp1)->descricao; ?></td>
                            <td><?= $emp->id; ?></td>
                            <td><?= mb_strimwidth($emp->razao, 0, 30, '...'); ?></td>
                            <td><?= $emp->cnpj; ?></td>
                            <td><?= $emp->email; ?></td>
                            <td class="coluna-acoes">
                                <a href="<?= url("emp2/form/") . $emp->id; ?>" class="btn btn-secondary list-edt"><i class="fa fa-pen"></i></a>
                            </td>
                            <td>
                                <?php if ($emp->plano != 'vencido') : ?>
                                    <a class="btn btn-secondary list-del" href="#" data-post="<?= url("emp2/excluir"); ?>" data-action="delete" data-confirm="Tem certeza?" data-id_emp2="<?= $emp->id; ?>"><i class="fa fa-ban"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8">NENHUM REGISTRO ENCONTRADO</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        $("#grupo").on("change", function() {
            const grupoSelecionado = $(this).val();
            $("#emp2-list tbody tr").each(function() {
                const grupoDaLinha = $(this).data("id_emp1");
                if (!grupoSelecionado || grupoSelecionado === grupoDaLinha.toString()) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    </script>
</div>