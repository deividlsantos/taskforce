<div class="modal modal-pag2" id="modalBaixasList" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="cabecalho-modal">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group coluna100">
                            <h2 class="modal-title fs-2 titulo-pai" id="title-editNovoCli">
                                Listagem de Baixas
                            </h2>
                        </div>
                    </div>
                    <form method="post" class="form-cadastros form-baixas-list" id="baixas-list-form" action="<?= url("baixar/busca") ?>">
                        <input type="hidden" name="page" value="1" id="page">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group">
                                <label>Buscar:</label>
                                <input type="text" name="lote">
                            </div>
                            <div class="fcad-form-group">
                                <label>Data Baixa:</label>
                                <input type="date" name="data_baixa">
                            </div>
                            <div class="fcad-form-group">
                                <label>Tipo</label>
                                <select name="tipo" class="form-control">
                                    <option value="todos">Ambos</option>
                                    <option value="R">Receita</option>
                                    <option value="D">Despesa</option>
                                </select>
                            </div>
                            <div class="fcad-form-group">
                                <label>Itens por página</label>
                                <select name="limit" class="form-control">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                </select>
                            </div>
                            <div class="fcad-form-group">
                                <label style="color:transparent;">_</label>
                                <button id="btnFiltrarBaixas" class="btn btn-info filtrar-baixas"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <div id="filtros-paginacao" style="display: none;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalBaixasConteudo">
                    <div class="fcad-form-row" style="margin-top: 10px;">
                        <div id="paginacao" class="paginacao-container"></div>
                        <div class="fcad-form-group direita" style="text-align: right;">
                            <span id="baixas-totalregistros"></span>
                        </div>
                        <div class="fcad-form-group direita" style="text-align: right;">
                            <span id="active-page"></span>
                        </div>
                    </div>
                    <div id="tableBaixasList-container">
                        <table id="tableBaixasList" class="table table-striped table-hover table-bordered table-vendas">
                            <thead>
                                <tr>
                                    <th>Lote</th>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th style="width:5%"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>