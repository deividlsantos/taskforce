<?php
$this->layout("_theme", $front);
?>

<div class="d-flex flex-column">
    <div class="fcad-form-row">
        <div class="fcad-form-group coluna30">
            <a href="<?= url("financeirorel/pagar") ?>" class="btn btn-info"><i class="fa fa-filter"></i>Contas a Pagar</a>
        </div>
    </div>
    <div class="fcad-form-row" style="margin-top: 10px;">
        <div class="fcad-form-group coluna30">
            <a href="<?= url("financeirorel/receber") ?>" class="btn btn-info"><i class="fa fa-filter"></i>Contas a Receber</a>
        </div>
    </div>

</div>

</html>