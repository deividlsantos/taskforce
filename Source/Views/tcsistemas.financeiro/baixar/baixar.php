<?php
$this->layout("_theme", $front);
?>


<div class="telas-body">
    <form class="form-cadastros" action="<?= url("baixar/salvar") ?>">
        <input type="text" id="" name="" hidden>
        <div class="entidade-form">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna20">
                    <label for="">Data</label>
                    <input type="date" id="" name="dataBx" value="<?= date("Y-m-d"); ?>" required>
                </div>
                <div class="fcad-form-group coluna30">
                    <label for="">Conta Corrente</label>
                    <select name="idportBx" id="" required>
                        <option></option>
                        <?php
                        foreach ($portador as $p):
                        ?>
                            <option value="<?= $p->id; ?>"><?= $p->nome; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="fcad-form-group coluna10 btnBaixar2">
                    <button id="btnBaixar2" class="btn btn-success"><i class="fa fa-check"></i> Baixar</button>
                </div>
                <div class="fcad-form-group coluna10 direita btnBaixar2">
                    <a href="<?= url("contas") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
                </div>
            </div>
        </div>
        <section id="telabaixas-content">
            <?php
            $this->insert("tcsistemas.financeiro/baixar/baixarlist", [
                "front" => $front,
                "baixar" => $baixar,
                "cliente" => $cliente,
                "fornecedor" => $fornecedor,
                "portador" => $portador
            ]);
            ?>
        </section>
        <div id="baixas-success" style="display: none;">
            <div class="fcad-form-row">
                <div class="baixas-message-success coluna50">
                    BAIXA REALIZADA - LOTE #<span id="id-baixas-lote"></span> - <span id="dia-baixas-lote"></span>
                </div>
                <div class="fcad-form-group">
                    <button type="button" class="btn btn-success baixas-lote-rel">VISUALIZAR RELATÓRIO</button>
                </div>
            </div>
        </div>
        <div class="fcad-form-row">
            <div class="painel-baixas fcad-form-row">
                <div class="fcad-form-group coluna10 btnBaixar2">
                    <button id="btnBaixar3" class="btn btn-success"><i class="fa fa-check"></i> Baixar</button>
                </div>
                <div class="fcad-form-group coluna10 direita btnBaixar2">
                    <a href="<?= url("contas") ?>" class="btn btn-info"><i class="fa fa-undo"></i> Voltar</a>
                </div>
            </div>
        </div>
    </form>
</div>