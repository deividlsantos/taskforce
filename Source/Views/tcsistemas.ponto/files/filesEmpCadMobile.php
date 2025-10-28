<body>
    <div class="main">
        <form class="content" id="form-upload" action="<?= url("files/salvar") ?>" method="POST"
            enctype="multipart/form-data">
            <input type="text" id="empresa" name="empresa" value="x" hidden>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile espaco-mobile coluna100">
                    <input type="text" id="descricao" name="descricao" value="" placeholder="Descrição" required>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile coluna100 espaco-mobile">
                    <span id="file-name" class="file-name"></span>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile coluna50 espaco-mobile">
                    <input type="file" id="arquivo-mobile" name="arquivo" accept="image/*,application/pdf" style="display: none;">
                    <button type="button" id="custom-file-upload-mobile" class="custom-file-upload-mobile">
                        <i class="fa fa-file-upload"></i> Selecionar Arquivo
                    </button>
                </div>
                <div class="fcad-form-group-mobile coluna50 espaco-mobile">
                    <button type="submit" class="custom-file-upload-mobile bgcolor-success"><i class="fa fa-upload"></i> Upload</button>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile coluna100 espaco-mobile">
                    <a href="<?= url("files/lista") ?>" class="custom-file-upload-mobile bgcolor-danger"><i class="fa fa-undo"></i> Voltar</a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>