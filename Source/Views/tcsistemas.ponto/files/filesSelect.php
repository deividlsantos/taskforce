<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="func-container settings-container">
        <form class="form-cadastros" id="form-turno" action="<?= url("funcionario/cadastro") ?>">
            <div class="fcad-form-row">
                <div>
                    <a href="<?= url("files/emp");
                    ?>" class="ponto-card-link">
                        <div class="doc-card" style="width: 220px;">
                            <div class="ponto-card-content">
                                <i class="ponto-card-icon fas fa-building"></i>
                                <span class="ponto-card-text">EMPRESA</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div>
                    <a href="<?= url("files/func");
                    ?>" class="ponto-card-link">
                        <div class="doc-card" style="width: 220px;">
                            <div class="ponto-card-content">
                                <i class="ponto-card-icon fas fa-id-card"></i>
                                <span class="ponto-card-text">COLABORADOR</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div>
                    <a href="<?= url("files/lista"); ?>" class="ponto-card-link exit-card">
                        <div class="doc-card">
                            <div class="ponto-card-content">
                                <i class="ponto-card-icon fas fa-undo"></i>
                                <span class="ponto-card-text">VOLTAR</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </form>
    </div>


    
</body>

</html>