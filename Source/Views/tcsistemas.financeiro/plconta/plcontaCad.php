<?php
$this->layout("_theme", $front);
?>


<div class="telas-body d-flex">
    <form class="form-cadastros coluna50" id="form-plconta" action="<?= url("plconta/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("plconta") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.financeiro/plconta/plcontaForm", [
                "plconta" => $plconta
            ]);
            ?>
        </section>
    </form>
</div>