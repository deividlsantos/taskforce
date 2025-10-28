<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" id="form-custogeral" action="<?= url("custogeral/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("custogeral") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_custogeral" name="id_custogeral" value="<?= ($custogeral != "") ? ll_encode($custogeral->id) : ''; ?>" hidden>
        <div class="custogeral-form">
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao" value="<?= ($custogeral != "") ? $custogeral->descricao : ''; ?>" required>
                </div>
                <div class="fcad-form-group coluna10">
                    <label for="percentual">Percentual:</label>
                    <input type="text" id="percentual" name="percentual" class="mask-money" value="<?= ($custogeral != "") ? moedaBR($custogeral->percentual) : ''; ?>">
                </div>
            </div>
        </div>
    </form>
</div>