<?php
$this->layout("_theme_oper", $front);
?>

<div class="conteudo-mob noscroll">
    <div class="oprmv_tabs_container">
        <div class="oprmv_tabs_header">
            <button class="oprmv_tab_button oprmv_tab_active" data-tab="tab1">RECEBIDAS</button>
            <button class="oprmv_tab_button" data-tab="tab2">ENVIADAS</button>
        </div>

        <div class="oprmv_tabs_content">
            <div class="oprmv_tab_panel oprmv_tab_active" id="tab1">
                <div class="oprmv_tab_content">
                    <?php
                    if (!empty($solRecebidas)):
                        foreach ($solRecebidas as $sol):
                    ?>
                            <div class="sol-mob-item" data-id="<?= $sol->id; ?>" data-url="<?= url("oper_mov/retorna_solicitacao"); ?>">
                                <div class="sol-barra-status barra-blue">
                                    <i class="fas fa-right-left"></i>
                                </div>
                                <div class="sol-mob-item-content">
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-txt" style="font-style:italic;">CONFIRMAR RECEBIMENTO</span>
                                    </div>
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-stxt"><?= mb_strimwidth($sol->equipamento_desc, 0, 30, '...'); ?></span>
                                    </div>
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-stxt"><b>ORIGEM:</b> <?= $sol->local_origem_desc; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        NENHUMA SOLICITAÇÃO RECEBIDA
                    <?php
                    endif;
                    ?>
                </div>
            </div>

            <div class="oprmv_tab_panel" id="tab2">
                <div class="oprmv_tab_content">
                    <button class="oprmv_nova_solicitacao" id="oprmv-sol">Enviar Ferramenta</button>
                    <?php
                    if (!empty($solEnviadas)):
                        foreach ($solEnviadas as $sol):
                    ?>
                            <div class="sol-mob-item" data-id="<?= $sol->id; ?>" data-url="<?= url("oper_mov/retorna_solicitacao"); ?>">
                                <div class="sol-barra-status barra-orange">
                                    <i class="fas fa-right-left"></i>
                                </div>
                                <div class="sol-mob-item-content">
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-txt" style="font-style:italic;">FERRAMENTA ENVIADA</span>
                                    </div>
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-stxt"><?= mb_strimwidth($sol->equipamento_desc, 0, 30, '...'); ?></span>
                                    </div>
                                    <div class="sol-mob-info fcad-form-row">
                                        <span class="sol-mob-stxt"><b>ORIGEM:</b> <?= $sol->local_origem_desc; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>
                        NENHUMA SOLICITAÇÃO ENVIADA
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.oprmv_tab_button').on('click', function() {
                const targetTab = $(this).data('tab');

                $('.oprmv_tab_button').removeClass('oprmv_tab_active');
                $('.oprmv_tab_panel').removeClass('oprmv_tab_active');

                $(this).addClass('oprmv_tab_active');
                $('#' + targetTab).addClass('oprmv_tab_active');
            });
        });
    </script>
</div>
<section>
    <div class="modal-geral">
        <?php
        $this->insert("mov/modalSolicitacao", []);
        $this->insert("mov/modalMovFerramenta", [
            "user" => $front['user'],
            "equipamentos" => $equipamentos,
            "usuarios" => $usuarios,
            "locais" => $locais,
            "estoque" => $estoque
        ]);
        ?>
    </div>
</section>