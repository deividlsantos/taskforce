<form action="">
    <div class="modal modal-pag2" id="listKardex" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-ModalKardex">
                                    Histórico.
                                    <p class="titulo-tarefa-modal" id="nome-ferramenta"></p>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="tab-list table table-hover table-vendas tabela-head">
                        <thead>
                            <tr>
                                <th>Mov</th>
                                <th>Data</th>
                                <th>Local</th>
                                <th>Usuário</th>
                                <th style="text-align: center;">Entrada</th>
                                <th style="text-align: center;">Saída</th>
                                <th style="text-align: center;">Saldo</th>
                            </tr>
                        </thead>
                    </table>

                    <!-- Tabela do conteúdo rolável -->
                    <div class="tabela-responsive">
                        <table class="tab-list table table-hover table-vendas">
                            <tbody id="kardex-body">
                                <!-- conteúdo dinâmico -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>