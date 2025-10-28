<form id="form-preos">
    <div class="modal modal-pag2" id="modalPreOs" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog"> <!-- Aumentei para caber a coluna lateral -->
            <div class="modal-content preos-modal d-flex">

                <div class="preos-principal flex-grow-1 d-flex flex-column">
                    <div class="modal-header">
                        <div class="cabecalho-modal preos-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Pré-Ordem de Serviço</h5>

                            <div class="fcad-form-row">
                                <div class="fcad-form-group">
                                    <label for="preos_cliente">Cliente</label>
                                    <select id="preos_cliente" class="form-select preos-select" data-url="<?= url("ordens/verifica_os") ?>">
                                        <option value="">Selecione um cliente</option>
                                        <?php
                                        if ($cliente) :
                                            foreach ($cliente as $cli) :
                                        ?>
                                                <option value="<?= $cli->id; ?>"><?= $cli->nome; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="fcad-form-row preos-linha2">
                                <div class="fcad-form-group">
                                    <label for="preos_servico">Serviço</label>
                                    <select id="preos_servico" class="form-select preos-select">
                                        <option value="">Selecione um serviço</option>
                                        <?php
                                        if ($servico) :
                                            foreach ($servico as $serv) :
                                        ?>
                                                <option value="<?= $serv->id; ?>"><?= $serv->nome; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <div class="fcad-form-group">
                                    <label for="preos_operador">Operador</label>
                                    <select id="preos_operador" class="form-select preos-select">
                                        <option value="">Selecione um operador</option>
                                        <?php
                                        if ($operador) :
                                            foreach ($operador as $op) :
                                        ?>
                                                <option value="<?= $op->id; ?>"><?= $op->nome; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <div class="fcad-form-group coluna10">
                                    <label for="preos_serv_qtd">Qtd</label>
                                    <input type="number" id="preos_serv_qtd" class="form-control preos-input" min="1">
                                </div>

                                <button type="button" class="btn btn-primary preos-add-btn" id="preos_add_servico">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="fcad-form-row preos-linha2">
                                <div class="fcad-form-group">
                                    <label for="preos_mat">Produtos/Materiais</label>
                                    <select id="preos_mat" class="form-select preos-select">
                                        <option value="">Selecione um produto/material</option>
                                        <?php
                                        if ($produto) :
                                            foreach ($produto as $prod) :
                                        ?>
                                                <option value="<?= $prod->id; ?>"><?= $prod->descricao; ?></option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <div class="fcad-form-group coluna10">
                                    <label for="preos_mat_qtd">Qtd</label>
                                    <input type="number" id="preos_mat_qtd" class="form-control preos-input" min="1">
                                </div>

                                <button type="button" class="btn btn-primary preos-add-btn" id="preos_add_produto">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn-close preos-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>


                    <div class="modal-body preos-body flex-grow-1">

                        <!-- TABELA DE SERVIÇOS -->
                        <h6>Serviços</h6>
                        <table class="preos-table">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Operador</th>
                                    <th>Qtd</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="preos_lista_servicos"></tbody>
                        </table>

                        <hr>

                        <!-- TABELA DE PRODUTOS -->
                        <h6 style="margin-top:1em;">Produtos / Materiais</h6>
                        <table class="preos-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="preos_lista_produtos"></tbody>
                        </table>

                    </div>


                    <div class="modal-footer">
                        <button style="margin-right: auto;" type="button" class="btn btn-info close" data-bs-dismiss="modal">Fechar</button>
                        <button data-url="<?= url("ordens/form") ?>" type="button" class="btn btn-success" id="btnCriarOS">Criar OS</button>
                    </div>

                </div>

                <!-- Coluna Lateral -->
                <div class="preos-lateral p-2">

                    <div id="preos_ultimas_ordens" data-url="<?= url("ordens/retorna_itens") ?>">
                        <div class="fcad-form-row" style="margin: 1em 0;">
                            <h6>Últimas Ordens</h6>
                        </div>
                        <div class="fcad-form-row" id="busca_os_group" style="margin: 1em 0;">
                            <div class="fcad-form-group">
                                <input type="text" id="preos_filtro_ultimas" class="mask-number">
                            </div>
                            <button type="button" class="btn btn-outline-secondary" id="preos_btn_buscar">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div class="lateral-itens">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>