<?php
$this->layout("_theme", $front);
?>

<body>
    <form class="folhas_form_ponto ajax_off" id="folhas_form_ponto" method="POST" data-action="<?= url("ponto/filter") ?>">
        <div class="folhas_form-container">
            <h2>Competência</h2>
            <div class="folhas_mes">
                <select name="mes" id="mes-view">
                    <option value="">Selecione o mês</option>
                    <option value="01">Janeiro</option>
                    <option value="02">Fevereiro</option>
                    <option value="03">Março</option>
                    <option value="04">Abril</option>
                    <option value="05">Maio</option>
                    <option value="06">Junho</option>
                    <option value="07">Julho</option>
                    <option value="08">Agosto</option>
                    <option value="09">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
            </div>
            <div class="folhas_mes">
                <select name="ano" id="ano-view">
                    <option value="">Selecione o ano</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        $selected = ($year == $currentYear) ? "selected" : "";
                        echo "<option value=\"$year\" $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <button class="btn btn-success"><i class="fas fa-search"></i></button>
            <a href="<?= url("dash"); ?>" class="btn btn-info"><i class="fas fa-undo"></i>Voltar</a>
        </div>
    </form>
    <section class="result">
        <?php
        $this->insert("tcsistemas.ponto/ponto/pontoFuncList", [
            "result" => $result,
            "func" => $func
        ]);
        ?>
    </section>
</body>

</html>