<div id="form-equipamentosos2modal" data-url="<?= url("ordens/equipamentos") ?>" data-verificachk="<?= url("ordens/verificachecklist"); ?>" data-exibechkmodal="<?= url("ordens/checklist"); ?>">
    <div class="modal modal-pag2 equipamentos-os2-modal" id="modalEquipamentosOs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <h2 class="modal-title fs-2 titulo-pai" id="title-equipamentosOs">
                                Equipamentos Tarefa #<span id="tarefa-titulo-equipamentos"></span>
                                <p class="titulo-tarefa-modal" id="nomeservico"></p>
                            </h2>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="modal-form">
                        <table class="table table-hover tab-list os2modal-list" id="equipOs2-list">
                            <thead>
                                <tr>
                                    <th hidden>id</th>
                                    <th style="width: 40%;">Descrição</th>
                                    <th style="width: 20%; text-align: left;">Qtde</th>
                                    <th style="width: 5%;"></th>
                                    <th style="width: 5%;"></th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </section>

                    <table class="tab-add-medicao" id="medicao-list2">
                        <thead>
                            <tr>
                                <th style="text-align: center;" colspan="3">
                                    Novo Equipamento
                                </th>
                            </tr>
                            <tr>
                                <th hidden>id</th>
                                <th style="width: 50%;">Descrição</th>
                                <th style="width: 30%;">Qtde</th>
                                <th>Grava</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="input-row linha-os2equip">
                                <td hidden>
                                    <input type="text" name="tarefaId" id="tarefa_id_equipamento">
                                </td>
                                <td>
                                    <div class="fcad-form-row">
                                        <div class="coluna90">
                                            <select type="text" name="id_equipamento" id="id_os2_equipamento" class="form-control id_os2_equipamento">
                                                <option value="">Selecione</option>
                                                <?php
                                                if (!empty($equipamentos)) :
                                                    foreach ($equipamentos as $equip) :
                                                        if ($ordens->id_cli == $equip->id_cli) :
                                                ?>
                                                            <option value="<?= ll_encode($equip->id); ?>"><?= $equip->descricao; ?></option>
                                                <?php
                                                        endif;
                                                    endforeach;
                                                endif;
                                                ?>
                                            </select>
                                        </div>
                                        <div class="fcad-form-group coluna05">
                                            <span class="und_os2mat"></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="qtde" id="qtde_os2_equipamento" class="form-control qtde_os2_equipamento only-int" placeholder="Qtde">
                                </td>
                                <td>
                                    <button type="button" id="btn-os2equip-add" class="btn list-add"><i class="fa fa-check"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>