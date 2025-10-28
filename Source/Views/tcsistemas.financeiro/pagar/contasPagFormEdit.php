<form id="form-contas" action="<?= url("contas/salvaredit") ?>">
    <div class="modal modal-pag1 border-red" id="modalEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="title-edit">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group">
                                <label>Documento</label>
                                <input type="text" id="documento-edit" name="documento-edit">
                            </div>
                            <div class="fcad-form-group">
                                <label>Título</label>
                                <input type="text" id="titulo-edit" name="titulo-edit">
                            </div>
                        </div>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="id-edit" name="id-edit" hidden>
                    <input type="text" id="tipo-edit" name="tipo-edit" hidden>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna15">
                            <label for="competencia-edit">Competência</label>
                            <input class="mask-month" type="text" id="competencia-edit" name="competencia-edit">
                        </div>
                        <div class="fcad-form-group coluna20">
                            <label for="dataven-edit">Vencimento</label>
                            <input type="date" id="dataven-edit" name="dataven-edit" required>
                        </div>
                        <div class="fcad-form-group">
                            <label for="plconta-edit">Plano de Conta
                                <span>
                                    <button type="button" id="btn-plconta" data-div="plcontapag-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button>
                                </span>
                            </label>
                            <select id="plconta-edit" name="plconta-edit">
                                <option value=""></option>
                                <?php
                                foreach ($plconta as $conta) :
                                ?>
                                    <option value="<?= $conta->id; ?>" data-tipo="<?= $conta->tipo; ?>"><?= $conta->descricao ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna20" hidden>
                            <label for="vtotal-edit">Valor Total</label>
                            <input class="mask-money" type="text" id="vtotal-edit" name="vtotal-edit">
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-for">Cód</label>
                            <input type="text" class="cod-for" id="cod-for" value="">
                        </div>
                        <div class="fcad-form-group fornEdt">
                            <label for="fornecedor-edit">Fornecedor<span><button type="button" data-div="fornecedor-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
                            <select id="fornecedor-edit" name="fornecedor-edit">
                                <option value=""></option>
                                <?php
                                foreach ($fornecedor as $entf) :
                                ?>
                                    <option value="<?= $entf->id; ?>"><?= $entf->id . ' - ' . $entf->nome ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-cli">Cód</label>
                            <input type="text" class="cod-cli" id="cod-cli" value="">
                        </div>
                        <div class="fcad-form-group cliEdt">
                            <label for="cliente-edit">Cliente<span><button type="button" data-div="cliente-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
                            <select id="cliente-edit" name="cliente-edit">
                                <option value=""></option>
                                <?php
                                foreach ($cliente as $entc) :
                                ?>
                                    <option value="<?= $entc->id; ?>"><?= $entc->id . ' - ' . $entc->nome ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-oper">Cód.</label>
                            <input type="text" id="cod-oper" value="">
                        </div>
                        <div class="fcad-form-group coluna40">
                            <label for="operacao-edit">Tp.Pgto(Operação)<span><button type="button" data-div="operacao-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
                            <select id="operacao-edit" name="operacao-edit" required>
                                <option value=""></option>
                                <?php
                                foreach ($operacao as $oper) :
                                ?>
                                    <option value="<?= $oper->id; ?>"><?= $oper->id . ' - ' . $oper->descricao ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="fcad-form-row" hidden>
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-port">Cód.</label>
                            <input type="text" id="cod-port" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="portador-edit">Portador<span><button type="button" data-div="portador-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
                            <select id="portador-edit" name="portador-edit">
                                <option value=""></option>
                                <?php
                                foreach ($portador as $entp) :
                                ?>
                                    <option value="<?= $entp->id; ?>"><?= $entp->id . ' - ' . $entp->nome ?></option>
                                <?php
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="obs1-edit">Observação 1</label>
                            <input type="text" id="obs1-edit" name="obs1-edit" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="obs2-edit">Observação 2</label>
                            <input type="text" id="obs2-edit" name="obs2-edit" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row autorizanteEdt">
                        <div class="fcad-form-group">
                            <label for="autorizante-edit">Autorizante</label>
                            <input type="text" id="autorizante-edit" name="autorizante-edit" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row valores-edit">
                        <div class="fcad-form-group">
                            <label for="valor-edit">Valor</label>
                            <input class="mask-money" type="text" id="valor-edit" name="valor-edit" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="vdesc-edit">Desconto</label>
                            <input class="mask-money" type="text" id="vdesc-edit" name="vdesc-edit" value="" disabled>
                        </div>
                        <div class="fcad-form-group">
                            <label for="voutros-edit">Outros</label>
                            <input class="mask-money" type="text" id="voutros-edit" name="voutros-edit" value="" disabled>
                        </div>
                        <div class="fcad-form-group">
                            <label for="vparcial-edit">Parcial</label>
                            <input class="mask-money" type="text" id="vparcial-edit" name="vparcial-edit" value="" disabled>
                        </div>
                        <div class="fcad-form-group">
                            <label for="saldo-edit">Saldo</label>
                            <input class="mask-money" type="text" id="saldo-edit" name="saldo-edit" value="" disabled>
                        </div>
                    </div>
                </div>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button btnAccordion" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Histórico
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <table id="saldo-table" class="table tab-list tab-contas table-hover tabela-resumo">
                                    <thead>
                                        <tr>
                                            <th>Vlr.Parcela</th>
                                            <th>Vlr.Pago</th>
                                            <th>Outros</th>
                                            <th>Desc.</th>
                                            <th>Saldo</th>
                                            <th>Pagto</th>
                                            <th><input type="checkbox" id="saldo-todos" /></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-action="<?= url('contas/estornar_parcial'); ?>" id="btn-estorna-parcial" class="btn btn-danger esquerda">Estornar</button>
                    <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="edit-submit">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</form>



</html>