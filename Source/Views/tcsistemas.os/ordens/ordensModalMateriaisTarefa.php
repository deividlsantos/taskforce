<div id="form-materiaisos2modal" data-url="<?= url("ordens/materiais") ?>">
    <div class="modal modal-pag2 materiais-os2-modal" id="modalMateriaisOs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <h2 class="modal-title fs-2 titulo-pai" id="title-materiaisOs">
                                Produtos/Materiais Tarefa #<span id="tarefa-titulo-materiais"></span>
                                <p class="titulo-tarefa-modal" id="nomeservico"></p>
                            </h2>
                        </div>
                        <div class="fcad-form-group total-geral-os2mat">
                            <label class="span-result"> Valor Total Produtos/Materiais:</label>
                            <p>R$ <span class="titulo-tarefa-modal" id="os2mat-total-geral"></span></p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="modal-form">
                        <table class="table table-hover tab-list os2modal-list" id="matOs2-list">
                            <thead>
                                <tr>
                                    <th hidden>id</th>
                                    <th style="width: 50%;">Descrição</th>
                                    <th style="text-align: right;">Qtde</th>
                                    <th></th>
                                    <th style="text-align: right;">V.Unit(R$)</th>
                                    <th style="text-align: right; padding-right: 10px;">V.Total(R$)</th>
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
                                <th style="text-align: center;" colspan="7">
                                    Novo Produto/Material
                                </th>
                            </tr>
                            <tr>
                                <th hidden>id</th>
                                <th style="width: 50%">Descrição</th>
                                <th style="width: 15%">Qtde</th>
                                <th style="width: 15%">V.Unit(R$)</th>
                                <th style="width: 15%">V.Total(R$)</th>
                                <th>Grava</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="input-row linha-os2mat">
                                <td hidden>
                                    <input type="text" name="tarefaId" id="tarefa_id_material">
                                </td>
                                <td>
                                    <div class="fcad-form-row">
                                        <div class="coluna90">
                                            <select type="text" name="id_material" id="id_os2_material" class="form-control id_os2_material">
                                                <option value="">Selecione</option>
                                                <?php
                                                if (!empty($material)) :
                                                    foreach ($material as $mat) :
                                                ?>
                                                        <option value="<?= $mat->id; ?>" data-unidade="<?= $mat->unidade; ?>" data-vunit="<?= $mat->valor; ?>"><?= $mat->descricao; ?></option>
                                                <?php
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
                                    <input type="text" name="qtde" id="qtde_os2_material" class="form-control num-decimal3 qtde_os2_material" placeholder="Qtde">
                                </td>
                                <td>
                                    <input type="text" name="vunit" id="vunit_os2_material" class="form-control num-decimal2 vunit_os2_material" placeholder="Valor Unitário">
                                </td>
                                <td>
                                    <div class="inputreadonly">
                                        <input type="text" name="vtotal" id="vtotal_os2_material" class="form-control num-decimal2 vtotal_os2_material" placeholder="Valor Total" readonly>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" id="btn-os2mat-add" class="btn list-add"><i class="fa fa-check"></i></button>
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