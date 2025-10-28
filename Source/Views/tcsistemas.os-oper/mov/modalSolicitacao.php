<!-- Modal -->
<div class="modal fade modal-solmob" id="modalSolicitacao" data-url="<?= url("medicao") ?>" tabindex="-1" role="dialog" aria-labelledby="opermedicaoModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Confirmar Movimentação</h2>
                <button type="button" id="close-solmob" class="btn-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body" id="solmob-body">
                <form class="form-cadastros" method="post" action="<?= url("oper_mov/solicitar_mov") ?>">
                    <input type="hidden" id="id_mov" name="id_mov">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label>Ferramenta</label>
                            <p class="modal-solmob" id="solmob-eqp"></p>
                        </div>
                        <div class="fcad-form-group">
                            <label>Qtde</label>
                            <p class="modal-solmob" id="solmob-qtde">10</p>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label>Local de Origem</label>
                            <p class="modal-solmob" id="solmob-lorigem">OFICINA</p>
                        </div>
                        <div class="fcad-form-group">
                            <label>Data</label>
                            <p class="modal-solmob" id="solmob-data">10/05/2025</p>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label style="font-size: 1.2em;">DESTINO:</label>
                            <p>Usuário: <span class="modal-solmob" id="solmob-udestino">Usuário Destino</span></p>
                            <p>Local: <span class="modal-solmob" id="solmob-ldestino">Local Destino</span></p>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna100">
                            <button class="btn btn-solmob-success" id="confirmarSolicitacao">
                                <i class="fa-solid fa-check"></i> Confirmar Recebimento
                            </button>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna100">
                            <button type="button" data-url="<?= url("oper_mov/cancelar_mov") ?>" class="btn btn-solmob-danger" id="cancelarSolicitacao">
                                <i class="fa-solid fa-x"></i> Cancelar Solicitação
                            </button>
                        </div>
                    </div>

                    <p style="margin-top: 20px; font-size: 0.7em;" id="sol-info">* Ao clicar em confirmar, você declara ter recebido a ferramenta no local de destino.</p>
                </form>
            </div>
        </div>
    </div>
</div>