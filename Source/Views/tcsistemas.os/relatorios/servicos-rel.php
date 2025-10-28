<?php
$this->layout("_theme", $front);
?>
<form method="post" class="form-cadastros" id="servicosrel-form" action="<?= url("servicosrel/resultados") ?>">
    <input type="hidden" name="page" value="1" id="page">
    <div class="ordens-container">
        <div class="fcad-form-row">
            <div class="filtro-item coluna20">
                <label for="servicosrel-cliente">Cliente</label>
                <select id="servicosrel-cliente" name="cliente">
                    <option value="">SELECIONE</option>
                    <?php
                    if (!empty($clientes)):
                        foreach ($clientes as $cli):
                    ?>
                            <option value="<?= $cli->id; ?>"><?= $cli->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="filtro-item coluna20">
                <label for="servicosrel-servico">Serviço</label>
                <select id="servicosrel-servico" name="servico">
                    <option value="todos">TODOS</option>
                    <?php
                    if (!empty($servicos)):
                        foreach ($servicos as $srv):
                    ?>
                            <option value="<?= $srv->id; ?>"><?= $srv->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>

            <div class="filtro-item coluna15">
                <label for="servicosrel-status">Status</label>
                <select id="servicosrel-status" name="status">
                    <option value="todos">TODOS</option>
                    <option value="A">AGUARDANDO INÍCIO</option>
                    <option value="I">EM ANDAMENTO</option>
                    <option value="P">PAUSADAS</option>
                    <option value="C">CONCLUÍDAS</option>
                    <option value="D">CANCELADAS</option>
                </select>
            </div>

            <div class="filtro-item coluna05">
                <label class="filtro-inline" for="inlineCheckbox1">Mostrar</label>
                <div class="fcad-form-row">
                    <div class="fcad-form-inline chk-filter-servicosrel">
                        <input class="status-filter" type="checkbox" id="servicosrel-concluidas" name="chk-concluidas">
                        <label for="servicosrel-concluidas">Concluídos</label>
                        <input class="status-filter" type="checkbox" id="servicosrel-canceladas" name="chk-canceladas">
                        <label for="servicosrel-canceladas">Cancelados</label>
                    </div>
                </div>
            </div>

            <div class="fcad-form-group coluna07 direita">
                <a href="<?= url("relatorios") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <div class="fcad-form-row" style=" margin-top: 10px;">

            <div class="filtro-item filtro-periodoi coluna10">
                <label for="servicosrel-datai">Execução de:</label>
                <input type="date" id="servicosrel-datai" value="" name="datai">
            </div>

            <div class="filtro-item filtro-periodof coluna10">
                <label for="servicosrel-dataf">até:</label>
                <input type="date" id="servicosrel-dataf" value="" name="dataf">
            </div>

            <div class="filtro-item coluna10">
                <label for="limit">Itens por página</label>
                <select id="limit" name="limit">
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                </select>
            </div>

            <button type="submit" id="" class="btn btn-info"><i class="fa fa-filter"></i></button>

        </div>
    </div>


    <div class="fcad-form-row">
        <div class="fcad-form-group coluna05">
            <label>Ordenar Por:</label>
        </div>

        <div class="filtro-item coluna20" id="coluna1">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="servicosrel-order1">1ª Coluna</label>
                    <select id="servicosrel-order1" name="order1" class="order-select">
                        <option value="servico">SERVIÇO</option>
                        <option value="id_os1">ORDEM DE SERVIÇO</option>
                        <option value="ctrl_os1">CONTROLE ORDEM DE SERVIÇO</option>
                        <option value="dataexec">DATA DE EXECUÇÃO</option>
                        <option value="segmento"><?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="ctrl_segmento">CONTROLE <?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="status">STATUS</option>
                        <option value="vtotal">VALOR</option>
                    </select>
                </div>

                <div class="fcad-form-group inputreadonly">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="toggle-sort1">Ordem</label>
                            <button type="button" id="toggle-sort1" class="btn btn-secondary btn-tasksort"
                                title="CRESCENTE"><i class="fa fa-arrow-up-a-z"></i></button>
                        </div>
                        <div class="fcad-form-group">
                            <label class="phantom-margin">_</label>
                            <input type="text" class="servicosrel-sort-input" id="servicosrel-order1-sort" name="sort1"
                                value="asc" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filtro-item coluna20" id="coluna2" style="display: none; position: relative;">
            <button type="button" class="remove-coluna btn-minus"
                style="position: absolute; top: 0; right: 0; display: none;">−</button>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="servicosrel-order2">2ª Coluna</label>
                    <select id="servicosrel-order2" name="order2" class="order-select">
                        <option value="">SELECIONE</option>
                        <option value="servico">SERVIÇO</option>
                        <option value="id_os1">ORDEM DE SERVIÇO</option>
                        <option value="ctrl_os1">CONTROLE ORDEM DE SERVIÇO</option>
                        <option value="dataexec">DATA DE EXECUÇÃO</option>
                        <option value="segmento"><?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="ctrl_segmento">CONTROLE <?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="status">STATUS</option>
                        <option value="vtotal">VALOR</option>
                    </select>
                </div>
                <div class="fcad-form-group inputreadonly">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="toggle-sort2">Ordem</label>
                            <button type="button" id="toggle-sort2" class="btn btn-secondary btn-tasksort"
                                title="CRESCENTE"><i class="fa fa-arrow-up-a-z"></i></button>
                        </div>
                        <div class="fcad-form-group">
                            <label class="phantom-margin">_</label>
                            <input type="text" class="servicosrel-sort-input" id="servicosrel-order2-sort" name="sort2"
                                value="asc" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filtro-item coluna20" id="coluna3" style="display: none; position: relative;">
            <button type="button" class="remove-coluna btn-minus"
                style="position: absolute; top: 0; right: 0;">−</button>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="servicosrel-order3">3ª Coluna</label>
                    <select id="servicosrel-order3" name="order3" class="order-select">
                        <option value="">SELECIONE</option>
                        <option value="servico">SERVIÇO</option>
                        <option value="id_os1">ORDEM DE SERVIÇO</option>
                        <option value="ctrl_os1">CONTROLE ORDEM DE SERVIÇO</option>
                        <option value="dataexec">DATA DE EXECUÇÃO</option>
                        <option value="segmento"><?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="ctrl_segmento">CONTROLE <?= strtoupper($emp->labelFiliais); ?></option>
                        <option value="status">STATUS</option>
                        <option value="vtotal">VALOR</option>
                    </select>
                </div>
                <div class="fcad-form-group inputreadonly">
                    <div class="fcad-form-row">
                        <div class="fcad-form-group">
                            <label for="toggle-sort3">Ordem</label>
                            <button type="button" id="toggle-sort3" class="btn btn-secondary btn-tasksort"
                                title="CRESCENTE"><i class="fa fa-arrow-up-a-z"></i></button>
                        </div>
                        <div class="fcad-form-group">
                            <label class="phantom-margin">_</label>
                            <input class="servicosrel-sort-input" type="text" id="servicosrel-order3-sort" name="sort3"
                                value="asc" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão de adicionar coluna -->
        <div class="fcad-form-group coluna05" id="coluna-add">
            <button type="button" id="add-order-col" style="margin-top: 25px;">+</button>
        </div>

        <div class="fcad-form-group direita coluna05" id="servicosrel-btns">
            <!-- Botões finais aqui -->
        </div>
    </div>
</form>


<div class="tabela-responsive">
    <div class="fcad-form-row" style="margin-top: 10px;">
        <div id="paginacao" class="paginacao-container">
            <!-- Os botões de página serão inseridos dinamicamente via jQuery -->
        </div>
        <div class="fcad-form-group direita coluna20">
            <span id="servicosrel-totalregistros"></span>
        </div>
        <div class="fcad-form-group direita coluna10">
            <span id="active-page"></span>
        </div>
    </div>
    <table id="servicosrel-list" class="tab-list table table-hover table-vendas">
        <thead>
            <tr>
                <th hidden>Id</th>
                <th>OS</th>
                <th>Ctrl OS</th>
                <th>Data Exec.</th>
                <th>Ctrl <?= $emp->labelFiliais; ?></th>
                <th>Nome <?= $emp->labelFiliais; ?></th>
                <th>Serviço</th>
                <th style="text-align: right;">Qtde</th>
                <th style="text-align: right;">V.Unit. R$</th>
                <th style="text-align: right; padding-right: 10px;">V.Total R$</th>
                <th style="margin-left: 5px;">Status</th>
                <!-- <th style="width:5%;">Pdf1</th>
                <th style="width:5%;">Pdf2</th> -->
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="100%">Selecione os filtros de pesquisa</td>
            </tr>
        </tbody>
    </table>
    <div id="filtros-paginacao" style="display: none;"></div>
</div>


</html>