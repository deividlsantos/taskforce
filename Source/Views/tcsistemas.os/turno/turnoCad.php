<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="telas-body">
        <form style="width: 80%;" class="form-cadastros" id="form-turno" action="<?= url("turno/salvar") ?>">
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("turno"); ?>" class="btn btn-info direita"><i
                        class="fa fa-undo"></i> Voltar</a>
            </div>
            <div class="fcad-form-row" hidden>
                <label for="id_turno">Código</label>
                <input type="text" id="id_turno" name="id_turno"
                    value="<?= ($turnos != "") ? ll_encode($turnos->id) : ''; ?>">
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="nome">Nome do turno <span class="required">*</span></label>
                    <input type="text" id="nome" name="nome" value="<?= ($turnos != "") ? $turnos->nome : ''; ?>"
                        required>
                </div>
                <div class="fcad-form-group coluna20">
                    <label for="carga">Carga Horária Semanal <span class="required">*</span></label>
                    <input type="text" id="carga" name="carga" value="<?= ($turnos != "") ? $turnos->carga : ''; ?>"
                        required>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="form-row coluna100">
                    <label for="dias_semana" class="label_dias_semana">Turno <span class="required">*</span></label>
                    <div class="custom-select-wrapper">
                        <div class="custom-select" id="dias_semana"
                            data-selected-days="<?= ($dias == "") ? '' : $dias ?>">
                            <span class="select-placeholder">Selecione os dias...</span>
                            <div class="select-options">
                                <label><input type="checkbox" value="0" hidden> Segunda-feira</label>
                                <label><input type="checkbox" value="1" hidden> Terça-feira</label>
                                <label><input type="checkbox" value="2" hidden> Quarta-feira</label>
                                <label><input type="checkbox" value="3" hidden> Quinta-feira</label>
                                <label><input type="checkbox" value="4" hidden> Sexta-feira</label>
                                <label><input type="checkbox" value="5" hidden> Sábado</label>
                                <label><input type="checkbox" value="6" hidden> Domingo</label>
                                <label><input type="checkbox" value="7" hidden> Segunda à Sexta</label>
                                <label><input type="checkbox" value="8" hidden> Segunda à Sábado</label>
                            </div>
                        </div>
                        <!-- Campo hidden para enviar os dados -->
                        <input type="hidden" name="dia_semana" id="dias_semana_hidden">
                    </div>
                </div>
            </div>
            <div class="fcad-form-group coluna100">
                <label>Horários Especiais <span class="required">#</span></label>
                <div class="fcad-form-row row-dias">
                    <div class="fcad-form-group fdsturno" id="segunda-row">
                        <label for="segunda">Segunda-feira</label>
                        <input type="checkbox" id="segunda" name="segunda" <?= ($turnos != "" && $turnos->segunda == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="terca-row">
                        <label for="terca">Terça-feira</label>
                        <input type="checkbox" id="terca" name="terca" <?= ($turnos != "" && $turnos->terca == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="quarta-row">
                        <label for="quarta">Quarta-feira</label>
                        <input type="checkbox" id="quarta" name="quarta" <?= ($turnos != "" && $turnos->quarta == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="quinta-row">
                        <label for="quinta">Quinta-feira</label>
                        <input type="checkbox" id="quinta" name="quinta" <?= ($turnos != "" && $turnos->quinta == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="sexta-row">
                        <label for="sexta">Sexta-feira</label>
                        <input type="checkbox" id="sexta" name="sexta" <?= ($turnos != "" && $turnos->sexta == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="sabado-row">
                        <label for="sabado">Sábado</label>
                        <input type="checkbox" id="sabado" name="sabado" <?= ($turnos != "" && $turnos->sabado == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                    <div class="fcad-form-group fdsturno" id="domingo-row">
                        <label for="domingo">Domingo</label>
                        <input type="checkbox" id="domingo" name="domingo" <?= ($turnos != "" && $turnos->domingo == 1) ? 'checked' : ''; ?> disabled>
                    </div>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini">Início <span class="required">*</span></label>
                    <input type="time" id="hora_ini" name="hora_ini"
                        value="<?= ($turnos != "") ? substr($turnos->hora_ini, 0, 5) : ''; ?>" required>
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini">Intervalo Início</label>
                    <input type="time" id="intervalo_ini" name="intervalo_ini"
                        value="<?= ($turnos != "") ? substr($turnos->intervalo_ini, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim">Intervalo Fim</label>
                    <input type="time" id="intervalo_fim" name="intervalo_fim"
                        value="<?= ($turnos != "") ? substr($turnos->intervalo_fim, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim">Fim <span class="required">*</span></label>
                    <input type="time" id="hora_fim" name="hora_fim"
                        value="<?= ($turnos != "") ? substr($turnos->hora_fim, 0, 5) : ''; ?>" required>
                </div>
            </div>
            <div class="fcad-form-row" id="monday-inputs" style="display: none;">
                <?php
                $hora_ini_mon = "";
                $hora_fim_mon = "";
                $int_ini_mon = "";
                $int_fim_mon = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 0):
                            $hora_ini_mon = $hora->hora_ini;
                            $hora_fim_mon = $hora->hora_fim;
                            $int_ini_mon = $hora->intervalo_ini;
                            $int_fim_mon = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_mon">Início Segunda-feira <span class="required">*</span></label>
                    <input type="time" id="hora_ini_mon" name="hora_ini_mon"
                        value="<?= $hora_ini_mon != "" ? substr($hora_ini_mon, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_mon">Intervalo Início Segunda-feira</label>
                    <input type="time" id="intervalo_ini_mon" name="intervalo_ini_mon"
                        value="<?= $int_ini_mon != "" ? substr($int_ini_mon, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_mon">Intervalo Fim Segunda-feira</label>
                    <input type="time" id="intervalo_fim_mon" name="intervalo_fim_mon"
                        value="<?= $int_fim_mon != "" ? substr($int_fim_mon, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_mon">Fim Segunda-feira <span class="required">*</span></label>
                    <input type="time" id="hora_fim_mon" name="hora_fim_mon"
                        value="<?= $hora_fim_mon != "" ? substr($hora_fim_mon, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="tuesday-inputs" style="display: none;">
                <?php
                $hora_ini_tue = "";
                $hora_fim_tue = "";
                $int_ini_tue = "";
                $int_fim_tue = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 1):
                            $hora_ini_tue = $hora->hora_ini;
                            $hora_fim_tue = $hora->hora_fim;
                            $int_ini_tue = $hora->intervalo_ini;
                            $int_fim_tue = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_tue">Início Terça-feira <span class="required">*</span></label>
                    <input type="time" id="hora_ini_tue" name="hora_ini_tue"
                        value="<?= $hora_ini_tue != "" ? substr($hora_ini_tue, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_tue">Intervalo Início Terça-feira</label>
                    <input type="time" id="intervalo_ini_tue" name="intervalo_ini_tue"
                        value="<?= $int_ini_tue != "" ? substr($int_ini_tue, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_tue">Intervalo Fim Terça-feira</label>
                    <input type="time" id="intervalo_fim_tue" name="intervalo_fim_tue"
                        value="<?= $int_fim_tue != "" ? substr($int_fim_tue, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_tue">Fim Terça-feira <span class="required">*</span></label>
                    <input type="time" id="hora_fim_tue" name="hora_fim_tue"
                        value="<?= $hora_fim_tue != "" ? substr($hora_fim_tue, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="wednesday-inputs" style="display: none;">
                <?php
                $hora_ini_wed = "";
                $hora_fim_wed = "";
                $int_ini_wed = "";
                $int_fim_wed = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 2):
                            $hora_ini_wed = $hora->hora_ini;
                            $hora_fim_wed = $hora->hora_fim;
                            $int_ini_wed = $hora->intervalo_ini;
                            $int_fim_wed = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_wed">Início Quarta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_ini_wed" name="hora_ini_wed"
                        value="<?= $hora_ini_wed != "" ? substr($hora_ini_wed, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_wed">Intervalo Início Quarta-feira</label>
                    <input type="time" id="intervalo_ini_wed" name="intervalo_ini_wed"
                        value="<?= $int_ini_wed != "" ? substr($int_ini_wed, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_wed">Intervalo Fim Quarta-feira</label>
                    <input type="time" id="intervalo_fim_wed" name="intervalo_fim_wed"
                        value="<?= $int_fim_wed != "" ? substr($int_fim_wed, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_wed">Fim Quarta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_fim_wed" name="hora_fim_wed"
                        value="<?= $hora_fim_wed != "" ? substr($hora_fim_wed, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="thursday-inputs" style="display: none;">
                <?php
                $hora_ini_thu = "";
                $hora_fim_thu = "";
                $int_ini_thu = "";
                $int_fim_thu = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 3):
                            $hora_ini_thu = $hora->hora_ini;
                            $hora_fim_thu = $hora->hora_fim;
                            $int_ini_thu = $hora->intervalo_ini;
                            $int_fim_thu = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_thu">Início Quinta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_ini_thu" name="hora_ini_thu"
                        value="<?= $hora_ini_thu != "" ? substr($hora_ini_thu, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_thu">Intervalo Início Quinta-feira</label>
                    <input type="time" id="intervalo_ini_thu" name="intervalo_ini_thu"
                        value="<?= $int_ini_thu != "" ? substr($int_ini_thu, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_thu">Intervalo Fim Quinta-feira</label>
                    <input type="time" id="intervalo_fim_thu" name="intervalo_fim_thu"
                        value="<?= $int_fim_thu != "" ? substr($int_fim_thu, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_thu">Fim Quinta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_fim_thu" name="hora_fim_thu"
                        value="<?= $hora_fim_thu != "" ? substr($hora_fim_thu, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="friday-inputs" style="display: none;">
                <?php
                $hora_ini_fri = "";
                $hora_fim_fri = "";
                $int_ini_fri = "";
                $int_fim_fri = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 4):
                            $hora_ini_fri = $hora->hora_ini;
                            $hora_fim_fri = $hora->hora_fim;
                            $int_ini_fri = $hora->intervalo_ini;
                            $int_fim_fri = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_fri">Início Sexta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_ini_fri" name="hora_ini_fri"
                        value="<?= $hora_ini_fri != "" ? substr($hora_ini_fri, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_fri">Intervalo Início Sexta-feira</label>
                    <input type="time" id="intervalo_ini_fri" name="intervalo_ini_fri"
                        value="<?= $int_ini_fri != "" ? substr($int_ini_fri, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_fri">Intervalo Fim Sexta-feira</label>
                    <input type="time" id="intervalo_fim_fri" name="intervalo_fim_fri"
                        value="<?= $int_fim_fri != "" ? substr($int_fim_fri, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_fri">Fim Sexta-feira <span class="required">*</span></label>
                    <input type="time" id="hora_fim_fri" name="hora_fim_fri"
                        value="<?= $hora_fim_fri != "" ? substr($hora_fim_fri, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="saturday-inputs" style="display: none;">
                <?php
                $hora_ini_sat = "";
                $hora_fim_sat = "";
                $int_ini_sat = "";
                $int_fim_sat = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 5):
                            $hora_ini_sat = $hora->hora_ini;
                            $hora_fim_sat = $hora->hora_fim;
                            $int_ini_sat = $hora->intervalo_ini;
                            $int_fim_sat = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_sat">Início Sábado <span class="required">*</span></label>
                    <input type="time" id="hora_ini_sat" name="hora_ini_sat"
                        value="<?= $hora_ini_sat != "" ? substr($hora_ini_sat, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_sat">Intervalo Início Sábado</label>
                    <input type="time" id="intervalo_ini_sat" name="intervalo_ini_sat"
                        value="<?= $int_ini_sat != "" ? substr($int_ini_sat, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_sat">Intervalo Fim Sábado</label>
                    <input type="time" id="intervalo_fim_sat" name="intervalo_fim_sat"
                        value="<?= $int_fim_sat != "" ? substr($int_fim_sat, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_sat">Fim Sábado <span class="required">*</span></label>
                    <input type="time" id="hora_fim_sat" name="hora_fim_sat"
                        value="<?= $hora_fim_sat != "" ? substr($hora_fim_sat, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row" id="sunday-inputs" style="display: none;">
                <?php
                $hora_ini_dom = "";
                $hora_fim_dom = "";
                $int_ini_dom = "";
                $int_fim_dom = "";
                if (!empty($horas)):
                    foreach ($horas as $hora):
                        if ($hora->dia_semana == 6):
                            $hora_ini_dom = $hora->hora_ini;
                            $hora_fim_dom = $hora->hora_fim;
                            $int_ini_dom = $hora->intervalo_ini;
                            $int_fim_dom = $hora->intervalo_fim;
                        endif;
                    endforeach;
                endif;
                ?>
                <div class="fcad-form-group hrturno">
                    <label for="hora_ini_sun">Início Domingo <span class="required">*</span></label>
                    <input type="time" id="hora_ini_sun" name="hora_ini_sun"
                        value="<?= $hora_ini_dom != "" ? substr($hora_ini_dom, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_ini_sun">Intervalo Início Domingo</label>
                    <input type="time" id="intervalo_ini_sun" name="intervalo_ini_sun"
                        value="<?= $int_ini_dom != "" ? substr($int_ini_dom, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="intervalo_fim_sun">Intervalo Fim Domingo</label>
                    <input type="time" id="intervalo_fim_sun" name="intervalo_fim_sun"
                        value="<?= $int_fim_dom != "" ? substr($int_fim_dom, 0, 5) : ''; ?>">
                </div>
                <div class="fcad-form-group hrturno">
                    <label for="hora_fim_sun">Fim Domingo <span class="required">*</span></label>
                    <input type="time" id="hora_fim_sun" name="hora_fim_sun"
                        value="<?= $hora_fim_dom != "" ? substr($hora_fim_dom, 0, 5) : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group obsturno">
                    <label for="descricao">Descrição / Obs.</label>
                    <input type="text" id="descricao" name="descricao"
                        value="<?= ($turnos != "") ? $turnos->descricao : ''; ?>">
                </div>
            </div>
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("turno"); ?>" class="btn btn-info direita"><i
                        class="fa fa-undo"></i> Voltar</a>
            </div>
        </form>
    </div>
    <div class="rodape">
        <span class="required">*</span> = Campos Obrigatórios
    </div>
    <div class="rodape">
        <span class="required">#</span> = Marque o dia que terá horários especiais
    </div>
</body>

</html>