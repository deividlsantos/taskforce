<form id="form-os2chkmodal" action="<?= url("ordens/salvarchecklist") ?>" method="post">
    <div class="modal modal-pag2" id="modalOs2Chk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal coluna90">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna60">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                                    Checklist do Equipamento
                                </h2>
                            </div>
                            <div class="fcad-form-group direita coluna10" style="margin-top: 3%;">
                                <button type="button" class="btn btn-info" id="chk-eqp-pdf" data-post="<?= url("ordens/checklistpdf"); ?>" data-idos2_2="">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                        <div class="fcad-form-row">
                            <div class="coluna60">
                                <span class="chk-eqp-desc"></span>

                                <span> - Tarefa: #<span class="chk-eqp-os2"></span></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Aqui vai a checklist via AJAX -->
                    <div id="checklist-container">
                        Carregando checklist...
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" id="save-os2chk">Salvar Checklist</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>