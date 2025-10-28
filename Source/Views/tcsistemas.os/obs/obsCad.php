<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container" style="width:50%;">
        <form class="form-cadastros" id="form-turno" action="<?= url("obs/salvar") ?>">
            <div class="fcad-form-row">
                <button class="btn btn-success"><i class="fa fa-check"></i> Salvar</button>
                <a href="<?= url("obs") ?>" class="btn btn-info"><i
                        class="fa fa-undo"></i> Voltar</a>
            </div>
            <section>
                <?php
                $this->insert("tcsistemas.os/obs/obsForm", [
                    "front" => $front,
                    "obs" => $obs
                ]);
                ?>
            </section>
        </form>
    </div>
</body>

</html>