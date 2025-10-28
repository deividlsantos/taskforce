<?php
$this->layout("_theme", $front);
?>

<div class="subdash-container">
    <?php
    if ($front['user']->cadastros == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("ent/cliente"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-handshake"></i></div>
            <div class="subdash-title">Clientes</div>
        </a>
    <?php
    endif;
    if ($front['user']->cadastros == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("ent/colaborador"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-user"></i></div>
            <div class="subdash-title">Colaboradores</div>
        </a>
    <?php
    endif;
    if ($front['user']->cadastros == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("ent/fornecedor"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="subdash-title">Fornecedores</div>
        </a>
    <?php
    endif;
    if ($front['user']->os == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("servico"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="subdash-title">Serviços</div>
        </a>
    <?php
    endif;
    ?>
    <a href="<?= url("obras"); ?>" class="subdash-card subdash-card-gray">
        <div class="subdash-icon"><i class="fa-solid <?= $empresa->iconeLabel ?>"></i></div>
        <div class="subdash-title"><?= $empresa->labelFiliais; ?></div>
    </a>
    <?php
    if ($front['user']->os == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("materiais"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-box-archive"></i></div>
            <div class="subdash-title">Produtos/Materiais</div>
        </a>
    <?php
    endif;
    if (($front['user']->os == "X" || $front['user']->id_emp2 == 1) && $empresa->servicosComEquipamentos == "X"):
    ?>
        <a href="<?= url("equipamentos"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-truck"></i></div>
            <div class="subdash-title">Equipamentos</div>
        </a>
    <?php
    endif;
    if (($front['user']->os == "X" || $front['user']->id_emp2 == 1)) :
    ?>
        <a href="<?= url("tipo"); ?>" class="subdash-card subdash-card-gray">
            <div class="subdash-icon"><i class="fa-solid fa-tag"></i></div>
            <div class="subdash-title">Tipos de OS</div>
        </a>
    <?php
    endif;
    ?>
</div>