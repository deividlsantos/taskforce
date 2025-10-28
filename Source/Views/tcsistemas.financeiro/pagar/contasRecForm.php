<form id="form-contasrec" action="<?= url("contas/salvarrec") ?>">
    <div class="modal modal-pag1" id="modalRec" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="staticBackdropLabel">Nova Receita</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-plconta">Cód.</label>
                            <input type="text" id="cod-plconta-rec" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="plconta">Plano de Conta
                                <span>
                                    <button type="button" data-div="plcontarec-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo" id="novoplcrec"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="plconta-rec" name="plconta">
                                <option value=""></option>
                                <?php
                                foreach ($plconta as $conta) :
                                    if ($conta->tipo == 'R') :
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
                            <input type="date" id="datacad-rec" name="datacad" value="<?php echo date('Y-m-d'); ?>" lang="pt-BR">
                        </div>
                        <div class="fcad-form-group coluna20">
                            <label for="competencia">Competência</label>
                            <input type="date" id="competencia-rec" name="competencia" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group">
                            <label for="documento">Documento</label>
                            <input type="text" id="documento-rec" name="documento" value="" required>
                        </div>
                        <div class="fcad-form-group">
                            <label for="vtotal">Valor Total</label>
                            <input class="mask-money" type="text" id="vtotal-rec" name="vtotal" value="" required>
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group coluna10" hidden>
                            <label for="cod-for">Cód</label>
                            <input type="text" class="cod-for" id="cod-for-rec" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="cliente">Cliente
                                <span>
                                    <button type="button" data-div="cliente-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo" id="novoclirec"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="cliente" name="cliente" required>
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
                            <input type="text" id="cod-oper-pag" value="">
                        </div>
                        <div class="fcad-form-group coluna40">
                            <label for="operacao">Tp.Pgto(Operação)
                                <span>
                                    <button type="button" data-div="operacao-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button>
                                </span>
                                <span>
                                    <button type="button" class="btn btn-info btn-fin-novo btn-novo-opr" data-tipo="R"><i class="fa fa-plus"></i></button>
                                </span>
                            </label>
                            <select id="operacao-rec" name="operacao">
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
                            <input type="text" id="cod-port-rec" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="portador">Portador<span><button type="button" data-div="portador-div" class="btn btn-info btn-fin-src"><i class="fa fa-search"></i></button></span></label>
                            <select id="portador-rec" name="portador">
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
                            <input type="text" id="valor-rec" name="valor" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="dataven">Vencimento</label>
                            <input type="date" id="dataven-rec" name="dataven" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row " hidden>
                        <div class="fcad-form-group">
                            <label for="vdesc">Desconto</label>
                            <input type="text" id="vdesc-rec" name="vdesc" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="voutros">Outros</label>
                            <input type="text" id="voutros-rec" name="voutros" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="vparcial">Parcial</label>
                            <input type="text" id="vparcial-rec" name="vparcial" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="saldo">Saldo</label>
                            <input type="text" id="saldo-rec" name="saldo" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row ">
                        <div class="fcad-form-group">
                            <label for="obs1">Observação 1</label>
                            <input type="text" id="obs1-rec" name="obs1" value="">
                        </div>
                        <div class="fcad-form-group">
                            <label for="obs2">Observação 2</label>
                            <input type="text" id="obs2-rec" name="obs2" value="">
                        </div>
                    </div>
                    <div class="fcad-form-row grupo-parcelas">
                        <div class="fcad-form-group coluna15 direita">
                            <label for="parcelas">Qdte.Parcelas</label>
                            <input type="text" id="parcelas-rec" name="parcelas" value="" pattern="\d*">
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label for="entrada">Entrada</label>
                            <input type="text" id="entrada-rec" name="entrada" value="" pattern="\d*">
                        </div>
                        <div class="fcad-form-group coluna15">
                            <label id="switch-intervalo-rec" for="intervalo">Intervalo<span style="float:right;" class="fa fa-info-circle parcelas-info" data-tooltip="Clique aqui para alternar entre 'Data' ou 'Intervalo'."></span></label>
                            <input type="text" id="intervalo-rec" name="intervalo" value="" pattern="\d*">
                        </div>
                    </div>
                    <div id="campos-parcelas-rec">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-fecharmodal" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="modal-rec-submit">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>