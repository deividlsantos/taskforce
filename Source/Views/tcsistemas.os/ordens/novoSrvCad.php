<form id="form-novosrv" action="<?= url("servico/salvar") ?>">
    <div class="modal modal-pag2" id="modalNovosrv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoSrv">
                        Cadastrar Serviço
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modalsrv" name="modalsrv" value="novo" style="display:none">
                    <input type="text" id="target-select" name="target-select" style="display:none">
                    <section class="modal-form">
                        <?php                        
                        $this->insert("tcsistemas.os/servico/servicoForm", [                            
                            "servico" => "",
                            "empresa" => $empresa,
                            "plconta" => $plconta,
                            "recorrencias" => $recorrencias
                        ]);
                        ?>
                    </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Gravar</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>