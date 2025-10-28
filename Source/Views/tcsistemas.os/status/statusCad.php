<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" style="width: 80%;" id="form-status" action="<?= url("status/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("status") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <input type="text" id="id_status" name="id_status" value="<?= ($status != "") ? ll_encode($status->id) : ''; ?>" hidden>
        <div class="status-form">
            <div class="fcad-form-row ">
                <div class="fcad-form-group coluna40">
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao" value="<?= ($status != "") ? $status->descricao : ''; ?>" required readonly>
                </div>
                <div class="fcad-form-group coluna10">
                    <label for="cor">Cor:</label>
                    <input type="color" id="cor" name="cor" value="<?= ($status != "") ? $status->cor : ''; ?>">
                </div>
            </div>
        </div>
    </form>
</div>