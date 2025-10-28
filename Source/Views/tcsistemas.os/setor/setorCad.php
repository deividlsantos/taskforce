<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" id="form-setor" action="<?= url("setor/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("setor") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_setor" name="id_setor" value="<?= ($setor != "") ? ll_encode($setor->id) : ''; ?>" hidden>
        <div class="setor-form">
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao" value="<?= ($setor != "") ? $setor->descricao : ''; ?>" required>
                </div>
            </div>
        </div>
    </form>
</div>