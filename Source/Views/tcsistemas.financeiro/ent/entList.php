<?php
$this->layout("_theme", $front);
?>

<div class="container-fl-review">
    <div class="fcad-form-row form-buttons">
        <a href="<?= url("ent/form") ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
        <a href="<?= url("dash") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
    </div>
    <div class="input-filtrar">
        <div class="fcad-form-row">
            <div class="fcad-form-group coluna10">
                <label>Filtrar:</label>
            </div>
            <div style="margin-left:-50px;" class="fcad-form-group coluna30">
                <input type="text" id="filtrarEnt" name="filtrar" value="">
            </div>
            <?php
            if ($tipo == 3):
            ?>
                <div class="fcad-radio-group coluna20 direita">
                    <label>
                        <input type="radio" name="ent-status" value="ativos" checked> Ativos
                    </label>
                    <label>
                        <input type="radio" name="ent-status" value="inativos"> Inativos
                    </label>
                </div>
            <?php
            endif;
            ?>
        </div>
    </div>
    <div class="ent-ativos">
        <?php $this->insert("tcsistemas.financeiro/ent/entListTabResults", [
            "ent" => $ent,
            "tipo" => $tipo,
            "filha" => $filha,
            "idTable" => "ent-list",
            "ativar" => false
        ]); ?>
    </div>
    <div class="ent-inativos" style="display:none;">
        <?php $this->insert("tcsistemas.financeiro/ent/entListTabResults", [
            "ent" => $entInativos,
            "tipo" => $tipo,
            "filha" => $filha,
            "idTable" => "ent-list-inativos",
            "ativar" => true
        ]); ?>
    </div>
</div>

</html>