<?php $this->layout('_theme', $front); ?>

<div class="nfeArea">
    <div class="container-nfe">
        <div class="boxFields">
            <h2 class="area-name">Formulário de Nota Fiscal</h2>

            <table cellpadding="0" cellspacing="0" class="boxNaturezaOperacao no-top" border="1">
                <tbody>
                    <tr>
                        <td>
                            <span class="nf-label">NATUREZA DA OPERAÇÃO</span>
                            <span class="info">
                                <select name="" id="" class="nfe-select" style="">
                                    <option value="">VENDA</option>
                                    <option value="">TRANSFERÊNCIA</option>
                                    <option value="">DEVOLUÇÃO</option>
                                </select>
                            </span>
                        </td>
                        <td style="width: 84.7mm;">
                            <span class="nf-label">PROTOCOLO DE AUTORIZAÇÃO DE USO:</span>
                            <span class="info">[ds_protocol]</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table cellpadding="0" cellspacing="0" class="boxInscricao no-top" border="1">
                <tbody>
                    <tr>
                        <td>
                            <span class="nf-label">INSCRIÇÃO ESTADUAL</span>
                            <span class="info"><input type="text" class="nfe-input" value="<?= $emp->ie ?>"></span>
                        </td>
                        <td style="width: 67.5mm;">
                            <span class="nf-label">INSCRIÇÃO ESTADUAL DO SUBST. TRIB.</span>
                            <span class="info">[nl_company_ie_st]</span>
                        </td>
                        <td style="width: 64.3mm">
                            <span class="nf-label">CNPJ</span>
                            <span class="info"><input type="text" class="nfe-cnpj" value="<?= $emp->cnpj ?>"></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="area-name">Destinatário / Remetente</p>
            <table cellpadding="0" cellspacing="0" class="boxDestinatario" border="1">
                <tbody>
                    <tr>
                        <td class="pd-0">
                            <table class="pull-up-1" cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="nf-label">NOME/RAZÃO SOCIAL</span>
                                            <span class="info"><select name="" id="cliente_busca" data-url="<?= url("nfe/cliente-busca") ?>"></select></span>
                                        </td>
                                        <td style="width: 60mm">
                                            <span class="nf-label">CNPJ/CPF</span>
                                            <span class="info"><input type="text" class="dest-cnpj" id="dest_cnpj"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width: 40mm">
                            <span class="nf-label">DATA DE EMISSÃO</span>
                            <span class="info"><input type="date" class="emissao-date" value="<?= date('Y-m-d') ?>"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pd-0">
                            <table class="pull-up-1" cellpadding="0" cellspacing="0" border="0">
                                <tbody>
                                    <tr>
                                        <td style="width: 150mm;">
                                            <span class="nf-label">ENDEREÇO</span>
                                            <span class="info"><input type="text" name="" id="dest_endereco" class="logradouro"></span>
                                        </td>
                                        <td style="width: 60mm;">
                                            <span class="nf-label">BAIRRO/DISTRITO</span>
                                            <span class="info"><input type="text" name="" id="dest_bairro" class="input-menores"></span>
                                        </td>
                                        <td style="width: 20 mm">
                                            <span class="nf-label">CEP</span>
                                            <span class="info"><input type="text" name="" id="dest_cep" class="input-menores"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <span class="nf-label">DATA DE SAÍDA</span>
                            <span class="info"><input type="date" class="emissao-date" value="<?= date('Y-m-d') ?>" ></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pd-0">
                            <table cellpadding="0" cellspacing="0" style="margin-bottom: -1px;" border="1">
                                <tbody>
                                    <tr>
                                        <td style="width: 60mm">
                                            <span class="nf-label">MUNICÍPIO</span>
                                            <span class="info"><input type="text" name="" id="dest_cidade" class="input-menores"></span>
                                        </td>
                                        <td style="width: 30mm">
                                            <span class="nf-label">UF</span>
                                            <span class="info"><input type="text" name="" id="dest_estado" class="input-menores"></span>
                                        </td>
                                        <td style="width: 40mm">
                                            <span class="nf-label">FONE/FAX</span>
                                            <span class="info"><input type="text" name="" id="dest_telefone" class="input-menores"></span>
                                        </td>
                                        <td style="width: 51mm">
                                            <span class="nf-label">INSCRIÇÃO ESTADUAL</span>
                                            <span class="info"><input type="text" name="" id="dest_ie" class="dest-cnpj"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <span class="nf-label">HORA DA SAÍDA</span>
                            <span id="info" class="info"><input type="time" class="emissao-date" value="<?= date('H:i') ?>"></span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="boxFatura">
                <p class="area-name">Fatura / Duplicata</p>
                <div class="wrapper-table">
                    <table cellpadding="0" cellspacing="0" border="1">
                        <tbody>
                            <tr>
                                <td>
                                    <span class="nf-label"></span>
                                    <span class="info"></span>
                                </td>
                                <td>
                                    <span class="nf-label"></span>
                                    <span class="info"></span>
                                </td>
                                <td>
                                    <span class="nf-label"></span>
                                    <span class="info"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="area-name">Calculo do imposto</p>
            <div class="wrapper-table">
                <table cellpadding="0" cellspacing="0" border="1" class="boxImposto">
                    <tbody>
                        <tr>
                            <td>
                                <span class="nf-label">BASE DE CÁLCULO DO ICMS</span>
                                <span class="info">[tot_bc_icms]</span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR DO ICMS</span>
                                <span class="info">[tot_icms]</span>
                            </td>
                            <td>
                                <span class="nf-label">BASE DE CÁLCULO DO ICMS S.T.</span>
                                <span class="info">[tot_bc_icms_st]</span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR DO ICMS SUBSTITUIÇÃO</span>
                                <span class="info">[tot_icms_st]</span>
                            </td>
                            <td>
                                <span class="nf-label">VLR ICMS DESON</span>
                                <span class="info"></span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR TOTAL DE PRODUTOS</span>
                                <span class="info" id="vl_total_prod">0.00</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="nf-label">VALOR DO FRETE</span>
                                <span class="info">[vl_shipping]</span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR DO SEGURO</span>
                                <span class="info">[vl_insurance]</span>
                            </td>
                            <td>
                                <span class="nf-label">DESCONTO</span>
                                <span class="info">[vl_discount]</span>
                            </td>
                            <td>
                                <span class="nf-label">OUTRAS DESP. ACESSÓRIAS</span>
                                <span class="info">[vl_other_expense]</span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR TOTAL DO I.P.I.</span>
                                <span class="info">[tot_total_ipi_tax]</span>
                            </td>
                            <td>
                                <span class="nf-label">VALOR TOTAL DA NOTA</span>
                                <span class="info">[vl_total]</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="area-name">Transportador / volumes transportados</p>
            <table cellpadding="0" cellspacing="0" border="1">
                <tbody>
                    <tr>
                        <td>
                            <span class="nf-label">NOME / RAZÃO SOCIAL</span>
                            <span class="info">[ds_transport_carrier_name]</span>
                        </td>
                        <td class="freteConta" style="width: 32mm">
                            <span class="nf-label">FRETE POR CONTA</span>
                            <span class="info">[ds_transport_code_shipping_type]</span>
                        </td>
                        <td style="width: 17.3mm">
                            <span class="nf-label">CÓDIGO ANTT</span>
                            <span class="info">[ds_transport_rntc]</span>
                        </td>
                        <td style="width: 24.5mm">
                            <span class="nf-label">PLACA DO VEÍCULO</span>
                            <span class="info">[ds_transport_vehicle_plate]</span>
                        </td>
                        <td style="width: 11.3mm">
                            <span class="nf-label">UF</span>
                            <span class="info">[ds_transport_vehicle_uf]</span>
                        </td>
                        <td style="width: 29.5mm">
                            <span class="nf-label">CNPJ / CPF</span>
                            <span class="info">[nl_transport_cnpj_cpf]</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table cellpadding="0" cellspacing="0" border="1" class="no-top">
                <tbody>
                    <tr>
                        <td class="field endereco">
                            <span class="nf-label">ENDEREÇO</span>
                            <span class="content-spacer info">[ds_transport_address]</span>
                        </td>
                        <td style="width: 32mm">
                            <span class="nf-label">MUNICÍPIO</span>
                            <span class="info">[ds_transport_city]</span>
                        </td>
                        <td style="width: 31mm">
                            <span class="nf-label">UF</span>
                            <span class="info">[ds_transport_uf]</span>
                        </td>
                        <td style="width: 51.4mm">
                            <span class="nf-label">INSCRIÇÃO ESTADUAL</span>
                            <span class="info">[ds_transport_ie]</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table cellpadding="0" cellspacing="0" border="1" class="no-top">
                <tbody>
                    <tr style="width: 100%">
                        <td style="width: 15%">
                            <span class="nf-label">QUANTIDADE</span>
                            <span class="content-spacer info" id="nu_transport_amount_transported_volumes">[]</span>
                        </td>
                        <td style="width: 15%">
                            <span class="nf-label">ESPÉCIE</span>
                            <span class="info" id="ds_transport_type_volumes_transported">[]</span>
                        </td>
                        <td style="width: 15%">
                            <span class="nf-label">MARCA</span>
                            <span class="info">[ds_transport_mark_volumes_transported]</span>
                        </td>
                        <td style="width: 15%">
                            <span class="nf-label">NUMERAÇÃO</span>
                            <span class="info">[ds_transport_number_volumes_transported]</span>
                        </td>
                        <td style="width: 15%">
                            <span class="nf-label">PESO BRUTO</span>
                            <span class="info">[vl_transport_gross_weight]</span>
                        </td>
                        <td style="width: 15%">
                            <span class="nf-label">PESO LÍQUIDO</span>
                            <span class="info">[vl_transport_net_weight]</span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="area-name">Dados do produto / serviço</p>
            <div class="wrapper-border">
                <div class="linha-produto">
                    <input list="listaProdutos" id="descricaoProduto" placeholder="Descrição do produto">
                    <datalist id="listaProdutos"></datalist>
                    <input type="number" id="quantidadeProduto" placeholder="Qtd" value="1" min="1">
                    <button class="nfe-btn verde" onclick="adicionarProduto()">Adicionar</button>
                </div>

                <table cellpadding="0" cellspacing="0" border="1" class="boxProdutoServico">
                    <thead class="listProdutoServico" id="table">
                        <tr class="titles">
                            <th rowspan="2" style="width: 15.5mm">CÓDIGO<br>PRODUTO</th>
                            <th rowspan="2" style="width: 66.1mm">DESCRIÇÃO DO PRODUTO / SERVIÇO</th>
                            <th rowspan="2">NCM / SH</th>
                            <th rowspan="2">CSOSN</th>
                            <th rowspan="2">CFOP</th>
                            <th rowspan="2">UNID</th>
                            <th rowspan="2">QUANT</th>
                            <th rowspan="2">VALOR<br>UNIT</th>
                            <th rowspan="2">VALOR<br>TOTAL</th>
                            <th rowspan="2">B.CÁLC<br>ICMS</th>
                            <th rowspan="2">VALOR<br>ICMS</th>
                            <th rowspan="2">VALOR<br>IPI</th>
                            <th colspan="2" style="background: linear-gradient(180deg, #f9fbfd 0%, #E3E4E5 100%) !important;border-bottom: 2px solid #e3e4e500;">ALIQUOTAS</th>
                        </tr>
                        <tr class="titles">
                            <th style="background: linear-gradient(180deg, #E3E4E5 0%, #c7c7c7 100%);">ICMS</th>
                            <th style="background: linear-gradient(180deg, #E3E4E5 0%, #c7c7c7 100%);">IPI</th>
                        </tr>
                    </thead>
                    <tbody id="itensNota"></tbody>
                </table>
            </div>
        </div>

        <div class="acoes-finais">
            <button type="button" class="nfe-btn vermelho" onclick="excluirNota()">Excluir</button>
            <button type="button" class="nfe-btn verde" onclick="enviarNota()">Enviar</button>
        </div>
    </div>
</div>

<?php
$this->insert("tcsistemas.os/ordens/novocliCad", []);
?>