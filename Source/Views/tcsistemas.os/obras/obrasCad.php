<?php
$this->layout("_theme", $front);
?>

<div class="telas-body">
    <form class="form-cadastros" id="form-obras" action="<?= url("obras/salvar") ?>">
        <div class="fcad-form-row">
            <div class="fcad-form-row form-buttons">
                <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Gravar</button>
                <a href="<?= url("obras") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
        </div>
        <section>
            <?php
            $this->insert("tcsistemas.os/obras/obrasForm", [
                "front" => $front,
                "obras" => $obras,
                "cliente" => $cliente
            ]);
            $this->insert("tcsistemas.financeiro/pagar/contasModalSrch", [
                "fornecedor" => "",
                "cliente" => $cliente,
                "portador" => "",
                "plconta" => "",
                "operacao" => ""
            ]);
            ?>
        </section>
    </form>
</div>