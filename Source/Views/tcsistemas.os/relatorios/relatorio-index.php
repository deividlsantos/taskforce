<?php
$this->layout("_theme", $front);
?>

<div class="d-flex flex-column">
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna30">
            <a href="<?= url("relatorios/ordens") ?>" class="btn btn-info"><i class="fa fa-filter"></i>Relatórios de Medições</a>
        </div>
    </div>
    <div class="fcad-form-row" style="margin-top: 10px;">
        <div class="fcad-form-group coluna30">
            <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#accordionRelatorios" aria-expanded="false" aria-controls="accordionRelatorios">
                <i class="fa fa-filter"></i> Relatórios de Serviços
            </button>
        </div>
    </div>
    <div class="fcad-form-row">
        <div class="collapse" id="accordionRelatorios" style="margin-top: 10px;">
            <div class="card card-body">
                <a href="<?= url("os2rel") ?>" class="btn btn-secondary" style="margin-bottom: 5px;">Relatório por Operador</a>
                <a href="<?= url("servicosrel") ?>" class="btn btn-secondary">Relatório por Cliente</a>
            </div>
        </div>
    </div>
</div>

</html>