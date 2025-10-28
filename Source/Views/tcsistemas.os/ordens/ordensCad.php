<?php
$this->layout("_theme", $front);
?>
<div class="telas-body">
    <form style="width: 90%;" class="form-cadastros" id="form-ordens"
        data-buscaurl="<?= url("servico/retorna_servicos") ?>"
        data-recorrenciaUrl="<?= url("recorrencias/verifica") ?>" action="<?= url("ordens/salvar") ?>">
        <div class="fcad-form-row buttons-os">
            <button
                id="grava-ordem"
                type="submit"
                class="btn btn-success" <?= $ordens && ($ordens->id_status == 5 || $ordens->id_status == 7) ? "disabled" : ""; ?>>
                <i class="fa fa-check"></i>
                Gravar
            </button>
            <?php
            //* VERIFICAÇÃO TEMPORÁRIA PARA OPERADOR DESKTOP *//
            if ($user->tipo != 3):
            ?>
                <button
                    id="conclui-ordem"
                    data-conclui="<?= $empresa->os_financeiro_auto; ?>"
                    data-url="<?= url("ordens/gerarecorrencia") ?>"
                    data-id="<?= $ordens ? $ordens->id : ""; ?>"
                    data-plcpadrao="<?= !empty($empresa->plconta_padrao) ? $empresa->plconta_padrao : ""; ?>"
                    data-oprpadrao="<?= !empty($empresa->oper_padrao) ? $empresa->oper_padrao : ""; ?>"
                    type="button"
                    class="btn"
                    style="background-color: #8E24AA  ;"
                    <?= (!$ordens || $ordens->id_status == 5 || $ordens->id_status == 7 || $ordens->id_status == 8) ? "hidden" : ""; ?>>
                    <i class="fa fa-check-square"></i>
                    Concluir
                </button>

                <?php
                if ($ordens && $ordens->id_status == 5):
                ?>
                    <button
                        id="estornar-ordem"
                        data-verifica="<?= url("ordens/verifica_rec") ?>"
                        data-url="<?= url("ordens/estorna_os") ?>"
                        data-id="<?= $ordens ? $ordens->id : ""; ?>"
                        type="button"
                        class="btn direita"
                        style="background-color: #a52834  ; color: white;">
                        <i class="fa fa-exchange-alt"></i>
                        Estornar
                    </button>
                <?php
                endif;
                ?>
                <!--button
                    id="duplica-ordem"
                    data-url="<?= url("ordens/duplicar") ?>"
                    data-id="<?= $ordens ? $ordens->id : ""; ?>"
                    type="button"
                    class="btn direita"                    
                    <?= (!$ordens || $ordens->id_status == 5 || $ordens->id_status == 7 || $ordens->id_status == 8) ? "hidden" : ""; ?>>
                    <i class="fa fa-copy"></i>
                    Duplicar
                </button-->
            <?php
            endif;
            ?>
            <a
                href="<?= url("ordens") ?>"
                class="btn btn-info direita">
                <i class="fa fa-undo"></i>
                Voltar
            </a>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.os/ordens/ordensForm", [
                "front" => $front,
                "ordens" => $ordens,
                "cliente" => $cliente,
                "tipo" => $tipo,
                "operador" => $operador,
                "status" => $status,
                "servico" => $servico,
                "material" => $material,
                "obras" => $obras,
                "obs" => $obs,
                "label" => $label,
                "recorrencias" => $recorrencias,
                "user" => $user,
                "empresa" => $empresa
            ]);
            ?>
        </section>
        <div class="fcad-form-row buttons-os">
            <button id="grava-ordem" type="submit" class="btn btn-success" <?= $ordens && ($ordens->id_status == 5 || $ordens->id_status == 7) ? "disabled" : ""; ?>><i class="fa fa-check"></i> Gravar</button>
            <a href="<?= url("ordens") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
        </div>
    </form>
    <section>
        <?php
        $this->insert("tcsistemas.os/ordens/novocliCad", []);
        $this->insert("tcsistemas.os/ordens/ordensModalObrasCad", [
            "obras" => "",
            "cliente" => $cliente,
            "label" => $label
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalMedicaoOs", [
            "operador" => $operador,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
        ]);
        $this->insert("tcsistemas.os/obs/obsModal", [
            "obs" => $obs
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalServicos", [
            "servicos" => $servico
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalOperador", [
            "operador" => $operador
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalEqpMedicao", [
            "equipamentos" => $equipamentos
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalCliente", [
            "cliente" => $cliente,
            "bank" => ""
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalObras", [
            "obras" => $obras,
            "label" => $label
        ]);
        $this->insert("tcsistemas.financeiro/pagar/contasRecForm", [
            "front" => $front,
            "cliente" => $cliente,
            "portador" => $portador,
            "plconta" => $plconta,
            "operacao" => $operacao
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalMateriaisTarefa", [
            "material" => $material
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalEquipamentosTarefa", [
            "equipamentos" => $equipamentos,
            "ordens" => $ordens
        ]);
        $this->insert("tcsistemas.os/ordens/ordensModalStatusTarefa", []);
        $this->insert("tcsistemas.os/ordens/ordensModalChecklist", []);
        $this->insert("tcsistemas.os/ordens/novoSrvCad", [
            "empresa" => $empresa,
            "plconta" => $plconta,
            "recorrencias" => $recorrencias
        ]);
        ?>
    </section>
</div>