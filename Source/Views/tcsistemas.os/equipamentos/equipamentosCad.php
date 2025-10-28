<?php
$this->layout("_theme", $front);
?>

<div class="telas-body">
    <form class="form-cadastros" id="form-equipamentos" action="<?= url("equipamentos/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("equipamentos") ?>" class="btn btn-info direita" id="bntEntVoltar"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.os/equipamentos/equipamentosForm", [
                "equipamento" => $equipamento,
                "status" => $status,
                "classeEquipamento" => $classeEquipamento,
                "classeOperacional" => $classeOperacional,
                "especieEquipamento" => $especieEquipamento,
                "cliente" => $cliente,
                "plconta" => $plconta
            ]);
            ?>
        </section>
        <section>
            <?php
            $this->insert("tcsistemas.os/equipamentos/equipamentosChklistModal", [
                "equipamento" => $equipamento,
                "gruposChklist" => $gruposChklist,
                "itensChklist" => $itensChklist
            ]);
            ?>
        </section>
    </form>
</div>