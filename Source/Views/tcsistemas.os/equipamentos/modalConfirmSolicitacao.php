<form method="post" action="<?= url("equipamentos/solicitar_mov"); ?>">
    <div class="modal modal-pag2" data-url="<?= url("equipamentos/retorna_solicitacao"); ?>" id="modalConfirmSolicitacao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <input type="hidden" name="id_mov" id="id_mov" value="">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-ferramentaGestao">
                                    Confirmar Movimentação
                                    <p class="titulo-tarefa-modal" id="nome-ferramenta"></p>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label>Equipamento/Ferramenta</label>
                            <p id="soldesk-eqp" class="modal-soldesk"></p>
                        </div>
                        <div class="fcad-form-group">
                            <label>Local de Origem</label>
                            <p id="soldesk-lorigem" class="modal-soldesk"></p>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label>Qtde</label>
                            <p id="soldesk-qtde" class="modal-soldesk"></p>
                        </div>
                        <div class="fcad-form-group">
                            <label>Data Solicitação</label>
                            <p id="soldesk-data" class="modal-soldesk"></p>
                        </div>
                    </div>

                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label style="font-size: 1.2em;">DESTINO:</label>
                            <p style="margin-bottom: 0;"><b>Usuário:</b> <span id="soldesk-udestino" class="modal-soldesk"></span></p>
                            <p><b>Local:</b> <span id="soldesk-ldestino" class="modal-soldesk"></span></p>
                        </div>
                    </div>
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna100">
                            <button class="btn btn-success" id="confirmarSolicitacao">
                                <i class="fa-solid fa-check"></i> Confirmar Recebimento
                            </button>
                        </div>
                    </div>
                    <div class="fcad-form-row" style="margin-top: 20px;">
                        <div class="fcad-form-group coluna100">
                            <button class="btn btn-danger" data-url="<?= url("equipamentos/cancelar_mov")?>" type="button" id="cancelarSolicitacao">
                                <i class="fa-solid fa-x"></i> Cancelar Solicitação
                            </button>
                        </div>
                    </div>

                    <p style="margin-top: 20px; font-size: 0.7em;">* Ao clicar em confirmar, você declara ter recebido a ferramenta no local de destino.</p>

                </div>
            </div>
        </div>
    </div>
</form>