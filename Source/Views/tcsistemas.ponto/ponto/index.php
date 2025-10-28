<?php
$this->layout("_theme", $front);
?>

<body>
    <div class="ponto-card-container">
        <a href="<?= url("ponto/fechamento") ?>" class="ponto-card-link">
            <div class="ponto-card">
                <div class="ponto-card-content">
                    <i class="ponto-card-icon fas fa-clock"></i>
                    <span class="ponto-card-text">GERAR</span>
                </div>
            </div>
        </a>
        <a href="<?= url("ponto/folhas") ?>" class="ponto-card-link">
            <div class="ponto-card">
                <div class="ponto-card-content">
                    <i class="ponto-card-icon fas fa-search"></i>
                    <span class="ponto-card-text">VISUALIZAR</span>
                </div>
            </div>
        </a>
        <a href="<?= url("dash") ?>" class="ponto-card-link exit-card">
            <div class="ponto-card">
                <div class="ponto-card-content">
                    <i class="ponto-card-icon fas fa-undo"></i>
                    <span class="ponto-card-text">VOLTAR</span>
                </div>
            </div>
        </a>
    </div>
</body>

</html>