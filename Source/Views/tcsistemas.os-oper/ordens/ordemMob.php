<div class="modal modal-opermob" data-url="<?= url("oper_ordens/pdf-os"); ?>" id="modalOrdemMob" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close-opermob" data-bs-dismiss="modal" aria-label="Close"><span class="fa-solid fa-left-long"></span></button>
                <span class="title-ordem">Tarefa</span>
                <span><i class="fa-solid fa-ellipsis-vertical"></i></span>
            </div>
            <div class="modal-body ordemopermob scrollable-container">
                <div class="tarefa-section" id="tarefa-section" data-url="<?= url("oper_ordens/activity"); ?>">
                    <div class="fcad-form-row tarefa-item">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section>
    <div class="modal-geral">
        <?php
        $this->insert("ordens/assinatura", []);
        $this->insert("ordens/modalPdf", []);
        $this->insert("ordens/modalObs", []);
        $this->insert("ordens/modalAnexos", []);
        $this->insert("ordens/modalImgView", []);
        $this->insert("ordens/modalPdfEdit", []);
        $this->insert("ordens/modalMedicao", [
            "operador" => $operador,
            "servico" => $servico,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
        ]);
        $this->insert("ordens/modalMedicaoEdit", [
            "operador" => $operador,
            "servico" => $servico,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
        ]);
        $this->insert("ordens/modalAditivo", [
            "operador" => $operador,
            "servico" => $servico
        ]);
        ?>
    </div>
</section>