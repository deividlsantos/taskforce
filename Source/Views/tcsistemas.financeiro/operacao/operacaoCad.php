<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" id="form-operacao" action="<?= url("operacao/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("operacao") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_oper" name="id_oper" value="<?= ($operacao != "") ? ll_encode($operacao->id) : ''; ?>" hidden>
        <div class="oper-form">
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="descricao">Descricao:</label>
                    <input type="text" id="descricao" name="descricao" value="<?= ($operacao != "") ? $operacao->descricao : ''; ?>">
                </div>
            </div>
        </div>
    </form>
</div>