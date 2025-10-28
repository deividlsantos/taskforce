<?php
$this->layout("_theme", $front);
?>

<div class="tfdash-container">
    <?php
    if ($front['user']->os == "X" || $front['user']->id_emp2 == 1):
    ?>
        <button type="button" id="atalhoPreOS" class="tfdash-card tfdash-card-golden">
            <div class="tfdash-icon"><i class="fa fa-rocket"></i></div>
            <div class="tfdash-title">Pré O.S.</div>
        </button>
    <?php
    endif;
    if ($front['user']->os == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("ordens"); ?>" class="tfdash-card tfdash-card-green">
            <div class="tfdash-icon"><i class="fa-regular fa-file-lines"></i></div>
            <div class="tfdash-title">Ordens de Serviço</div>
        </a>
    <?php
    endif;
    if ($front['user']->financeiro == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("dash-financeiro"); ?>" class="tfdash-card tfdash-card-red">
            <div class="tfdash-icon"><i class="fa-regular fa-money-bill-1"></i></div>
            <div class="tfdash-title">Financeiro</div>
        </a>
    <?php
    endif;
    if ($front['user']->ponto == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("ponto/fechamento"); ?>" class="tfdash-card tfdash-card-blue">
            <div class="tfdash-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="tfdash-title">Ponto</div>
        </a>
    <?php
    endif;
    if ($front['user']->cadastros == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("cadastros"); ?>" class="tfdash-card tfdash-card-gray">
            <div class="tfdash-icon"><i class="fa-regular fa-clipboard"></i></div>
            <div class="tfdash-title">Cadastros</div>
        </a>
    <?php
    endif;
    if ($front['user']->arquivos == "X" || $front['user']->id_emp2 == 1):
    ?>
        <a href="<?= url("files/lista"); ?>" class="tfdash-card tfdash-card-purple">
            <div class="tfdash-icon"><i class="fa-regular fa-floppy-disk"></i></div>
            <div class="tfdash-title">Arquivos</div>
        </a>
    <?php
    endif;
    ?>
</div>
<section>
    <?php
    $this->insert("tcsistemas.os/ordens/ordensModalPreOs", [
        "cliente" => $cliente,
        "servico" => $servico,
        "operador" => $operador,
        "produto" => $produto
    ]);
    ?>
</section>