<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container">
        <form class="form-cadastros" id="form-turno" action="<?= url("faltas/cadastro") ?>">
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Salvar</button>
                <a href="<?= url("faltas") ?>" class="btn btn-info"><i
                        class="fa fa-undo"></i> Voltar</a>
            </div>
            <div class="form-row" hidden>
                <label for="id_faltas">Código</label>
                <input type="text" id="id_faltas" name="id_faltas"
                    value="<?= ($faltas != "") ? ll_encode($faltas->id) : ''; ?>">
            </div>
            <div class="fcad-form-row">
                <div class="fcad-form-group">
                    <label for="descricao">Descrição <span class="required">*</span></label>
                    <input type="text" id="descricao" name="descricao"
                        value="<?= ($faltas != "") ? $faltas->descricao : ''; ?>">
                </div>
            </div>           
        </form>
    </div>
</body>

</html>