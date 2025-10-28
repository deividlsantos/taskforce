<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container" style="width:50%;">
        <form class="form-cadastros" id="form-turno" action="<?= url("tipo/salvar") ?>">
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Salvar</button>
                <a href="<?= url("tipo") ?>" class="btn btn-info"><i
                        class="fa fa-undo"></i> Voltar</a>
            </div>
            <section>
                <?php
                $this->insert("tcsistemas.os/tipo/tipoForm", [
                    "front" => $front,
                    "tipo" => $tipo
                ]);
                ?>
            </section>
        </form>
    </div>
</body>

</html>