<?php $this->layout('_theme', $front); ?>
<style>
    body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    background: #fff;
}

.danfe {
    width: 800px;
    margin: auto;
}

.bloco {
    border: 1px solid #000;
    margin-bottom: 4px;
}

.recibo {
    display: flex;
    gap: 4px;
    margin-bottom: 4px;
}

.recibo-texto {
    flex: 1;
    border: 1px solid #000;
    padding: 4px;
}

.recibo-linhas {
    display: flex;
    margin-top: 10px;
}

.recibo-linhas div {
    flex: 1;
    border-top: 1px solid #000;
    height: 25px;
}

.recibo-num {
    width: 120px;
    border: 1px solid #000;
    text-align: center;
    font-weight: bold;
}

.topo {
    display: flex;
}

.logo {
    width: 25%;
    text-align: center;
    padding-top: 25px;
    border-right: 1px solid #000;
}

.danfe-info {
    width: 30%;
    text-align: center;
    border-right: 1px solid #000;
}

.danfe-info h1 {
    margin: 0;
    font-size: 18px;
}

.barcode {
    width: 45%;
    padding: 4px;
}

.codigo-barras {
    height: 90px;
    border: 1px solid #000;
    margin-bottom: 4px;
}

.chave {
    font-size: 10px;
}

.grid-emitente {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
}

.grid-emitente div,
.grid-4 div {
    border-right: 1px solid #000;
    padding: 4px;
}

.grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}

.titulo {
    font-weight: bold;
    background: #eee;
    padding: 2px;
    border-bottom: 1px solid #000;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #000;
    padding: 3px;
    text-align: center;
}

.obs {
    height: 60px;
    padding: 4px;
}

.rodape {
    text-align: center;
    font-weight: bold;
    color: red;
    margin-top: 4px;
}

</style>
<div class="danfe">

<!-- RECIBO -->
<div class="recibo">
    <div class="recibo-texto">
        RECEBEMOS DE <strong><?= $emp->razao ?></strong> OS PRODUTOS CONSTANTES DA NOTA FISCAL INDICADA AO LADO
        <div class="recibo-linhas">
            <div>Data de recebimento</div>
            <div>Identificação e assinatura do recebedor</div>
        </div>
    </div>
    <div class="recibo-num">
        <strong>NF-e</strong><br>
        Nº 0000175<br>
        Série 1
    </div>
</div>

<!-- TOPO -->
<div class="bloco topo">
    <div class="logo">
        <strong><?= $emp->razao ?></strong><br>
        CNPJ: <?= $emp->cnpj ?><br>
        <?= $emp->endereco ?> - <?= $emp->cidade ?> - <?= $emp->uf ?><br>
        CEP: <?= $emp->cep ?> - Fone: <?= $emp->fone1 ?>
    </div>

    <div class="danfe-info">
        <h1>DANFE</h1>
        Documento Auxiliar da<br>
        Nota Fiscal Eletrônica<br><br>
        <strong>Entrada</strong> ☐ &nbsp; <strong>Saída</strong> ☒<br><br>
        Nº <strong>0000175</strong><br>
        SÉRIE <strong>1</strong><br>
        Página 1 de 1
    </div>

    <div class="barcode">
        <div class="codigo-barras"><img src="<?= url("Source/Views/tcsistemas.nfe/barra.png") ?>" style="width: 90%; margin: 20px auto 0 15px;"></div>
        <div class="protocolo">
            Número de protocolo de autorização de uso da NF-e<br>
            <strong>DOCUMENTO SEM VALOR FISCAL</strong>
        </div>
        <div class="chave">
            Chave de acesso da NF-e – consulte no site www.fazenda.gov.br<br>
            43.0908.9062.7936.0001.30.55.0010.0000.1750.0089.6536
        </div>
    </div>
</div>

<!-- EMITENTE -->
<div class="bloco grid-emitente">
    <div>
        <strong>Natureza da operação</strong><br>
        Venda de mercadorias
    </div>
    <div>
        <strong>Inscrição Estadual</strong><br>
        010.00000
    </div>
    <div>
        <strong>CNPJ</strong><br>
        <?= $emp->cnpj ?>
    </div>
</div>

<!-- DESTINATÁRIO -->
<div class="bloco">
    <div class="titulo">Destinatário / Remetente</div>

    <div class="grid-4">
        <div><strong>Nome / Razão Social</strong><br> <?= $ent->nome ?></div>
        <div><strong>CNPJ</strong><br><?= $ent->cpfcnpj ?></div>
        <div><strong>Inscrição Estadual</strong><br><?= $ent->inscrg ?></div>
        <div><strong>Data emissão</strong><br><?= date('d/m/Y') ?></div>
    </div>

    <div class="grid-4">
        <div><strong>Endereço</strong><br><?= $ent->endereco ?></div>
        <div><strong>Bairro</strong><br><?= $ent->bairro ?></div>
        <div><strong>CEP</strong><br><?= $ent->cep ?></div>
        <div><strong>Data saída</strong><br><?= date('d/m/Y') ?></div>
    </div>

    <div class="grid-4">
        <div><strong>Município</strong><br><?= $ent->cidade ?></div>
        <div><strong>Fone/Fax</strong><br><?= $ent->fone1 ?></div>
        <div><strong>UF</strong><br><?= $ent->uf ?></div>
        <div><strong>Hora saída</strong><br> - </div>
    </div>
</div>

<!-- FATURAS -->
<div class="bloco">
    <div class="titulo">Faturas</div>
    <table>
        <tr>
            <th>Número</th>
            <th>Vencimento</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>000175/1</td>
            <td>06/09/2009</td>
            <td>76,66</td>
        </tr>
    </table>
</div>

<!-- CÁLCULO DO IMPOSTO -->
<div class="bloco">
    <div class="titulo">Cálculo do imposto</div>
    <table>
        <tr>
            <th>Base ICMS</th>
            <th>Valor ICMS</th>
            <th>Base ICMS ST</th>
            <th>Valor ICMS ST</th>
            <th>Valor produtos</th>
        </tr>
        <tr>
            <td>230,00</td>
            <td>27,60</td>
            <td>0,00</td>
            <td>0,00</td>
            <td>230,00</td>
        </tr>
    </table>
</div>

<!-- TRANSPORTADOR -->
<div class="bloco">
    <div class="titulo">Transportador / Volumes transportados</div>
    <table>
        <tr>
            <th>Nome</th>
            <th>Frete por conta</th>
            <th>Código ANTT</th>
            <th>Placa</th>
            <th>UF</th>
            <th>CNPJ/CPF</th>
        </tr>
        <tr>
            <td>Transportes Valdemar</td>
            <td>1 - Emitente</td>
            <td></td>
            <td>HLT-7746</td>
            <td>RS</td>
            <td>00000000000000</td>
        </tr>
    </table>
</div>

<!-- ITENS -->
<div class="bloco">
    <div class="titulo">Itens da nota fiscal</div>
    <table>
        <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>NCM</th>
            <th>CST</th>
            <th>CFOP</th>
            <th>UN</th>
            <th>Qtde</th>
            <th>Vlr Unit</th>
            <th>Vlr Total</th>
            <th>BC ICMS</th>
            <th>Vlr ICMS</th>
            <th>% ICMS</th>
            <th>% IPI</th>
        </tr>
        <tr>
            <td>FL389</td>
            <td>Lâmpada dicróica</td>
            <td>10000000</td>
            <td>100</td>
            <td>5102</td>
            <td>Cx</td>
            <td>4</td>
            <td>45,00</td>
            <td>180,00</td>
            <td>180,00</td>
            <td>21,60</td>
            <td>12,00</td>
            <td>0,00</td>
        </tr>
    </table>
</div>

<!-- ISSQN -->
<div class="bloco">
    <div class="titulo">Cálculo do ISSQN</div>
    <table>
        <tr>
            <th>Inscrição Municipal</th>
            <th>Valor serviços</th>
            <th>Base ISSQN</th>
            <th>Valor ISSQN</th>
        </tr>
        <tr>
            <td></td>
            <td>0,00</td>
            <td>0,00</td>
            <td>0,00</td>
        </tr>
    </table>
</div>

<!-- DADOS ADICIONAIS -->
<div class="bloco">
    <div class="titulo">Dados adicionais</div>
    <div class="obs">
        EXEMPLO
    </div>
</div>

<div class="rodape">
    Ambiente de HOMOLOGAÇÃO – Documento sem valor fiscal
</div>