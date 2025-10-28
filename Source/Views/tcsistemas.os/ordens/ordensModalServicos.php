<form id="form-srvmodal">
    <div class="modal modal-pag2" id="modalSrv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                                    Selecione o serviço
                                </h2>
                            </div>
                        </div>
                        <div class="fcad-form-row input-srv-filtrar">
                            <div class="fcad-form-group coluna10">
                                <label>Filtrar:</label>
                            </div>
                            <div style="height: 5px;" class="fcad-form-group coluna50">
                                <input type="text" id="filtrarSrvModal" name="filtrar" value="">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-striped" id="srv_lst">
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" id="incluir-srv" hidden>Incluir</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>