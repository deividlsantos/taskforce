<?php

use Source\Models\Ent;
use Source\Models\Os2;
use Source\Models\Servico;

$this->layout("_theme", $front);
?>
<div class="ordens-container" id="ordens-list-container" data-url="<?= url("ordens/carregar_pagina"); ?>">
    <input type="hidden" id="tipo-user" value="<?= $user->tipo; ?>">
    <div class="fcad-form-row">
        <?php
        //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
        if ($user->tipo != 3) :
        ?>
            <a href="<?= url("ordens/form") ?>" id="nova-os" class="btn btn-success"><i class="fa fa-plus"></i>Nova OS</a>
            <button class="btn btn-success" style="width: 10em;" id="btnPreOs"><i class="fa fa-rocket"> </i> Pré OS</button>
        <?php
        endif;
        ?>
        <!--a href="<?= url("dash") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a-->
    </div>

    <form class="form-default" action="<?= url("ordens/carregar_pagina"); ?>" id="form-oslist">
        <div class="fcad-form-row" style="margin-top: 10px;">

            <!-- <div class="filtro-item">
            <label class="filtro-inline" for="inlineCheckbox1">Mostrar</label>
            <div class="fcad-form-row">
                <div class="fcad-form-inline">
                    <input type="checkbox" id="os-canceladas" value="canceladas">
                    <label for="os-canceladas">Canceladas</label>
                </div>
                <div class="fcad-form-inline">
                    <input type="checkbox" id="os-concluidas" value="concluidas">
                    <label for="os-concluidas">Concluídas</label>
                </div>
            </div>
        </div> -->

            <div class="filtro-item">
                <label class="filtro-inline">Status</label>
                <div class="fcad-form-row">
                    <?php
                    if (!empty($status)):
                        foreach ($status as $st) :
                    ?>
                            <div class="fcad-form-inline" style="font-size: 0.8em;">
                                <input type="checkbox" id="os-status-<?= $st->id; ?>" name="os-status[]" value="<?= $st->id; ?>">
                                <label class="filtro-chkbox-label" for="os-status-<?= $st->id; ?>"><?= $st->descricao; ?></label>
                            </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <?php
            if (!empty($tipo)):
                if (count($tipo) > 1):
            ?>
                    <div class="filtro-item">
                        <label for="os-tipo">Tipo</label>
                        <select id="os-tipo" name="os-tipo">
                            <option value="selecione">Selecione</option>
                            <?php
                            if (!empty($tipo)):
                                foreach ($tipo as $t) :
                            ?>
                                    <option value="<?= $t->descricao; ?>"><?= $t->descricao; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
            <?php
                endif;
            endif;
            ?>
        </div>
        <div class="fcad-form-row" style="margin-top: 10px;">
            <div class="filtro-item">
                <label class="filtro-inline">Buscar Por</label>
                <div class="fcad-form-row">
                    <div class="fcad-form-inline" style="width: 100%;">
                        <select id="os-buscar-por">
                            <option value="todos">Todos</option>
                            <option value="cliente">Cliente</option>
                            <option value="tarefa">Tarefas</option>
                            <option value="operador">Operador</option>
                            <option value="segmento"><?= $empresa->labelFiliais; ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Campo de texto livre -->
            <div class="filtro-item coluna20" id="filtro-tudo" style="display: none;">
                <label for="os-busca-geral">Buscar</label>
                <input type="text" id="os-busca-geral" placeholder="Digite algo...">
            </div>

            <!-- Cliente -->
            <div class="filtro-item coluna20" id="filtro-cliente" style="display: none;">
                <label for="os-cli">Cliente</label>
                <select id="os-cli" name="os-cli">
                    <option value="todos">Todos</option>
                    <?php if (!empty($cliente)) : ?>
                        <?php foreach ($cliente as $cli) : ?>
                            <option value="<?= $cli->id; ?>"><?= mb_strimwidth($cli->nome, 0, 50, '...'); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Tarefas -->
            <div class="filtro-item coluna20" id="filtro-tarefa" style="display: none;">
                <label for="os-tarefa">Tarefas</label>
                <select id="os-tarefa" name="os-tarefa">
                    <option value="todas">Todas</option>
                    <?php if (!empty($servico)) : ?>
                        <?php foreach ($servico as $srv) : ?>
                            <option value="<?= $srv->id; ?>"><?= mb_strimwidth($srv->nome, 0, 50, '...'); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Operador -->
            <div class="filtro-item coluna20" id="filtro-operador" style="display: none;">
                <label for="os-operador">Operador</label>
                <select id="os-operador" name="os-operador">
                    <option value="todos">Todos</option>
                    <?php if (!empty($operador)) : ?>
                        <?php foreach ($operador as $op) : ?>
                            <option value="<?= $op->id; ?>"><?= $op->nome; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Segmento -->
            <div class="filtro-item coluna20" id="filtro-segmento" style="display: none;">
                <label for="os-segmento"><?= $empresa->labelFiliais; ?></label>
                <select id="os-segmento" name="os-segmento">
                    <option value="todos">Todos</option>
                    <?php if (!empty($segmentos)) : ?>
                        <?php foreach ($segmentos as $seg) : ?>
                            <option value="<?= $seg->id; ?>"><?= $seg->nome; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filtro-item coluna20">

                <div class="fcad-form-row">
                    <div class="fcad-form-group">
                        <label for="">Ordenar Por</label>
                        <select id="os-ordenar-por" name="ordenar-por">
                            <option value="os">OS</option>
                            <option value="controle">N.Controle</option>
                            <option value="tipo">Tipo</option>
                            <option value="execucao">Execução</option>
                            <option value="cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="fcad-form-group">
                        <label for="toggle-sort1">Ordem</label>
                        <button type="button" id="toggle-sort-oslist" class="btn btn-secondary btn-oslist-sort"
                            title="CRESCENTE"><i class="fa fa-arrow-down-a-z"> </i> CRESC</button>
                    </div>
                </div>
            </div>

            <div class="inputreadonly" hidden>
                <div class="fcad-form-row">
                    <div class="fcad-form-group">
                        <label class="phantom-margin">_</label>
                        <input type="text" class="oslist-sort-input" id="oslist-order1-sort" name="sort1"
                            value="asc" readonly>
                    </div>
                </div>
            </div>


            <div class="filtro-item oslist-periodo">
                <div class="fcad-form-row">
                    <div class="fcad-form-group">
                        <label for="os-datai">Execução de:</label>
                        <input type="date" id="os-datai" value="" name="data-inicio">
                    </div>

                    <div class="fcad-form-group">
                        <label for="os-dataf">até:</label>
                        <input type="date" id="os-dataf" value="" name="data-fim">
                    </div>
                </div>
            </div>


            <button type="button" id="buscar-ordens" class="btn btn-info os-filtrar"><i class="fa fa-search"></i> Buscar</button>


        </div>
    </form>

    <!-- Navegação de páginas -->
    <section id="paginacao-oslist-section">
        <?php
        $this->insert("tcsistemas.os/ordens/ordensListPaginacao", [
            "paginacao" => $paginacao
        ]);
        ?>
    </section>

    <div class="fcad-form-row" style="margin-top: 10px;">
        <div class="filtro-item coluna20">
            <label for="filtrar-input">Filtrar</label>
            <input id="filtrarOrdens">
        </div>
    </div>

    <div class="tabela-responsive">
        <table id="ordens-list" class="tab-list table table-hover table-vendas">
            <thead>
                <tr>
                    <th>OS</th>
                    <th>Nº Controle</th>
                    <th>Status</th>
                    <th></th>
                    <?php
                    if (!empty($tipo)):
                        if (count($tipo) > 1):
                    ?>
                            <th>Tipo</th>
                    <?php
                        endif;
                    endif;
                    ?>
                    <th>Execução</th>
                    <th>Cliente</th>
                    <?php
                    //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
                    if ($user->tipo != 3) :
                    ?>
                        <th class="sorter-currency sort-header">Valor R$</th>
                    <?php
                    endif;
                    ?>
                    <th style="width:5%;">Pdf</th>
                    <th style="width:5%;">Pdf2</th>
                    <th style="width:5%;">Ver/Edt</th>
                    <?php
                    //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
                    if ($user->tipo != 3) :
                    ?>
                        <th style="width:5%;">Excluir</th>
                    <?php
                    endif;
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $this->insert("tcsistemas.os/ordens/ordensListListagem", [
                    "ordens" => $ordens,
                    "status" => $status,
                    "tipo" => $tipo,
                    "cliente" => $cliente,
                    "user" => $user
                ]);
                ?>
            </tbody>
        </table>
    </div>
</div>
<section>
    <?php

    $this->insert("tcsistemas.os/ordens/ordensModalCabecalhoEmpresa", [
        "empresasDoGrupo" => $empresasDoGrupo,
        "empresa_id" => $empresa->id
    ]);

    $this->insert("tcsistemas.os/ordens/ordensModalPreOs", [
        "cliente" => $cliente,
        "servico" => $servico,
        "operador" => $operador,
        "produto" => $produto
    ]);
    ?>
</section>

</html>