<form id="form-obrasmodal" action="<?= url("obras/salvar") ?>">
    <div class="modal modal-pag2" id="modalObras" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                        Cadastrar <?= $label; ?>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modalobras" name="modalobras" value="novo" hidden>
                    <section class="modal-form">
                        <?php
                        $this->insert("tcsistemas.os/obras/obrasForm", [
                            "obras" => $obras,
                            "cliente" => $cliente
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