<?php
$this->layout("_theme_oper", $front);
?>
<input id="url-acoes" value="<?= url("oper_ordens/activity") ?>" hidden>
<div class="days-wrapper">
    <div class="fcad-form-row data-dashopermob">
        <div class="date-display"></div>
        <div class="selector-container">
            <button type="button" class="arrow left-arrow arrow-dashopermob" data-target="#month-selector" data-direction="prev"><i class="fa-solid fa-chevron-left"></i></button>
            <select class="select-dashopermob" data-url="<?= url("oper_calendario") ?>" id="month-selector">
                <?php foreach ($meses as $number => $name): ?>
                    <option value="<?= $number ?>" <?= $number == (int)date('n') ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="arrow right-arrow arrow-dashopermob" data-target="#month-selector" data-direction="next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <div class="selector-container">
            <button type="button" class="arrow left-arrow arrow-dashopermob" data-target="#year-selector" data-direction="prev"><i class="fa-solid fa-chevron-left"></i></button>
            <select class="select-dashopermob" id="year-selector">
                <?php for ($year = date('Y') - 5; $year <= date('Y') + 5; $year++): ?>
                    <option value="<?= $year ?>" <?= $year == date('Y') ? 'selected' : '' ?>>
                        <?= $year ?>
                    </option>
                <?php endfor; ?>
            </select>
            <button type="button" class="arrow right-arrow arrow-dashopermob" data-target="#year-selector" data-direction="next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>
    <div class="days-selector">
        <!-- Dois últimos dias do mês anterior -->
        <?php foreach ($lastTwoDaysPrevMonth as $day): ?>
            <div class="day other-month" data-day="<?= $day ?>">
                <span><?= $day ?></span>
            </div>
        <?php endforeach; ?>

        <!-- Dias do mês atual -->
        <?php

        for ($day = 1; $day <= date('t', strtotime("$currentYear-$currentMonth-01")); $day++): ?>
            <div class="day this-month" data-day="<?= str_pad($day, 2, '0', STR_PAD_LEFT) ?>" data-month="<?= $currentMonth ?>" data-year="<?= $currentYear ?>"><span><?= $day ?></span></div>
        <?php endfor; ?>

        <!-- Dois primeiros dias do próximo mês -->
        <?php foreach ($firstTwoDaysNextMonth as $day): ?>
            <div class="day other-month" data-day="<?= $day ?>">
                <span><?= $day ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Conteúdos dos dias -->
<div class="days-content scrollabe-container" id="days-content">
    <?php
    if (!empty($ordensPorDia)):
        foreach ($ordensPorDia as $container):
    ?>
            <div class="content" id="content-<?= $container->dataexec; ?>">
                <?php
                foreach ($os2 as $os) :
                    $dataStatus = "";
                    if ($os->status == 'A') {
                        $dataStatus = (date_fmt($os->dataexec, "Y-m-d") > date("Y-m-d")) ? "futuras" : "pendentes";
                    } elseif ($os->status == 'I') {
                        $dataStatus = "andamento";
                    } elseif ($os->status == 'P') {
                        $dataStatus = "pausadas";
                    } elseif ($os->status == 'C') {
                        $dataStatus = "concluidas";
                    } elseif ($os->status == 'D') {
                        $dataStatus = "canceladas";
                    }
                    if ($container->dataexec == date_fmt($os->dataexec, "Y-m-d")) :
                ?>
                        <div class="mob-item" data-status="<?= $dataStatus ?>" data-date="<?= date_fmt($os->dataexec, "Y-m-d"); ?>" data-id="<?= $os->id; ?>" data-url="<?= url("oper_ordens/ordem"); ?>">
                            <div class="barra-status barra-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"></div>
                            <div class="mob-item-content">
                                <div class="mob-info fcad-form-row">
                                    <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?>"><?= date_fmt($os->dataexec, "d/m/Y")  . " (#" . $os->id . ")"; ?></span>
                                    <span class="mob-txt txt-<?= $dataStatus == "pendentes" ? "red" : cor_status($dataStatus); ?> direita"><?= secondsToTime($os->horaexec); ?></span>
                                </div>
                                <div class="mob-info fcad-form-row">
                                    <span class="mob-stxt coluna20">OS: <?= $os->id_os1; ?></span>
                                    <span class="mob-stxt direita">
                                    <?= $dataStatus == 'andamento' ? 'EM ANDAMENTO' : ($dataStatus == 'pausadas' ? 'PAUSADA' : ($dataStatus == 'pendentes' ? 'AGUARDANDO INÍCIO' : ($dataStatus == 'concluidas' ? 'CONCLUÍDA' : ($dataStatus == 'futuras' ? 'AGUARDANDO INÍCIO' : 'CANCELADA')))); ?>
                                    </span>
                                </div>
                                <div class="mob-info fcad-form-row">
                                    <div class="mob-stxt"><?= $os->cliente; ?></div>
                                </div>
                                <div class="mob-info fcad-form-row">
                                    <div class="mob-stxt"><?= $os->servico; ?></div>
                                </div>
                            </div>
                        </div>
                <?php
                    endif;
                endforeach;

                ?>

            </div>
        <?php
        endforeach;
        ?>
    <?php
    endif;
    ?>
    <div class="content no-event" id="content-default">

    </div>

</div>

<section>
    <?php
    $this->insert("ordens/ordemMob", [
        "operador" => $operador,
        "servico" => $servico,
        "equipamentos" => $equipamentos,
        "empresa" => $empresa
    ]);
    ?>
</section>