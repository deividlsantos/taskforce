<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <div class="container-telas">
        <div class="telas-group third-width">
            <label for="product-name">Código do produto:</label>
            <input type="text" id="product-name" name="product-name" required>
        </div>

        <div class="telas-group third-width">
            <label for="barcode">NCM:</label>
            <input type="text" id="ncm" name="ncm" required>
        </div>

        <div class="telas-group third-width">
            <label for="barcode">Situação tributária:</label>
            <input type="text" id="cest" name="cest" required>
        </div>

        <div class="telas-group full-width">
            <label for="address">Código de barras:</label>
            <input type="text" id="cean" name="cean" required>
        </div>

        <div class="telas-group full-width">
            <label for="address">Descrição do produto:</label>
            <input type="text" id="descprod" name="descprod" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="address">Unidade de medida:</label>
            <input type="text" id="undm" name="undm" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="address">Venda:</label>
            <input type="text" id="venda" name="venda" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="address">Estoque:</label>
            <input type="text" id="estq" name="estq" required>
        </div>

        <div class="telas-group quarter-width">
            <label for="address">Custo:</label>
            <input type="text" id="custo" name="custo" required>
        </div>

        <button type="button" class="btn-telas-primary">Cadastrar</button>
        <button type="button" class="btn-telas-danger">Voltar</button>
    </div>
</div>