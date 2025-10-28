<div id="modalEdicaoFerramentaPsi" class="modal modal-pag1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-2 titulo-pai">Gestão de Ferramenta</h3>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" type="button">
                </button>
            </div>

            <div class="modal-body">
                <div class="fcad-form-row">
                    <div class="fcad-form-group">
                        <form class="formulario-ferramenta-upsilon">
                            <div class="fcad-form-group">
                                <label for="codFerramenta">Código:</label>
                                <input type="text" id="codFerramenta" class="entrada-codigo-ferramenta"
                                    placeholder="Ex: FRR-001" value="">
                            </div>
                             <div class="fcad-form-group">
                                <label for="descFerramenta">Descrição:</label>
                                <input type="text" id="descFerramenta" class="entrada-descricao-ferramenta"
                                    placeholder="Ex: Furadeira Elétrica Industrial" value="">
                            </div>
                            <div class="fcad-form-group coluna30">
                                <label for="qtdFerramenta">Quantidade:</label>
                                <input type="number" id="qtdFerramenta" class="entrada-quantidade-ferramenta"
                                    placeholder="0" value="" min="0">
                            </div>
                            <div class="fcad-form-group">
                                <label for="stFerramentas">Status:</label>
                                <select id="stFerramentas" class="selecao-status-ferramenta">
                                    <option value="">Selecione o status</option>
                                    <option value="ativo">Ativo</option>
                                    <option value="manutencao">Manutenção</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="alocado">Alocado</option>
                                    <option value="disponivel">Disponível</option>
                                </select>
                            </div>
                            <div class="rodape-formulario-lambda">
                                <button type="button" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Salvar
                                </button>
                                <button type="button" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>