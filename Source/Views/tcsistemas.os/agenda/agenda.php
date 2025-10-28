<?php
$this->layout("_theme", $front);
?>

<head>
    <script src="<?= url("Source/Views/js/calendar.js"); ?>"></script>
</head>

<div id='calendar'></div>

<div id="dataContainer"
    data-os1='<?= htmlspecialchars(json_encode($dataos1), ENT_QUOTES, 'UTF-8') ?>'
    data-os2='<?= htmlspecialchars(json_encode($dataos2), ENT_QUOTES, 'UTF-8') ?>'
    <?php if (!empty($dataos3)) : ?>
    data-os3='<?= htmlspecialchars(json_encode($dataos3), ENT_QUOTES, 'UTF-8') ?>'
    <?php endif; ?>
    <?php if (!empty($dataos2os3)) : ?>
    data-os2os3='<?= htmlspecialchars(json_encode($dataos2os3), ENT_QUOTES, 'UTF-8') ?>'
    <?php endif; ?>
    data-url='<?= url("agenda/refresh"); ?>'>
</div>

<form id="form-novaos" action="<?= url("ordens/salvar") ?>">
    <section>
        <?php
        $this->insert("tcsistemas.os/agenda/novaOsCad", [
            "ordens" => $ordens,
            "cliente" => $cliente,
            "operador" => $operador,
            "status" => $status,
            "servico" => $servico,
            "material" => $material,
            "obras" => $obras
        ]);
        ?>
    </section>
</form>

<form id="form-obrasmodal" action="<?= url("obras/salvar") ?>">
    <section>
        <?php
        $this->insert("tcsistemas.os/ordens/ordensModalObras", [
            "obras" => "",
            "cliente" => $cliente
        ]);
        ?>
    </section>
</form>

<section>
    <?php
    $this->insert("tcsistemas.os/ordens/novocliCad", []);
    ?>
</section>
<?= $this->section("js"); ?>