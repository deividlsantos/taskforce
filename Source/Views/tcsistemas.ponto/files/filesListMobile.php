<div class="input-filtrar-mobile">
    <div class="fcad-form-row-mobile">
        <div class="fcad-form-group-mobile coluna80">
            <input type="text" id="filtrar-arq-mobile" name="filtrar-arq-mobile" placeholder="Filtrar">
        </div>
        <div class="fcad-form-group-mobile coluna15" style="position: relative; margin-left: 5px;">
            <button class="btn btn-success btn-add-arq-mob"><i class="fa fa-plus"></i></button>
            <div class="tooltip-menu" style="display: none;">
                <a href="<?= url("files/emp") ?>" class="menu-option">
                    <div class="mobile-card">
                        <div class="mobile-card-content">
                            <i class="mobile-card-icon fas fa-building"></i>
                            <span class="mobile-card-text">EMPRESA</span>
                        </div>
                    </div>
                </a>
                <a href="<?= url("files/func") ?>" class="menu-option">
                    <div class="mobile-card">
                        <div class="mobile-card-content">
                            <i class="mobile-card-icon fas fa-id-card"></i>
                            <span class="mobile-card-text">COLABORADOR</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>


<div class="container-arq" id="contentArea" role="main">

    <?php

    use Source\Models\Ent;

    if ($arquivo):
        foreach ($arquivo as $arq):
            if ($arq->tipo == "F"):
                $nome = (new Ent())->findById($arq->id_func)->nome;
            else:
                $nome = $emp->razao;
            endif;

            $avatar = "";
            if ($arq->extensao == "pdf"):
                $icone = "fa fa-file-pdf";
                $avatar = "icone-vermelho";
            else:
                $icone = "fa fa-file-image";
            endif;

            if ($arq->categoria == null):
                $cat = "EMPRESA";
                $dataCat = "empresa";
            elseif ($arq->categoria == "1"):
                $cat = "PESSOAIS";
                $dataCat = "1";
            elseif ($arq->categoria == "2"):
                $cat = "TRABALHISTAS";
                $dataCat = "2";
            elseif ($arq->categoria == "3"):
                $cat = "OCUPACIONAL";
                $dataCat = "3";
            elseif ($arq->categoria == "4"):
                $cat = "BENEFÍCIOS";
                $dataCat = "4";
            else:
                $cat = "DIVERSOS";
                $dataCat = "5";
            endif;

    ?>
            <div class="content-card" role="article" data-cat="<?= $dataCat; ?>">
                <div class="user-item" role="button" tabindex="0">
                    <div class="avatar <?= $avatar; ?>" aria-hidden="true"><i class="<?= $icone; ?>"></i></div>
                    <div class="user-info">
                        <h3><?= $nome; ?></h3>
                        <p><?= $arq->descricao . " • " . $cat ?></p>
                    </div>
                    <i class="fas fa-chevron-down chevron-icone"></i>
                </div>
                <div class="document-panel" aria-hidden="true">
                    <div class="document-content">
                        <a href="<?= FTP_URL . '/tcponto/docs/emp_' . $emp->id . "/" . $arq->nome_arquivo . "." . $arq->extensao; ?>" title="Baixar" target="_blank" class="btn-arq-mobile btn-arq-success"><i class="fa fa-download"></i></a>
                        <a href="#"
                            data-post="<?= url("files/apagar"); ?>" data-action="delete"
                            data-confirm="Tem certeza que deseja deletar esse arquivo?"
                            data-id_arq="<?= ll_encode($arq->id); ?>" title="Deletar" class="btn-arq-mobile btn-arq-danger"><i class="fa fa-trash"></i></a>
                    </div>
                </div>
            </div>
    <?php
        endforeach;
    endif;
    ?>
</div>

<!-- Filtros Mobile -->
<div class="func-container settings-container">
    <nav class="tab-bar" role="navigation">
        <div class="tabs-wrapper" role="tablist">
            <div class="tabs" id="tabs">
                <button class="tab active" data-type="todos" role="tab" aria-selected="true">Todos</button>
                <button class="tab" data-type="empresa" role="tab" aria-selected="false">Empresa</button>
                <button class="tab" data-type="1" role="tab" aria-selected="false">Pessoais</button>
                <button class="tab" data-type="2" role="tab" aria-selected="false">Trabalhistas</button>
                <button class="tab" data-type="3" role="tab" aria-selected="false">Medicina Ocupacional</button>
                <button class="tab" data-type="4" role="tab" aria-selected="false">Financeiro/Benefícios</button>
                <button class="tab" data-type="5" role="tab" aria-selected="false">Diversos</button>
            </div>
        </div>
    </nav>
</div>

</html>