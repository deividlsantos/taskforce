<form id="form-novocli" action="<?= url("ent/salvar") ?>">
    <div class="modal modal-pag2" id="modalNovocli" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                        Cadastrar Cliente
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modalcli" name="modalcli" value="novo" style="display:none">
                    <section class="modal-form">
                        <?php                        
                        $this->insert("tcsistemas.financeiro/ent/entForm", [
                            "ent" => "",
                            "uri" => "cliente",
                            "tipo" => null,
                            "hidden" => "hidden",
                            "bank" => ""
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