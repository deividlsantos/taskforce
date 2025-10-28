<?php

use Source\Models\Ent;

$this->layout("_theme", $front);
?>

<section class="tela-arquivos">
    <div class="func-container settings-container  d-none d-md-block">
        <div class="fcad-form-row">
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="todos" title="Todos">
                    <div class="doc-card file-icons active">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-th-list"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="empresa" title="Empresa">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-building"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="1" title="Pessoais">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-id-card"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="2" title="Trabalhistas">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-briefcase"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="3" title="Medicina Ocupacional">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-stethoscope"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="4" title="Financeiro/Benefícios">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <button class="ponto-card-link btn btn-files filter-btn" data-filter="5" title="Diversos">
                    <div class="doc-card file-icons">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-folder-open"></i>
                        </div>
                    </div>
                </button>
            </div>
            <div>
                <a href="<?= url("dash"); ?>" class="ponto-card-link exit-card">
                    <div class="doc-card">
                        <div class="ponto-card-content">
                            <i class="ponto-card-icon fas fa-undo"></i>
                            <span class="ponto-card-text">VOLTAR</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="titulo-selecionado" style="margin-top: 20px;">
            <h4 id="titulo-filtrado" class="titulo-doc" style="color: #a52834;">Todos</h4>
        </div>
    </div>

    <div class="container-fl-review d-none d-md-block">
        <div class="input-filtrar">
            <div class="fcad-form-row">
                <div class="fcad-form-group coluna10">
                    <label style="padding-top: 3%;">Filtrar:</label>
                </div>
                <div class="fcad-form-group coluna30">
                    <input type="text" id="filtrar-arq" name="filtrar-arq" value="">
                </div>
                <div class="fcad-form-group coluna10">
                    <a href="<?= url("files/select"); ?>" class="btn btn-success"><i class="fa fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fl-review">
        <!-- Tabela para Desktop -->
        <div class="tabela-responsive d-none d-md-block">
            <table id="arq-tab" class="tab-list table table-striped table-hover bordered table-vendas">
                <thead>
                    <tr>
                        <th>Colaborador/Emp</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Tipo Arquivo</th>
                        <th style="width:5%;">Visualizar</th>
                        <th style="width:5%;">Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($arquivo): ?>
                        <?php foreach ($arquivo as $arq): ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($arq->tipo == "F"):
                                        foreach ($func as $vlrFunc):
                                            if ($vlrFunc->id == $arq->id_func):
                                                echo $vlrFunc->nome . " " . $vlrFunc->fantasia;
                                            endif;
                                        endforeach;
                                    else:
                                        echo $emp->razao;
                                    endif;
                                    ?>
                                </td>
                                <td><?= $arq->descricao; ?></td>
                                <td data-cat="<?= $arq->categoria; ?>">
                                    <?php
                                    switch ($arq->categoria) {
                                        case 1:
                                            echo "Pessoais";
                                            break;
                                        case 2:
                                            echo "Trabalhista";
                                            break;
                                        case 3:
                                            echo "Medicina Ocupacional";
                                            break;
                                        case 4:
                                            echo "Financeiro / Benefícios";
                                            break;
                                        case 5:
                                            echo "Diversos";
                                            break;
                                        default:
                                            echo "";
                                    }
                                    ?>
                                </td>
                                <td><?= ($arq->extensao == "pdf") ? "pdf" : "imagem"; ?></td>
                                <td class="coluna-acoes">
                                    <a href="<?= FTP_URL . '/tcponto/docs/emp_' . $emp->id . "/" . $arq->nome_arquivo . "." . $arq->extensao; ?>"
                                        class="btn btn-secondary list-edt" target="_blank">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-secondary list-del" href="#"
                                        data-post="<?= url("files/apagar"); ?>" data-action="delete"
                                        data-confirm="Tem certeza que deseja deletar esse arquivo?"
                                        data-id_arq="<?= ll_encode($arq->id); ?>">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">Nenhum resultado encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="d-md-none files-mobile">
    <?php
    $this->insert("tcsistemas.ponto/files/filesListMobile", [
        "arquivo" => $arquivo,
        "emp" => $emp
    ]);
    ?>
</section>

</html>