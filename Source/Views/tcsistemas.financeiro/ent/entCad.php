<?php
$this->layout("_theme", $front);
?>

<div class="telas-body">
    <form class="form-cadastros" id="form-entidades" action="<?= url("ent/salvar") ?>">
        <div class="fcad-form-row form-buttons">
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
            <a href="<?= url("ent/" . $uri) ?>" class="btn btn-info direita" id="btnEntVoltar"><i class="fa fa-undo"></i> Voltar</a>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.financeiro/ent/entForm", [
                "front" => $front,
                "ent" => $ent,
                "entFilha" => $entFilha,
                "tipo" => $tipo,
                "uri" => $uri,
                "bank" => $bank,
                "turno" => $turno,
                "arquivos" => $arquivos,
                "id_emp" => $id_emp
            ]);
            ?>
        </section>
        <div class="fcad-form-row form-buttons">
            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
            <a href="<?= url("ent/" . $uri) ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
    </form>
</div>