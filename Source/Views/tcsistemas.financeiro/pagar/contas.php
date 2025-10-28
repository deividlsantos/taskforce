<?php
$this->layout("_theme", $front);
?>

<div class="pagcontainer">
    <div class="fcad-form-row">
        <div class="filtro-item">
            <div class="fcad-form-row">
                <div class="fcad-form-row contas-buttons">
                    <button id="abrirModalRec" type="button" class="btn btn-success"><i class="fa fa-check"></i> Nova Receita</button>
                    <button id="abrirModalPag" type="button" class="btn btn-danger"><i class="fa fa-check"></i> Nova Despesa</button>
                </div>
            </div>
        </div>

        <div class="filtro-item fcad-form-group coluna10 direita">
            <label for="filtrar-tipo">Tipo</label>
            <select id="filtrar-tipo">
                <option value="todos">Todos</option>
                <option value="despesa">Despesa</option>
                <option value="receita">Receita</option>
            </select>
        </div>

        <div class="filtro-item fcad-form-group coluna10">
            <label for="filtrar-situacao">Situação</label>
            <select id="filtrar-situacao">
                <option value="todos">Todos</option>
                <option value="aberto">Aberto</option>
                <option value="baixado">Baixado</option>
            </select>
        </div>


        <div class="filtro-item fcad-form-group filtro-periodoi coluna10">
            <label for="filtrar-datai">Período:</label>
            <input type="date" id="filtrar-datai" value="">
        </div>

        <div class="filtro-item fcad-form-group filtro-periodof coluna10">
            <label style="color:transparent;" for="filtrar-dataf">_</label>
            <input type="date" id="filtrar-dataf" value="">
        </div>

        <div class="filtro-item fcad-form-group coluna15">
            <label for="filtrar-tipo-data">Filtrar por:</label>
            <select id="filtrar-tipo-data">
                <option value="datacad">Lançamento</option>
                <option value="dataven">Vencimento</option>
            </select>
        </div>
        <div class="filtro-item fcad-form-group coluna07">
            <button type="button" id="filtrar-periodo" class="btn btn-filtro"><i class="fa fa-filter"></i></button>
        </div>
    </div>

    <div class="fcad-form-row" style="margin-top: 10px;">
        <div class="filtro-item coluna20">
            <label for="filtrar-input">Procurar</label>
            <input id="filtrarPag">
        </div>

        <div class="fcad-form-group coluna10 direita">
            <button type="button" data-url="<?= url("baixar/busca"); ?>" class="btn btn-secondary baixas-lote-list"><i class=""></i> Visualizar Baixas</button>
        </div>
    </div>

    <div class="fcad-form-row" style="margin-top: 10px;">
        <div class="fluxo direita">
            <div class="painel-led fcad-form-row">
                <div class="fcad-form-group direita coluna10">
                    <label for="">Qtde.</label>
                    <div class="total-quantidade valor-led">
                        <strong><span id="quantidade-marcada">0</span></strong>
                    </div>
                </div>
                <div class="fcad-form-group coluna30">
                    <label for="">Total</label>
                    <div class="total-sum valor-led">
                        <span id="total-sum">0,00</span>
                    </div>
                </div>
                <div class="fcad-form-group btnBaixar coluna10">
                    <label for="">_</label>
                    <button id="btnBaixar" data-action="<?= url('baixar'); ?>" class="btn btn-success" disabled><i class="fa-regular fa-square-check"></i> Baixar</button>
                </div>
                <div class="fcad-form-group btnEstornar coluna10">
                    <label for="">_</label>
                    <button type="button" id="btnEstornar" data-action="<?= url('contas/estorno'); ?>" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEstornar" disabled>
                        <i class="fa fa-undo"></i> Estornar
                    </button>
                </div>
                <div class="fcad-form-group btnExcluir coluna10">
                    <label for="">_</label>
                    <button type="button" id="btnExcluir" data-action="<?= url('contas/excluirtudo'); ?>" class="btn btn-danger" disabled>
                        <i class="fa fa-trash"></i> Excluir
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="fluxo-pagxrec">
    <div class="fluxo-full">
        <div class="fluxo-title">Registros</div>
        <table id="pag-rec" class="table tab-list table-hover tabela-resumo tab-contas">
            <thead>
                <tr>
                    <th hidden></th>
                    <th class="sorter-shortDate sort-header" data-date-format="ddmmyyyy">Data</th>
                    <th class="sort-header">Título</th>
                    <th class="sort-header">Fornecedor/Cliente</th>
                    <th class="sorter-currency sort-header">Receita</th>
                    <th class="sorter-currency sort-header">Despesa</th>
                    <th class="sort-header">Parcial</th>
                    <th class="sort-header">Saldo</th>
                    <th class="sorter-shortDate sort-header" data-date-format="ddmmyyyy">Vencimento</th>
                    <th class="sort-header">Baixado</th>
                    <th width="3%"><input type="checkbox" id="check-all"></th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($lancamentos)) :
                    foreach ($lancamentos as $reg) :
                        if ($reg->tipo == 'receita') {
                            $cliFor = $reg->id_entc;
                            $entidades = $cliente;
                        } else {
                            $cliFor = $reg->id_entf;
                            $entidades = $fornecedor;
                        }
                ?>
                        <tr>
                            <td hidden><?= $reg->tipo; ?></td>
                            <td data-dateformat="<?= date_fmt($reg->datacad, 'Y-m-d'); ?>"><?= date_fmt($reg->datacad, 'd/m/Y'); ?></td>
                            <td><?= mb_strimwidth($reg->titulo, 0, 15, "..."); ?></td>
                            <td>
                                <?php foreach ($entidades as $ent):
                                    if ($ent->id == $cliFor):
                                        echo mb_strimwidth($ent->nome, 0, 25, "...");
                                    endif;
                                endforeach;
                                ?>
                            </td>
                            <td class="<?= $reg->tipo == 'receita' ? "moedareal" : ""; ?>"><?= $reg->tipo == 'receita' ? ($reg->baixado == "N" ? moedaBR($reg->valor) : moedaBR($reg->valor)) : ""; ?></td>
                            <td class="<?= $reg->tipo == 'despesa' ? "moedareal" : ""; ?>"><?= $reg->tipo == 'despesa' ? ($reg->baixado == "N" ? moedaBR($reg->valor) : moedaBR($reg->valor)) : ""; ?></td>
                            <td class="moedareal"><?= moedaBR($reg->vpago); ?></td>
                            <td class="moedareal"><?= moedaBR($reg->saldo); ?></td>
                            <td><?= date_fmt($reg->dataven, 'd/m/Y'); ?></td>
                            <td><?= $reg->baixado; ?></td>
                            <td><input type="checkbox" class="check-contas" data-tipo="<?= $reg->tipo; ?>" data-id="<?= $reg->id; ?>" disabled></td>
                            <td><button class="btn btn-secondary abrirModalEdit list-edt" data-tipo="<?= $reg->tipo; ?>" data-url="<?= $reg->tipo == 'receita' ? url('contas/edtrec') : url('contas/edtpag'); ?>" data-id="<?= ll_encode($reg->id); ?>"><i class="fa fa-pen"></i></button></td>
                            <td><a class="btn btn-secondary list-del" data-grid="#pag-rec" data-post="<?= url('contas/excluir'); ?>" data-tipo="<?= ll_encode($reg->tipo); ?>" data-confirm="Tem certeza que deseja apagar esse registro?" data-id="<?= $reg->id; ?>"><i class="fa fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach;
                else:
                    ?>
                    <td colspan="100%">NENHUM REGISTRO ENCONTRADO</td>
                <?php endif;
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- fluxo Section -->
<div class="fluxo-pagcontainer">
    <!-- Table Section -->



</div>



<section class="modal-form">
    <?php
    $this->insert("tcsistemas.financeiro/pagar/contasPagForm", [
        "front" => $front,
        "fornecedor" => $fornecedor,
        "portador" => $portador,
        "plconta" => $plconta,
        "operacao" => $operacao
    ]);

    $this->insert("tcsistemas.financeiro/pagar/contasRecForm", [
        "front" => $front,
        "cliente" => $cliente,
        "portador" => $portador,
        "plconta" => $plconta,
        "operacao" => $operacao
    ]);

    $this->insert("tcsistemas.financeiro/pagar/contasPagFormEdit", [
        "front" => $front,
        "fornecedor" => $fornecedor,
        "cliente" => $cliente,
        "portador" => $portador,
        "plconta" => $plconta,
        "operacao" => $operacao
    ]);

    $this->insert("tcsistemas.financeiro/pagar/contasModalSrch", [
        "fornecedor" => $fornecedor,
        "cliente" => $cliente,
        "portador" => $portador,
        "plconta" => $plconta,
        "operacao" => $operacao
    ]);

    $this->insert("tcsistemas.os/ordens/novocliCad", [
        "ent" => "",
        "uri" => "fornecedor",
        "tipo" => "2",
        "hidden" => "hidden"
    ]);

    $this->insert("tcsistemas.financeiro/pagar/contasEstornar", []);

    $this->insert("tcsistemas.financeiro/baixar/baixasListModal", []);
    $this->insert("tcsistemas.financeiro/pagar/contasModalNovoPlconta", []);
    $this->insert("tcsistemas.financeiro/pagar/contasModalNovoOper", []);

    ?>
</section>

</html>