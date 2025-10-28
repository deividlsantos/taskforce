<?php
$this->layout("_theme", $front);
?>

<div class="container d-flex align-items-start">
    <div class="d-flex flex-column coluna15" style="margin-top: 2%;">
        <div class="fcad-form-row">
            <div class="fcad-form-group">
                <a href="<?= url("checklist/grupos") ?>" class="btn btn-info"><i class="fa fa-layer-group"></i> Grupos</a>
            </div>
        </div>
        <div class="fcad-form-row" style="margin-top: 10px;">
            <div class="fcad-form-group">
                <a href="<?= url("checklist/itens") ?>" class="btn btn-info"><i class="fa fa-list-check"></i> Itens</a>
            </div>
        </div>
    </div>
</div>


</html>