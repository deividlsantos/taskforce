<body>
    <div class="main">
        <form class="content" id="form-upload" action="<?= url("files/salvar") ?>" method="POST"
            enctype="multipart/form-data">
            <input type="text" id="funcionario" name="funcionario" value="x" hidden>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile espaco-mobile coluna100">
                    <select id="func" name="func">
                        <option value="">COLABORADOR</option>
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
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile espaco-mobile coluna100">                    
                    <select id="categoria" name="categoria">
                        <option value="0">CATEGORIA</option>
                        <option value="1">PESSOAIS/CERTIFICADOS</option>
                        <option value="2">TRABALHISTAS</option>
                        <option value="3">MED.OCUPACIONAL</option>
                        <option value="4">FINANCEIRO/BENEFÍCIOS</option>
                        <option value="5">DIVERSOS</option>
                    </select>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile espaco-mobile coluna100">
                    <input type="text" id="descricao" name="descricao" value="" placeholder="Descrição" required>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile coluna100 espaco-mobile">
                    <span id="file-name-func" class="file-name"></span>
                </div>
            </div>
            <div class="fcad-form-row-mobile margem-mobile">
                <div class="fcad-form-group-mobile coluna50 espaco-mobile">
                    <input type="file" id="arquivo-mobile-func" name="arquivo" accept="image/*,application/pdf" style="display: none;">
                    <button type="button" id="custom-file-upload-mobile-func" class="custom-file-upload-mobile">
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