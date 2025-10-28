<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container d-none d-md-block" style="width: 60%;">
        <form class="form-cadastros" id="form-upload" action="<?= url("files/salvar") ?>" method="POST"
            enctype="multipart/form-data">
            <input type="text" id="funcionario" name="funcionario" value="x" hidden>
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                <a href="<?= url("files/select") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna40">
                    <label for="func">Colaborador <span class="required">*</span></label>
                    <select id="func" name="func">
                        <option value=""></option>
                        <?php if (!empty($func)):
                            foreach ($func as $vlrFunc): ?>
                                <option value="<?= ll_encode($vlrFunc->id); ?>">
                                    <?= $vlrFunc->nome . " " . $vlrFunc->fantasia; ?>
                                </option>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <strong>NENHUM COLABORADOR CADASTRADO. CLIQUE <a
                                    href="<?= url("ent/colaborador"); ?>">AQUI</a>
                                PARA CADASTRÁ-LOS.</strong>
                            <?php
                        endif; ?>
                    </select>
                </div>
                <div class="fcad-form-group coluna40">
                    <label for="categoria">Categoria <span class="required">*</span></label>
                    <select id="categoria" name="categoria">
                        <option value="0">Selecione uma categoria</option>
                        <option value="1">Pessoais/Certificados</option>
                        <option value="2">Trabalhistas</option>
                        <option value="3">Med.Ocupacional</option>
                        <option value="4">Financeiro/Benefícios</option>
                        <option value="5">Diversos</option>
                    </select>
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna50">
                    <label for="descricao">Descrição <span class="required">*</span></label>
                    <input type="text" id="descricao" name="descricao" value="">
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna40">
                    <label for="arquivo">Arquivo <span class="required">*</span></label>
                    <input type="file" id="arquivo" name="arquivo" accept="image/*,application/pdf">
                </div>
            </div>
        </form>
    </div>
</body>

<section class="d-md-none files-mobile">
    <?php
    $this->insert("tcsistemas.ponto/files/filesFuncCadMobile", [
        "func" => $func
    ]);
    ?>
</section>

</html>