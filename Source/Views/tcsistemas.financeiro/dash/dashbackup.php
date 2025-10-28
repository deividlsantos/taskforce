<?php
$this->layout("_theme", $front);
?>


<div class="info-card-container">
    <a href="#" class="info-card-link">
        <div class="info-card">
            <div class="info-card-header">
                <span>CARD</span>
            </div>
            <div class="info-card-content">
                <i class="info-card-icon fas fa-clock"></i>
                <span class="info-card-text">EXEMPLO</span>
            </div>
        </div>
    </a>
    <a href="<#" class="info-card-link">
        <div class="info-card">
            <div class="info-card-header">
                <span>CARD</span>
            </div>
            <div class="info-card-content">
                <i class="info-card-icon fas fa-folder-open"></i>
                <span class="info-card-text">EXEMPLO</span>
            </div>
        </div>
    </a>
    <a href="#" class="info-card-link">
        <div class="info-card">
            <div class="info-card-header">
                <span>CARD</span>
            </div>
            <div class="info-card-content">
                <i class="info-card-icon fas fa-pen-to-square"></i>
                <span class="info-card-text">EXEMPLO</span>
            </div>
        </div>
    </a>
    <a href="#" class="info-card-link">
        <div class="info-card">
            <div class="info-card-header">
                <span>CARD</span>
            </div>
            <div class="info-card-content">
                <i class="info-card-icon fas fa-cog"></i>
                <span class="info-card-text">EXEMPLO</span>
            </div>
        </div>
    </a>
    <a href="<?= url("logout") ?>" class="info-card-link exit-card">
        <div class="info-card">
            <div class="info-card-header">
                <span>SAIR</span>
            </div>
            <div class="info-card-content">
                <i class="info-card-icon fas fa-sign-out"></i>
                <span class="info-card-text">LOGOUT</span>
            </div>
        </div>
    </a>
</div>


</html>