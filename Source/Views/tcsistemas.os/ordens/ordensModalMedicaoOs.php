<div id="form-medicaomodal" data-url="<?= url("medicao") ?>">
    <div class="modal modal-pag2 medicao-os-modal" id="modalMedicaoOs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="fcad-form-row">
                        <h2 class="modal-title fs-2 titulo-pai" id="title-medicaoOs">
                            Medições Tarefa #<span id="tarefa-titulo-medicao"></span>
                            <p class="titulo-tarefa-modal" id="nomeservico"></p>
                        </h2>
                    </div>
                    <div class="fcad-form-row">
                        <div class="medicao-info-container coluna100">
                            <span class="medicao-info">Qtde Orçada: <span id="span-total" class="span-result"></span></span>
                            <span class="medicao-info">Qtde Medida: <span id="span-medido" class="span-result"></span></span>
                            <span class="medicao-info">Qtde Pendente: <span id="span-pendente" class="span-result"></span></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>                
                <div class="modal-body">
                    <section class="modal-form">
                        <table class="table table-hover tab-list os2modal-list" id="medicao-list">
                            <thead>
                                <tr>
                                    <th hidden>id</th>
                                    <th style="width: 15%;">Início</th>
                                    <th style="width: 15%;">Fim</th>
                                    <th style="width: 9%;">Qtde</th>
                                    <th style="width: 27%;">Operador</th>
                                    <th style="width: 27%;" <?= $empresa->servicosComEquipamentos != 'X' ? "hidden" : ""; ?>>Eqp</th>
                                    <th style="width: 40%;">Obs</th>
                                    <th style="width: 8%;"></th>
                                    <th style="width: 8%;"></th>
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
                                    Nova Medição
                                </th>
                            </tr>
                            <tr>
                                <th hidden>id</th>
                                <th style="width: 15%">Início</th>
                                <th style="width: 15%">Fim</th>
                                <th style="width: 15%">Qtde</th>
                                <th style="width: 15%">Oper.<span><button type="button" class="btn btn-info btn-oper-med-src"><i class="fa fa-search"></i></button></span></th>
                                <th style="width: 15%" <?= $empresa->servicosComEquipamentos != 'X' ? "hidden" : ""; ?>>Eqpto.<span><button type="button" class="btn btn-info btn-eqp-med-src"><i class="fa fa-search"></i></button></span></th>
                                <th style="width: 50%">Obs</th>
                                <th>Grava</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="input-row">
                                <td hidden>
                                    <input type="text" name="tarefaId" id="tarefa_id_medicao" class="form-control" placeholder="id_os2">
                                </td>
                                <td>
                                    <input type="datetime-local" name="datai" id="data_inicio_medicao" class="form-control" placeholder="Início">
                                </td>
                                <td>
                                    <input type="datetime-local" name="dataf" id="data_fim_medicao" class="form-control" placeholder="Fim">
                                </td>
                                <td>
                                    <input type="text" name="qtde" id="qtde_medicao" class="form-control" placeholder="Qtde">
                                </td>
                                <td>
                                    <select type="text" name="operador" id="operador_medicao" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php
                                        if (!empty($operador)) :
                                            foreach ($operador as $oper) :
                                        ?>
                                                <option value="<?= $oper->id; ?>"><?= $oper->nome; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td <?= $empresa->servicosComEquipamentos != 'X' ? "hidden" : ""; ?>>
                                    <select type="text" name="eqp" id="eqp_medicao" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php
                                        if (!empty($equipamentos)) :
                                            foreach ($equipamentos as $eqp) :
                                        ?>
                                                <option value="<?= $eqp->id; ?>"><?= $eqp->descricao; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="obs" id="obs_medicao" class="form-control" placeholder="Obs">
                                </td>
                                <td>
                                    <button type="button" id="btn-add" class="btn list-add"><i class="fa fa-check"></i></button>
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