<form id="form-contaspag" action="<?= url("contas/salvarpag") ?>">
    <div class="modal modal-pag1" id="modalPag" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai fluxo-negativo" id="staticBackdropLabel">Nova Despesa</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-plconta">Cód.</label>
                            <input type="text" id="cod-plconta-pag" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="plconta">Plano de Conta
                                <span>
                                    <button type="button" data-div="plcontapag-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i> </button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo" id="novoplcpag"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="plconta-pag" name="plconta">
                                <option value=""></option>
                                <?php
                                foreach ($plconta as $conta) :
                                    if ($conta->tipo == 'D') :
                                ?>
                                        <option value="<?= $conta->id; ?>"><?= $conta->descricao ?></option>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <div class="fcad-form-group coluna20">
                            <label for="datacad">Data Doc</label>
                            <input type="date" id="datacad-pag" name="datacad" value="<?php echo date('Y-m-d'); ?>" lang="pt-BR">
                        </div>
                        <div class="fcad-form-group coluna20">
                            <label for="competencia">Competência</label>
                            <input type="date" id="competencia-pag" name="competencia" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group">
                            <label for="documento">Documento</label>
                            <input type="text" id="documento-pag" name="documento" value="" required>
                        </div>
                        <div class="fcad-form-group">
                            <label for="vtotal">Valor Total</label>
                            <input class="mask-money" type="text" id="vtotal-pag" name="vtotal" value="" required>
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-for">Cód</label>
                            <input type="text" class="cod-for" id="cod-for-pag" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="fornecedor">Fornecedor
                                <span>
                                    <button type="button" data-div="fornecedor-div" class="btn btn-info btn-fin-src newregpag"><i class="fa fa-search"></i></button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo" id="novoclipag"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="fornecedor-pag" name="fornecedor" required>
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
                            <label for="cod-oper">Cód.</label>
                            <input type="text" id="cod-oper-pag" value="">
                        </div>
                        <div class="fcad-form-group coluna40">
                            <label for="operacao">Tp.Pgto(Operação)
                                <span>
                                    <button type="button" data-div="operacao-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo btn-novo-opr" data-tipo="D"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="operacao-pag" name="operacao">
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

                    <div class="fcad-form-row " hidden>
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-port">Cód.</label>
                            <input type="text" id="cod-port-pag" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="portador">Portador</label>
                            <select id="portador-pag" name="portador">
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
                    <div class="fcad-form-row " hidden>
                        <div class="fcad-form-group">
                            <label for="valor">Valor</label>
                            <input type="text" id="valor-pag" name="valor" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="dataven">Vencimento</label>
                            <input type="date" id="dataven-pag" name="dataven" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row " hidden>
                        <div class="fcad-form-group">
                            <label for="vdesc">Desconto</label>
                            <input type="text" id="vdesc-pag" name="vdesc" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="voutros">Outros</label>
                            <input type="text" id="voutros-pag" name="voutros" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="vparcial">Parcial</label>
                            <input type="text" id="vparcial-pag" name="vparcial" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="saldo">Saldo</label>
                            <input type="text" id="saldo-pag" name="saldo" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group">
                            <label for="obs1">Observação 1</label>
                            <input type="text" id="obs1-pag" name="obs1" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="obs2">Observação 2</label>
                            <input type="text" id="obs2-pag" name="obs2" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group">
                            <label for="autorizante">Autorizante</label>
                            <input type="text" id="autorizante-pag" name="autorizante" value="">
                        </div>
                        <div class="fcad-form-group coluna15 direita">
                            <label for="parcelas">Qdte.Parcelas</label>
                            <input type="text" id="parcelas-pag" name="parcelas" value="" pattern="\d*">
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label for="entrada">
                                Entrada
                            </label>
                            <input type="text" id="entrada-pag" name="entrada" value="" pattern="\d*">
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label id="switch-intervalo-pag" for="intervalo">Intervalo<span style="float:right;" class="fa fa-info-circle parcelas-info" data-tooltip="Clique aqui para alternar entre 'Data' ou 'Intervalo'."></span></label>
                            <input type="text" id="intervalo-pag" name="intervalo" value="" pattern="\d*">
                        </div>
                    </div>
                    <div id="campos-parcelas-pag">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="modal-pag-submit">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</form>



</html>