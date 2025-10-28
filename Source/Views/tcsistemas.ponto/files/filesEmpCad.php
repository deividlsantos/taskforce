<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container d-none d-md-block" style="width: 50%;">
        <form class="form-cadastros" id="form-upload" action="<?= url("files/salvar") ?>" method="POST"
            enctype="multipart/form-data">
            <input type="text" id="empresa" name="empresa" value="x" hidden>
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                <a href="<?= url("files/select") ?>" class="btn btn-info direita"><i class="fa fa-undo"></i> Voltar</a>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="descricao">Descrição <span class="required">*</span></label>
                    <input type="text" id="descricao" name="descricao" value="">
                </div>
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="arquivo">Arquivo <span class="required">*</span></label>
                    <input type="file" id="arquivo" name="arquivo" accept="image/*,application/pdf">
                </div>
            </div>
        </form>
    </div>
</body>

<section class="d-md-none files-mobile">
    <?php
    $this->insert("tcsistemas.ponto/files/filesEmpCadMobile", []);
    ?>
</section>

</html>