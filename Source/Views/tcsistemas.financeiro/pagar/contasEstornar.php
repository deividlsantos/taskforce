<form id="form-contasestornar" action="<?= url("contas/estornar") ?>">
    <div class="modal" id="modalEstornar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-estornar">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai" id="staticBackdropLabel">Estornar Lançamentos</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $this->insert("tcsistemas.financeiro/pagar/contasEstornarList", []);
                    ?>
                </div>
                <div class="modal-footer">
                    <div class="esquerda">
                        <span>Quantidade de Títulos: <strong id="totalRegistrosEs">0</strong></span>
                        <span>Total Estorno: R$ <strong id="totalEstorno">0,00</strong></span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" id="modal-estornar-submit">Estornar</button>
                </div>
            </div>
        </div>
    </div>
</form>

</html>