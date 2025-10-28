<form id="form-novoopr" action="<?= url("operacao/salvar") ?>">
    <div class="modal modal-pag2" id="modalNovoOpr" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-2 titulo-pai">
                        Cadastrar Tipo de Pagto (Operação)
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="oper-form">
                        <div class="fcad-form-row ">
                            <input type="text" id="modal-opr" name="modal-opr" value="novo" hidden>
                            <input type="text" id="tipo" name="tipo" value="" hidden>
                            <div class="fcad-form-group coluna40">
                                <label for="descricao">Descricao:</label>
                                <input type="text" id="descricao" name="descricao">
                            </div>
                        </div>
                    </div>
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