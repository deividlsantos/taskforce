<?php
$btnText = "Recebimento";
$adm = false;
if ($user == '1' || $user == '5') {
    $btnText = "Recebimento/Envio";
    $adm = true;
}
?>
<form action="">
    <div class="modal modal-pag2" id="listSolicitacao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="cabecalho-modal">
                        <div class="fcad-form-row">
                            <div class="fcad-form-group coluna100">
                                <h2 class="modal-title fs-2 titulo-pai" id="title-ferramentaGestao">
                                    Solicitações de Equipamentos
                                    <p class="titulo-tarefa-modal" id="nome-ferramenta"></p>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="solicitacoesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="recebidas-tab" data-bs-toggle="tab"
                                data-bs-target="#tab-recebidas" type="button" role="tab"
                                aria-controls="tab-recebidas" aria-selected="true">
                                Solicitações de <?= $btnText; ?>
                            </button>
                        </li>
                        <?php
                        if (!$adm):
                        ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="enviadas-tab" data-bs-toggle="tab"
                                    data-bs-target="#tab-enviadas" type="button" role="tab"
                                    aria-controls="tab-enviadas" aria-selected="false">
                                    Solicitações de Envio
                                </button>
                            </li>
                        <?php
                        endif;
                        ?>
                    </ul>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content mt-3" id="solicitacoesTabContent">

                        <!-- Aba Recebidas -->
                        <div class="tab-pane fade show active" id="tab-recebidas" role="tabpanel"
                            aria-labelledby="recebidas-tab">
                            <div class="tabela-responsive">
                                <table class="tab-list table table-hover table-vendas">
                                    <thead class="cabecalho-tabela-omega">
                                        <tr>
                                            <th>Equip./Ferramenta</th>
                                            <th>Origem</th>
                                            <th>Destino</th>
                                            <th>Qtde</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-recebidas">
                                        <!-- Conteúdo carregado no PHP ou via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Aba Enviadas -->
                        <div class="tab-pane fade" id="tab-enviadas" role="tabpanel"
                            aria-labelledby="enviadas-tab">
                            <div class="tabela-responsive">
                                <table class="tab-list table table-hover table-vendas">
                                    <thead class="cabecalho-tabela-omega">
                                        <tr>
                                            <th>Equip./Ferramenta</th>
                                            <th>Origem</th>
                                            <th>Destino</th>
                                            <th>Qtde</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-enviadas">
                                        <!-- Conteúdo carregado no PHP ou via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<section>
    <?php
    $this->insert("tcsistemas.os/equipamentos/modalConfirmSolicitacao", []);
    ?>
</section>