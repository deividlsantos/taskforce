<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" style="width: 80%;" id="form-materiais" action="<?= url("materiais/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("materiais") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_materiais" name="id_materiais" value="<?= ($materiais != "") ? ll_encode($materiais->id) : ''; ?>" hidden>
        <div class="materiais-form">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna60">
                    <label for="descricao">Descrição:</label>
                    <input id="descricao" name="descricao" type="text" value="<?= ($materiais != "") ? $materiais->descricao : ''; ?>" required>
                </div>
                <div class="fcad-form-group">
                    <label for="unidade">Unidade:</label>
                    <input type="text" id="unidade" name="unidade" value="<?= ($materiais != "") ? $materiais->unidade : ''; ?>" required>
                </div>
                <div class="fcad-form-group">
                    <label for="custo">Custo:</label>
                    <input type="text" id="custo" name="custo" class="mask-money" value="<?= ($materiais != "") ? moedaBR($materiais->custo) : ''; ?>" required>
                </div>
                <div class="fcad-form-group">
                    <label for="valor">Valor:</label>
                    <input type="text" id="valor" name="valor" class="mask-money" value="<?= ($materiais != "") ? moedaBR($materiais->valor) : ''; ?>" required>
                </div>
            </div>
        </div>
    </form>
</div>