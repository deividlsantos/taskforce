<?php
$this->layout("_theme", $front);
?>

<body>
    <form class="app_form_ponto ajax_off" id="app_form_ponto" method="POST" data-action="<?= url("ponto/gerar") ?>">
        <!-- <h1 class="titulo-secao">Gerar Registros de Ponto</h1> -->
        <div class="app_form-container">
            <div class="app_mes" id="app_mes_ano" data-action="<?= url("ponto/verificar") ?>">
                <h2>Competência</h2>
                <select name="mes" id="mes">
                    <option data-value="" value="">Selecione o mês</option>
                    <option data-value="<?= ll_encode("01"); ?>" value="01">Janeiro</option>
                    <option data-value="<?= ll_encode("02"); ?>" value="02">Fevereiro</option>
                    <option data-value="<?= ll_encode("03"); ?>" value="03">Março</option>
                    <option data-value="<?= ll_encode("04"); ?>" value="04">Abril</option>
                    <option data-value="<?= ll_encode("05"); ?>" value="05">Maio</option>
                    <option data-value="<?= ll_encode("06"); ?>" value="06">Junho</option>
                    <option data-value="<?= ll_encode("07"); ?>" value="07">Julho</option>
                    <option data-value="<?= ll_encode("08"); ?>" value="08">Agosto</option>
                    <option data-value="<?= ll_encode("09"); ?>" value="09">Setembro</option>
                    <option data-value="<?= ll_encode("10"); ?>" value="10">Outubro</option>
                    <option data-value="<?= ll_encode("11"); ?>" value="11">Novembro</option>
                    <option data-value="<?= ll_encode("12"); ?>" value="12">Dezembro</option>
                </select>

                <select name="ano" id="ano">
                    <option value="">Selecione o ano</option>
                    <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= $currentYear - 10; $year--) {
                        $selected = ($year == $currentYear) ? "selected" : "";
                        echo "<option data-value='" . ll_encode($year) . "' value=\"$year\" $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="app_func_container" style="display: none;">
                <h2>Funcionários</h2>
                <div class="app_func-item">
                    <input type="checkbox" id="select-all">
                    <label for="select-all">Selecionar Todos</label>
                </div>
                <div class="app_func" id="app_func">
                    <?php if (!empty($func)):
                        foreach ($func as $vlrFunc): ?>
                            <div class="app_func-item">
                                <div class="app_func-item-box">
                                    <input type="checkbox" id="box<?= $vlrFunc->id; ?>" name="id_func[]"
                                        value="<?= $vlrFunc->id; ?>">
                                    <label
                                        for="box<?= $vlrFunc->id; ?>"><?= $vlrFunc->nome . " " . $vlrFunc->fantasia; ?></label>
                                    <input type="text" class="date-range" name="date_range_<?= $vlrFunc->id; ?>"
                                        placeholder="Férias dentro do mês (opcional)">
                                    <i class="fa fa-info-circle ferias-info" data-tooltip="Caso se aplique, informe os dias que o colaborador esteve de férias durante o mês selecionado no seguinte formato:<br>'dd-dd'(ex: 01-15)."></i>
                                </div>

                            </div>

                            <?php
                        endforeach;
                    else:
                        ?>
                        <strong>NENHUM COLABORADOR CADASTRADO. CLIQUE <a href="<?= url("ent/colaborador"); ?>">AQUI</a>
                            PARA CADASTRÁ-LOS.</strong>
                        <?php
                    endif; ?>
                </div>
            </div>
        </div>
        <div>
            <button type="submit" class=" btn btn-success">GERAR</button>
            <a href="<?= url("dash"); ?>" class="btn btn-info"><i class="fas fa-undo"></i>Voltar</a>
        </div>
    </form>
    <div id="success-message-container" style="display: none;">
        <p class="success-message titulo-secao">CARTÕES GERADOS COM SUCESSO</p>
        <a id="visualizar-link" class="btn btn-info" data-link="<?= url("ponto/folhas/") ?>" href="">VISUALIZAR</a>
        <a id="voltar-link" class="btn btn-info" href="<?= url("ponto/fechamento"); ?>">VOLTAR</a>
    </div>
</body>

</html>