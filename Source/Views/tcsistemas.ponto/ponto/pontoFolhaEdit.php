<?php

use Source\Models\EntFun;

$this->layout("_theme", $front);
$entfunc = (new EntFun())->findByIdEnt($func->id);
?>

<body>
    <div class="fcad-form-row" style="margin-bottom: 10px;">
        <button id="save-data" data-action="<?= url("ponto/salvar") ?>" class="save-fl-review btn btn-success"><i
                class="fa fa-check"></i>Salvar</button>
        <a href="<?= url("ponto/folhas/" . ll_encode($ponto1->mes) . "/" . ll_encode($ponto1->ano)) ?>"
            class="btn btn-info direita"><i class="fa fa-undo"></i>Voltar</a>
    </div>
    <div class="container-fl-review">
        <div class="employee-info mb-4">
            <div class="row">
                <div id="id_ponto1" hidden><?= $ponto1->id; ?></div>
                <div class="col-md-4">
                    <p><strong>Nome:</strong> <?= $func->nome . " " . $func->fantasia; ?></p>
                    <p><strong>Cargo:</strong> <?= $entfunc->cargo; ?></p>
                    <p><strong>Matrícula:</strong> <?= $entfunc->matricula; ?></p>
                    <p><strong>CTPS:</strong> <?= $entfunc->ctps; ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Competência:</strong>
                        <?= str_pad($ponto1->mes, 2, "0", STR_PAD_LEFT) . "/" . $ponto1->ano; ?></p>
                    <p><strong>Carga Horária(semanal):</strong> <span id=""><?= $turno->carga; ?></span> </p>
                    <p><strong>Horas Trabalhadas/Total:</strong> <span id="total-geral"></span> </p>
                    <p><strong>Horas Extras/Total:</strong> <span id="extras-geral"></span> </p>
                    <p><strong>Banco de Horas/Total:</strong> <span id="banco-geral"></span> </p>
                </div>
                <div class="col-md-4">
                    <p><strong>Horário:</strong>
                        <?= substr($turno->hora_ini, 0, 5) . "-" . substr($turno->intervalo_ini, 0, 5) . "-" . substr($turno->intervalo_fim, 0, 5) . "-" . substr($turno->hora_fim, 0, 5); ?><span
                            class="total-dia" hidden><?= $totalTurno; ?></span></p>
                    <p <?= ($segunda != "") ? "" : "hidden" ?>><strong>Segunda:</strong><span class="seg-txt">
                            <?= ($segunda != "") ? substr($segunda->hora_ini, 0, 5) . "-" . substr($segunda->intervalo_ini, 0, 5) . "-" . substr($segunda->intervalo_fim, 0, 5) . "-" . substr($segunda->hora_fim, 0, 5) : "NORMAL"; ?></span><span
                            class="total-segunda" hidden><?= $segunda != "" ? $totalSeg : "00:00"; ?></span></p>
                    <p <?= ($terca != "") ? "" : "hidden" ?>><strong>Terça:</strong><span class="ter-txt">
                            <?= ($terca != "") ? substr($terca->hora_ini, 0, 5) . "-" . substr($terca->intervalo_ini, 0, 5) . "-" . substr($terca->intervalo_fim, 0, 5) . "-" . substr($terca->hora_fim, 0, 5) : "NORMAL"; ?></span><span
                            class="total-terca" hidden><?= $terca != "" ? $totalTer : "00:00"; ?></span></p>
                    <p <?= ($quarta != "") ? "" : "hidden" ?>><strong>Quarta:</strong><span class="qua-txt">
                            <?= ($quarta != "") ? substr($quarta->hora_ini, 0, 5) . "-" . substr($quarta->intervalo_ini, 0, 5) . "-" . substr($quarta->intervalo_fim, 0, 5) . "-" . substr($quarta->hora_fim, 0, 5) : "NORMAL"; ?></span><span
                            class="total-quarta" hidden><?= $quarta != "" ? $totalQua : "00:00"; ?></span></p>
                    <p <?= ($quinta != "") ? "" : "hidden" ?>><strong>Quinta:</strong><span class="qui-txt">
                            <?= ($quinta != "") ? substr($quinta->hora_ini, 0, 5) . "-" . substr($quinta->intervalo_ini, 0, 5) . "-" . substr($quinta->intervalo_fim, 0, 5) . "-" . substr($quinta->hora_fim, 0, 5) : "NORMAL"; ?></span><span
                            class="total-quinta" hidden><?= $quinta != "" ? $totalQui : "00:00"; ?></span></p>
                    <p <?= ($sexta != "") ? "" : "hidden" ?>><strong>Sexta:</strong><span class="sex-txt">
                            <?= ($sexta != "") ? substr($sexta->hora_ini, 0, 5) . "-" . substr($sexta->intervalo_ini, 0, 5) . "-" . substr($sexta->intervalo_fim, 0, 5) . "-" . substr($sexta->hora_fim, 0, 5) : "NORMAL"; ?></span><span
                            class="total-sexta" hidden><?= $sexta != "" ? $totalSex : "00:00"; ?></span></p>
                    <p><strong>Sábado:</strong>
                        <?= ($sabado != "") ? substr($sabado->hora_ini, 0, 5) . "-" . substr($sabado->intervalo_ini, 0, 5) . "-" . substr($sabado->intervalo_fim, 0, 5) . "-" . substr($sabado->hora_fim, 0, 5) : "FOLGA"; ?><span
                            class="total-sabado" hidden><?= $sabado != "" ? $totalSat : "00:00"; ?></span></p>
                    <p><strong>Domingo:</strong>
                        <?= ($domingo != "") ? substr($domingo->hora_ini, 0, 5) . "-" . substr($domingo->intervalo_ini, 0, 5) . "-" . substr($domingo->intervalo_fim, 0, 5) . "-" . substr($domingo->hora_fim, 0, 5) : "FOLGA"; ?><span
                            class="total-domingo" hidden><?= $domingo != "" ? $totalSun : "00:00"; ?></span></p>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <table
                        class="tab-folha table table-striped table-hover bordered tablesorter specific-table">
                        <thead>
                            <tr>
                                <th hidden>ID</th>
                                <th>Data<span class="required" hidden>*</span></th>
                                <th>Entrada<span class="required">*</span></th>
                                <th>Intervalo<span class="required">*</span></th>
                                <th>Retorno<span class="required">*</span></th>
                                <th>Saída<span class="required">*</span></th>
                                <th>Horas trabalhadas<span class="required" hidden>*</span></th>
                                <th>Banco de Horas<span class="required" hidden>*</span></th>
                                <th>Horas Extras<span class="required">*</span></th>
                                <th>Observações<span class="required" hidden>*</span></th>
                                <th>Editar Livre</th>
                                <th style="width: 3%;">Limpar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ponto2 as $hrDia):
                            ?>
                                <tr data-dia-semana="<?= $hrDia->nome_dia_semana; ?>"
                                    data-id_ponto2="<?= $hrDia->id; ?>">
                                    <td hidden><?= $hrDia->id; ?></td>
                                    <td style="text-align: left;">
                                        <?= str_pad($hrDia->dia, 2, "0", STR_PAD_LEFT) . "/" . str_pad($ponto1->mes, 2, "0", STR_PAD_LEFT) . "/" . $ponto1->ano . " - <span style=''>(" . $hrDia->nome_dia_semana . ")</span>"; ?>
                                    </td>
                                    <td class="editable">
                                        <?= validaHoraPonto(substr($hrDia->hora_ini, 0, 5)) ? substr($hrDia->hora_ini, 0, 5) : $hrDia->hora_ini; ?>
                                    </td>
                                    <td class="editable">
                                        <?= validaHoraPonto(substr($hrDia->intervalo_ini, 0, 5)) ? substr($hrDia->intervalo_ini, 0, 5) : $hrDia->intervalo_ini; ?>
                                    </td>
                                    <td class="editable">
                                        <?= validaHoraPonto(substr($hrDia->intervalo_fim, 0, 5)) ? substr($hrDia->intervalo_fim, 0, 5) : $hrDia->intervalo_fim; ?>
                                    </td>
                                    <td class="editable">
                                        <?= validaHoraPonto(substr($hrDia->hora_fim, 0, 5)) ? substr($hrDia->hora_fim, 0, 5) : $hrDia->hora_fim; ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td class="editable"></td>
                                    <td>
                                        <select name="obs" id="obs">
                                            <option value="0" selected>...</option>
                                            <?php
                                            if (!empty($faltas)):
                                                foreach ($faltas as $chvFt => $vlrFt):
                                                    $temp = '';
                                                    if ($vlrFt->id == $hrDia->obs) {
                                                        $temp = 'selected';
                                                    }
                                                    echo "<option value='" . ll_encode($vlrFt->id) . "' $temp >" . mb_strtoupper($vlrFt->descricao) . "</option>";
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                        <button type="button" class="btn_ponto_copy"><i class="fa fa-copy"></i></button>
                                    </td>
                                    <td><input type="checkbox" class="edit-free" <?= $hrDia->checkbox == 1 ? 'checked' : ''; ?>></td>
                                    <td><button type="button" class="btn_ponto_view zera-linha"><img class="ponto-broom"
                                                src="<?= url("Source/Images/borracha.png"); ?>"><!--span class="fas fa-eraser"--></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="fcad-form-row">
        <button id="save-data2" data-action="<?= url("ponto/salvar") ?>" class="save-fl-review btn btn-success"><i
                class="fa fa-check"></i>Salvar</button>
        <a href="<?= url("ponto/folhas/" . ll_encode($ponto1->mes) . "/" . ll_encode($ponto1->ano)) ?>"
            class="btn btn-info direita"><i class="fa fa-undo"></i>Voltar</a>
    </div>
</body>

</html>