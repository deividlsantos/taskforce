<?php
$this->layout("_theme_oper", $front);
?>

<!-- Barra de ícones superior -->
<div class="top-icons">
    <div class="left-icons">
        <!-- 
        <div class="icon whatsapp">
            <i class="fab fa-whatsapp"></i>
        </div>
        <div class="icon instagram">
            <i class="fab fa-instagram"></i>
        </div>
        <div class="icon phone">
            <i class="fas fa-phone-alt"></i>
        </div>
        <div class="icon email">
            <i class="fas fa-at"></i>
        </div>-->
    </div>
    <div class="right-icons">
        <a class="icon user" href="<?= url("oper_dash/oper") ?>">
            <i class="fas fa-user"></i>
        </a>
        <a class="icon logout" href="<?= url("logout") ?>">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>
</div>

<!-- Mensagem de boas-vindas -->
<h2 class="welcome"><?= $front['secTit']; ?>!</h2>

<div class="dash-content1" id="dash-content1">
    <a href="<?= !empty($andamento) ? url("oper_ordens/andamento") : "#"; ?>" class="dash-card1 dash-card-default" id="card-tarefa-atual">
        <div class="dash-bar1">Tarefa(s) em Andamento</div>
        <i class="">
            <?php
            if (!empty($andamento)) :
                $count = 0;
                foreach ($andamento as $vlrA) :
                    if ($count >= 3) break;
            ?>
                    <span><?= "#" . $vlrA->id; ?></span>
                <?php
                    $count++;
                endforeach;
                if (count($andamento) > 3) :
                ?>
                    <span>...</span>
                <?php
                endif;
            else :
                ?>
                <span style="font-size: 0.5em;">Nenhuma Tarefa em andamento!</span>
            <?php
            endif;
            ?>
        </i>
    </a>
</div>

<div class="dash-content2 scrollable-container" id="dash-content2">
    <a href="<?= !empty($pausadas) ? url("oper_ordens/pausadas") : "#"; ?>" class="dash-card2 dash-card-orange" id="card-pausadas">
        <div class="dash-bar">Pausadas</div>
        <i class="fa-solid fa-circle-pause">
            <?php
            if (!empty($pausadas)) :
            ?>
                <span><?= count($pausadas); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </a>
    <a href="<?= !empty($pendentes) ? url("oper_ordens/pendentes") : "#"; ?>" class="dash-card2 dash-card-red" id="card-pendentes">
        <div class="dash-bar">Pendentes</div>
        <i class="fas fa-hourglass-half">
            <?php
            if (!empty($pendentes)) :
            ?>
                <span><?= count($pendentes); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </a>
    <a href="<?= !empty($futuras) ? url("oper_ordens/futuras") : "#"; ?>" class="dash-card2 dash-card-blue" id="card-futuras">
        <div class="dash-bar">Futuras</div>
        <i class="fas fa-calendar-alt">
            <?php
            if (!empty($futuras)) :
            ?>
                <span><?= count($futuras); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </a>
    <a href="<?= !empty($concluidas) ? url("oper_ordens/concluidas") : "#"; ?>" class="dash-card2 dash-card-green" id="card-concluidas">
        <div class="dash-bar">Concluídas</div>
        <i class="fa-solid fa-circle-check">
            <?php
            if (!empty($concluidas)) :
            ?>
                <span><?= count($concluidas); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </a>
    <a href="<?= !empty($canceladas) ? url("oper_ordens/canceladas") : "#"; ?>" class="dash-card2 dash-card-purple" id="card-canceladas">
        <div class="dash-bar">Canceladas</div>
        <i class="fa-solid fa-circle-xmark">
            <?php
            if (!empty($canceladas)) :
            ?>
                <span><?= count($canceladas); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </a>
    <div class="dash-card2 dash-card-brown">
        <div class="dash-bar">Total de Tarefas</div>
        <i class="fa-solid fa-circle-exclamation">
            <?php
            if (!empty($os2)) :
            ?>
                <span><?= count($os2); ?></span>
            <?php
            else :
            ?>
                <span>0</span>
            <?php
            endif;
            ?>
        </i>
    </div>
</div>

<!-- Ícones roláveis na parte inferior -->
<div class="bottom-icons">
    <div class="icon-card">
        <a id="ico-os" href="<?= url("oper_ordens") ?>">
            <i class="fas fa-screwdriver-wrench"></i>
            <p>Tarefas</p>
        </a>
    </div>
    <div class="icon-card">
        <a id="ico-calendario" href="<?= url("oper_calendario") ?>">
            <i class="fas fa-calendar"></i>
            <p>Calendário</p>
        </a>
    </div>
    <div class="icon-card">
        <a id="ico-os1" href="<?= url("oper_os1") ?>">
            <i class="fas fa-clipboard"></i>
            <p>Ordens</p>
        </a>
    </div>
    <?php
    if ($empresa->servicosComEquipamentos == 'X' && $empresa->confirmaMovimentacaoEstoque == 'X') :
    ?>
        <div class="icon-card">
            <a id="ico-mov" href="<?= url("oper_mov") ?>">
                <i class="fas fa-right-left"></i>
                <?php
                if (!empty($solicitacoes) && count($solicitacoes) > 0) :
                ?>
                    <span class="badge"><?= count($solicitacoes); ?></span>
                <?php
                endif;
                ?>
                <p>Mov.<br>Ferramentas</p>
            </a>
        </div>
    <?php
    endif;
    ?>
    <div class="icon-card">
        <a id="ico-logout" href="<?= url("logout") ?>">
            <i class="fas fa-right-from-bracket"></i>
            <p>Log Out</p>
        </a>
    </div>
    <!-- Adicione mais ícones conforme necessário -->
</div>