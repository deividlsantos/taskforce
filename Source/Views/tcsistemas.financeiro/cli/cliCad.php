<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <div class="container-telas">

        <div class="checkbox-group">
            <label>Cliente Ativo:</label>
            <label><input type="checkbox" id="active-yes" name="active" value="sim"> Sim</label>
        </div>

        <div class="telas-group full-width">
            <label for="razao">Razão:</label>
            <input type="text" id="razao" name="razao" required>
        </div>

        <div class="telas-group full-width">
            <label for="barcode">Fantasia:</label>
            <input type="text" id="barcode" name="barcode" required>
        </div>

        <div class="telas-group full-width">
            <label for="barcode">Logadouro:</label>
            <input type="text" id="address" name="address" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="barcode">Número:</label>
            <input type="text" id="number" name="number" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="barcode">Complemento:</label>
            <input type="text" id="complemento" name="complemento" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="barcode">Bairro:</label>
            <input type="text" id="bairro" name="bairro" required>
        </div>


        <div class="telas-group quarter-width">
            <label for="barcode">Cep:</label>
            <input type="text" id="cep" name="cep" required>
        </div>

        <div class="telas-group third-width">
            <label for="occupation">Código do Municipio:</label>
            <input type="text" id="codigomnc" name="codigomnc">
        </div>

        <div class="telas-group third-width">
            <label for="occupation">Cidade:</label>
            <input type="text" id="cidade" name="cidade">
        </div>

        <div class="telas-group third-width">
            <label for="issuer">Estado:</label>
            <select id="issuer" name="issuer" class="issuer-select" required>
                <option value="">Selecione</option>
                <option value="AC">AC</option>
                <option value="AL">AL</option>
                <option value="AP">AP</option>
                <option value="AM">AM</option>
                <option value="BA">BA</option>
                <option value="CE">CE</option>
                <option value="DF">DF</option>
                <option value="ES">ES</option>
                <option value="GO">GO</option>
                <option value="MA">MA</option>
                <option value="MT">MT</option>
                <option value="MS">MS</option>
                <option value="MG">MG</option>
                <option value="PA">PA</option>
                <option value="PB">PB</option>
                <option value="PR">PR</option>
                <option value="PE">PE</option>
                <option value="PI">PI</option>
                <option value="RJ">RJ</option>
                <option value="RN">RN</option>
                <option value="RS">RS</option>
                <option value="RO">RO</option>
                <option value="RR">RR</option>
                <option value="SC">SC</option>
                <option value="SP">SP</option>
                <option value="SE">SE</option>
                <option value="TO">TO</option>
            </select>
        </div>

        <div class="telas-group third-width">
            <label for="occupation">Fone:</label>
            <input type="text" id="phone" name="phone">
        </div>

        <div class="telas-group third-width">
            <label for="occupation">Celular:</label>
            <input type="text" id="phone" name="phone">
        </div>

        <div class="telas-group third-width">
            <label for="occupation">Email:</label>
            <input type="text" id="email" name="email">
        </div>

        <button type="button" class="btn-telas-primary">Cadastrar</button>
        <button type="button" class="btn-telas-danger">Voltar</button>
    </div>
    
</div>