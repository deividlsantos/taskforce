<?php $this->layout('_theme', $front); ?>
<div class="container-nfe">
    <h2 class="titulo-nfe">Formulário de Nota Fiscal</h2>

    <form class="ajax_off" id="formNfe" data-url="<?= url('nfe/dados') ?>">
        <div style="display: flex; justify-content: space-between;">
            <h3 style="width: 30%;">Dados da Nota</h3>
            <input type="hidden" name="id_emp2" id="id_emp2" value="<?= ll_encode($emp->id); ?>" style="width: 10%;">
            <div style="display: flex; width: 50%; float: right;">
                <div style="width: 35%; padding: 5px;">
                    <label class="label-nfe">Data de Emissão</label>
                    <input type="date" class="input-nfe" name="ide_dhEmi" value="<?= date('Y-m-d'); ?>">
                </div>
                <div style="width: 35%; padding: 5px;">
                    <label class="label-nfe">Data de Saída</label>
                    <input type="date" class="input-nfe" name="ide_dhSaiEn" value="<?= date('Y-m-d'); ?>">
                </div>
                <div style="width: 25%; padding: 5px;">
                    <label class="label-nfe">Hora</label>
                    <input type="time" class="input-nfe" name="hora_saida">
                </div>
            </div>
        </div>
        <!-- Dados da Nota -->
        <div class="grupo-nfe">
            <div style="display: flex;">
                <div style="width: 15%; padding: 5px;">
                    <label class="label-nfe">Número da Nota</label>
                    <input type="text" class="input-nfe" name="ide_nNF" style="background: #DCDCDC;" value="00000023">
                </div>

                <div style="width: 10%; padding: 5px;">
                    <label class="label-nfe">Série</label>
                    <input type="text" class="input-nfe" name="ide_serie" style="background: #DCDCDC;" value="1">
                </div>

                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">Natureza da operação</label>
                    <select class="select-nfe" name="ide_natOp" id="natOp">
                        <option value="venda">Venda</option>
                        <option value="devolucao">Devolução</option>
                    </select>
                </div>
                <div style="width: 50%; padding: 5px;">
                    <label style="font-weight: normal; margin-top: 40px; margin-left: 10px"><input type="radio" name="decisao" value="0"> Entrada</label>
                    <label style="font-weight: normal;"><input type="radio" name="decisao" value="1" checked> Saída</label>
                </div>
            </div>
        </div>
        <div class="cli-emit">
            <p class="emit">Emitente</p>
            <input class="emit-pesq" type="text" value="<?= $emp->razao; ?>" readonly>
            <button class="btn btn-secundary verifica-emit" type="button"><i class="fa-solid fa-ellipsis"></i></button>
        </div>
        <!-- Emitente -->
        <div class="grupo-nfe" id="emit" style="display: none;">
            <div style="display: flex;">
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">CNPJ|CPF</label>
                    <input type="text" class="input-nfe" name="emit_CNPJ" value="<?= $emp->cnpj; ?>" readonly>
                </div>
                <div style="width: 20%; padding: 5px; display: none;">
                    <label class="label-nfe">Inscrição Estadual</label>
                    <input type="text" class="input-nfe" name="emit_IE" value="<?= $emp->ie; ?>" readonly>
                </div>
                <div style="width: 75%; padding: 5px;">
                    <label class="label-nfe">Razão Social</label>
                    <input type="text" class="input-nfe" name="emit_xNome" value="<?= $emp->razao; ?>" readonly>
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">CEP</label>
                    <input type="text" class="input-nfe" name="emit_CEP" id="emit_cep" value="<?= $emp->cep; ?>" readonly>
                </div>
                <div style="display: 
                block; width: 65%; padding: 5px;">
                    <label class="label-nfe">Endereço</label>
                    <input type="text" class="input-nfe" name="emit_xLgr" id="emit_endereco" value="<?= $emp->endereco; ?>" readonly>
                </div>
                <div class="valores" style="width: 10%;">
                    <label class="label-nfe">Nº</label>
                    <input type="text" class="input-nfe" name="emit_nro" value="<?= $emp->numero ?>" id="emit_num" readonly>
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 43%; padding: 5px;">
                    <label class="label-nfe">Bairro</label>
                    <input type="text" class="input-nfe" name="emit_xBairro" id="emit_bairro" value="<?= $emp->bairro; ?>" readonly>
                </div>
                <div style="width: 42%; padding: 5px;">
                    <label class="label-nfe">Cidade</label>
                    <input type="text" class="input-nfe" name="emit_xMun" id="emit_cidade" value="<?= $emp->cidade; ?>" readonly>
                </div>
                <div style="width: 10%; padding: 5px;">
                    <label class="label-nfe">UF</label>
                    <input type="text" class="input-nfe" name="emit_UF" id="emit_estado" value="<?= $emp->uf; ?>" readonly>
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">Telefone</label>
                    <input type="text" class="input-nfe" name="emit_fone" id="emit_telefone" value="<?= $emp->fone1; ?>" readonly>
                </div>
            </div>
        </div>
        <div class="cli-emit">
            <p class="dest">Destinatário</p>
            <select
                class="dest-pesq"
                id="cliente_busca"
                data-url="<?= url('nfe/cliente-busca') ?>" style="width: 80%; margin-left: 5%;"></select>
            <button class="btn btn-secundary verifica-dest" type="button" id="btn-opcoes"><i class="fa-solid fa-ellipsis"></i></button>
            <button class="btn btn-success" type="button" id="nfe-novocli" style="display: none; width: 40px; height: 30px; margin-left: 5px;" data-origem="nfe">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        <div id="lista_clientes" class="autocomplete"></div>
        <!-- Destinatário -->
        <div class="grupo-nfe" id="dest" style="display: none;">
            <div style="display: flex;">
                <input type="text" id="cli_id" name="cli_id" hidden>
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">CNPJ/CPF</label>
                    <input type="text" class="input-nfe" name="dest_CNPJ" id="dest_cnpj">
                </div>
                <div style="width: 20%; padding: 5px; display: none;">
                    <label class="label-nfe">Inscrição Estadual</label>
                    <input type="text" class="input-nfe" name="dest_IE" id="dest_ie">
                </div>
                <div style="width: 75%; padding: 5px;">
                    <label class="label-nfe">Razão Social</label>
                    <input type="text" class="input-nfe" name="dest_xNome" id="dest_razao">
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">CEP</label>
                    <input type="text" class="input-nfe" name="dest_CEP" id="dest_cep">
                </div>
                <div style="display: 
                block; width: 65%; padding: 5px;">
                    <label class="label-nfe">Endereço</label>
                    <input type="text" class="input-nfe" name="dest_xLgr" id="dest_endereco">
                </div>
                <div class="valores" style="width: 10%;">
                    <label class="label-nfe">Nº</label>
                    <input type="text" class="input-nfe" name="dest_nro" id="dest_num">
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 42%; padding: 5px;">
                    <label class="label-nfe">Bairro</label>
                    <input type="text" class="input-nfe" name="dest_xBairro" id="dest_bairro">
                </div>
                <div style="width: 43%; padding: 5px;">
                    <label class="label-nfe">Cidade</label>
                    <input type="text" class="input-nfe" name="dest_xMun" id="dest_cidade">
                </div>
                <div style="width: 10%; padding: 5px;">
                    <label class="label-nfe">UF</label>
                    <input type="text" class="input-nfe" name="dest_UF" id="dest_estado">
                </div>
            </div>
            <div style="display: flex;">
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">Telefone</label>
                    <input type="text" class="input-nfe" name="dest_fone" id="dest_telefone">
                </div>
                <div style="width: 20%; padding: 5px;">
                    <label class="label-nfe">E-mail</label>
                    <input type="text" class="input-nfe" name="dest_email" id="dest_email">
                </div>
            </div>
        </div>
        <!-- Produtos -->
        <div class="grupo-nfe">
            <h3>Produto</h3>
            <div style="display: flex;">
                <div class="descricao-prod">
                    <label class="label-nfe">Descrição</label>
                    <select
                        class="produto-pesq"
                        id="prod_xProd"
                        data-url="<?= url('nfe/busca_produto') ?>"></select>
                </div>
                <div class="valores">
                    <label class="label-nfe">Quantidade</label>
                    <input type="number" class="input-nfe" min="0" step="1" name="prod_qtd" style="width: 100%;">
                </div>

                <div class="valores">
                    <label class="label-nfe">Valor Unitário</label>
                    <input type="number" id="unit" class="input-nfe" min="0.00" step="0.01" name="prod_valor" style="width: 100%;">
                </div>

                <div class="valores">
                    <label class="label-nfe">Total do Item</label>
                    <input type="number" class="input-nfe" min="0" step="0.01" name="prod_total" style="width: 100%;" readonly>
                </div>

                <div class="impostos">
                    <button id="impostos" class="btn btn-info" style="margin-top: 40px; height: 3.7vh;">Impostos</button>
                </div>

                <div class="adicionar">
                    <button id="add" class="btn btn-success" style="margin-top: 40px; height: 3.7vh;"><i class="fa fa-plus"></i></button>
                </div>

                <div id="inputs-ocultos"></div>
            </div>
        </div>
        <div style="display: none; margin-bottom: 20px; " id="impostos-div">
            <div class="tc-info" style="display: flex; width: 100%; padding: 10px;">
                <label class="tc-info-label" style="background-color: #fff;">ICMS</label>
                <div>
                    <label class="imp-label">CST</label>
                    <input type="text" class="cts">
                </div>
                <div>
                    <label class="imp-label">Base</label>
                    <input type="text" class="base">
                </div>
                <div>
                    <label class="imp-label">%</label>
                    <input type="text" class="percent">
                </div>
                <div>
                    <label class="imp-label">Valor</label>
                    <input type="text" class="valor-total">
                </div>
            </div>
            <div class="tc-info" style="display: flex; width: 100%; padding: 10px;">
                <label class="tc-info-label" style="background-color: #fff;">IBS</label>
                <div>
                    <label class="imp-label">CST</label>
                    <input type="text" class="cts">
                </div>

                <div>
                    <label class="imp-label">Base</label>
                    <input type="text" class="base">
                </div>

                <div>
                    <label class="imp-label">%</label>
                    <input type="text" class="percent">
                </div>

                <div>
                    <label class="imp-label">Valor</label>
                    <input type="text" class="valor-total">
                </div>
            </div>
            <div class="tc-info" style="display: flex; width: 100%; padding: 10px;">
                <label class="tc-info-label" style="background-color: #fff;">CBS</label>
                <div>
                    <label class="imp-label">CST</label>
                    <input type="text" class="cts">
                </div>

                <div>
                    <label class="imp-label">Base</label>
                    <input type="text" class="base">
                </div>

                <div>
                    <label class="imp-label">%</label>
                    <input type="text" class="percent">
                </div>

                <div>
                    <label class="imp-label">Valor</label>
                    <input type="text" class="valor-total">
                </div>
            </div>
            <div class="tc-info" style="display: flex; width: 75.5%; float: right; padding: 10px;">
                <label class="tc-info-label" style="background-color: #fff;">PIS</label>
                <div>
                    <label class="imp-label">Base</label>
                    <input type="text" class="base">
                </div>

                <div>
                    <label class="imp-label">%</label>
                    <input type="text" class="percent">
                </div>

                <div>
                    <label class="imp-label">Valor</label>
                    <input type="text" class="valor-total">
                </div>
            </div>
            <div class="tc-info" style="display: flex; width: 75.5%; float: right; padding: 10px;">
                <label class="tc-info-label" style="background-color: #fff;">COFINS</label>
                <div>
                    <label class="imp-label">Base</label>
                    <input type="text" class="base">
                </div>

                <div>
                    <label class="imp-label">%</label>
                    <input type="text" class="percent">
                </div>

                <div>
                    <label class="imp-label">Valor</label>
                    <input type="text" class="valor-total">
                </div>
            </div>
        </div>
        <div id="impostos-container"></div>
        <!-- Produtos Adicionados -->
        <div class="grupo-nfe">
            <h3 id="prod-nfe">Produtos Adicionados</h3>
            <table class="table table-striped" id="produtos-grid">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>V. Unit</th>
                        <th>V. Total</th>

                        <th>ICMS CST</th>
                        <th>ICMS BC</th>
                        <th>ICMS %</th>
                        <th>ICMS Vlr</th>

                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="produtos-tbody">
                </tbody>
            </table>
        </div>

        <!-- Totais -->
        <div class="grupo-nfe">
            <h3>Totais</h3>

            <div style="display: flex;">
                <div style="padding: 5px;">
                    <label class="label-nfe">Total dos Produtos</label>
                    <input type="number" class="input-nfe" step="0.01" name="total_produtos" readonly>
                </div>

                <div style="padding: 5px;">
                    <label class="label-nfe">Valor Total da Nota</label>
                    <input type="number" class="input-nfe" step="0.01" name="total_nota" readonly>
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="grupo-nfe">
            <h3>Observações</h3>
            <textarea name="obs" class="textarea-nfe" rows="4"></textarea>
        </div>

        <div style="display: flex; justify-content: space-between; ">
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gerar XML</button>
            <a href="<?= url("dash") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
        </div>

    </form>
</div>
<?php
$this->insert("tcsistemas.os/ordens/novocliCad", []);
?>