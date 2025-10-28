<?php
$this->layout("_theme", $front);
?>

<div class="filters fcad-form-row" id="medicao-rel-form">
    <input type="hidden" id="url-medicao-rel" value="<?= url("relatorios/ordens"); ?>">
    <div class="fcad-form-group coluna15">
        <label for="filtrar-por">Filtrar por:</label>
        <select id="filtrar-por" class="filter-select" name="filtrar-por">
            <option value="" selected>Selecione um filtro</option>
            <option value="os">Ordens</option>
            <option value="obra"><?= $empresa->labelFiliais ?></option>
            <option value="cliente">Clientes</option>
            <option value="funcionario">Funcionários</option>
            <option value="servico" disabled>Serviços</option>
        </select>
    </div>

    <div class="fcad-form-group coluna20">
        <div class="periodo">
            <div class="fcad-form-group coluna20" id="filter-os">
                <label for="filter-ordem">Ordens</label>
                <select id="filter-ordem" class="" name="select-os">
                    <option value="" selected>Selecione um filtro</option>
                    <option value="filtro1">Filtro 1</option>
                    <option value="filtro2">Filtro 2</option>
                    <option value="filtro3">Filtro 3</option>
                </select>
            </div>
            <div class="fcad-form-group coluna20" id="filter-obra">
                <label for="filter-obras"><?= $empresa->labelFiliais ?></label>
                <select id="filter-obras" class="" name="select-obra">
                    <option value="0">TODAS</option>
                    <?php
                    if (!empty($obras)):
                        foreach ($obras as $obra):
                    ?>
                            <option value="<?= $obra->id; ?>"><?= $obra->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div class="fcad-form-group coluna20" id="filter-funcionario">
                <label for="filter-funcionarios">Funcionários</label>
                <select id="filter-funcionarios" class="" name="select-funcionario">
                    <option value="0">TODOS</option>
                    <?php
                    if (!empty($funcionarios)):
                        foreach ($funcionarios as $func):
                    ?>
                            <option value="<?= $func->id; ?>"><?= $func->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
            <div class="fcad-form-group coluna20" id="filter-servico">
                <label for="filter-servicos">Serviços</label>
                <select id="filter-servicos" class="" name="select-servico">
                    <option value="0" selected>Selecione um filtro</option>
                    <option value="filtro1">Filtro 1</option>
                    <option value="filtro2">Filtro 2</option>
                    <option value="filtro3">Filtro 3</option>
                </select>
            </div>
            <div class="fcad-form-group coluna20" id="filter-cliente">
                <label for="filter-clientes">Clientes</label>
                <select id="filter-clientes" class="" name="select-cliente">
                    <option value="0">TODOS</option>
                    <?php
                    if (!empty($clientes)):
                        foreach ($clientes as $cli):
                    ?>
                            <option value="<?= $cli->id; ?>"><?= $cli->nome; ?></option>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="fcad-form-group coluna30" id="radio-obra">
        <label>Agrupar:</label>
        <div class="fcad-radio-group">
            <label>
                <input type="radio" name="opcao" value="individual" checked> Individual
            </label>
            <label>
                <input type="radio" name="opcao" value="obra"> Por <?= $empresa->labelFiliais; ?>
            </label>
        </div>
    </div>

    <div class="fcad-form-group coluna10 direita">
        <label for="data-inicio">Início</label>
        <input type="date" class="date-input" id="data-inicio" name="data-inicio">
    </div>
    <div class="fcad-form-group coluna05" style="margin-left: -1%;">
        <label for="fcad-from-group" class="transparent">.</label>
        <span>até</span>
    </div>
    <div class="fcad-form-group coluna10" style="margin-left: -4%;">
        <label for="data-fim">Final</label>
        <input type="date" class="date-input" id="data-fim" name="data-fim">
    </div>

    <button id="gera-relatorio" class="btn btn-info"><i class="fa fa-search"></i></button>
    <div class="fcad-form-group coluna10 direita">
        <a href="<?= url("relatorios") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
    </div>
</div>
<div class="resultados-rel">

</div>
<section>
    <?php
    $this->insert("tcsistemas.os/relatorios/medicao-temapdf", []);
    ?>
</section>