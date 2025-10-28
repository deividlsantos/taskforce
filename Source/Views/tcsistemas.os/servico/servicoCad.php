<?php
$this->layout("_theme", $front);
?>
<div class="telas-body">
    <form class="form-cadastros" style="width: 80%;" id="form-servico" action="<?= url("servico/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("servico") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.os/servico/servicoForm", [
                "front" => $front,
                "servico" => $servico,
                "empresa" => $empresa,
                "plconta" => $plconta,
                "recorrencias" => $recorrencias
            ]);
            ?>
        </section>
    </form>
</div>